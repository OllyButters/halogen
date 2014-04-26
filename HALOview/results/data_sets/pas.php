<?php 
//////////////////////////////////////////////////////////////////////
// HALO-view; a web-based geospatial visualisation tool.
// Written by Olly Butters (<http://www.faji.co.uk>).
// Copyright (C) 2011 University of Leicester (<http://www.le.ac.uk/legal>)
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//////////////////////////////////////////////////////////////////////

?>
<?php
///////////////////////////////////////////////////
//PAS data processing
//Olly Butters 
//6/9/2011
///////////////////////////////////////////////////

/////////////////////////////////////////
//County code
$unsan_county_code = (isset($_REQUEST['county'])) ? $_REQUEST['county'] : NULL;
foreach($unsan_county_code as $this_place)
{
    if(ctype_alpha($this_place)){$san_county_code[] = $this_place;}
}
unset($unsan_county_code);

//Some special users may select *all counties* as well as other places,
//so lets strip those out if they are there.
if(count($san_county_code)>1)
{
    if(array_search('all',$san_county_code) !== FALSE)
    {
        unset($san_county_code[array_search('all',$san_county_code)]);
    }
}

///////////////////////////////////////////////////
//Broadperiod
$unsan_broadperiod = (isset($_REQUEST['broadperiod'])) ? $_REQUEST['broadperiod'] : NULL;
$san_broadperiod = (preg_match("/^[a-zA-Z ]+$/",$unsan_broadperiod)) ? $unsan_broadperiod : NULL; 
unset($unsan_broadperiod);



////////////////////////////////////////////////////////////
//Start to build the queries

//////////////////////////////////////////////////
//Fine
$query_select_fine = "easting,
                      northing,
                      '' AS name,
                      CONCAT('<a href=\"data_sets/pas_more_info.php?easting=',easting,'&northing=',northing,'\">More info about this area</a>','<br/>'),
                      GROUP_CONCAT(a, ' ',b ORDER BY b DESC SEPARATOR '<br/>') AS data,
                      '<br/><br/><i>N.B. In order to preserve confidentially the actual location of objects are within a 1km square of this marker location.</i>'";


$query_table_fine = " FROM 
                       (
                         SELECT 
                          pas_findspots.easting AS easting, 
                          pas_findspots.northing AS northing,
                          CONCAT(broadperiod, ' ', objecttype) AS a,
                          COUNT(*) AS b
                         FROM 
                          pas_findspots
                         LEFT JOIN pas_finds ON pas_findspots.findid = pas_finds.secuid
                         WHERE 
                          easting IS NOT NULL 
                          AND
                          northing IS NOT NULL";


$query_group_fine =   " GROUP BY easting, northing, broadperiod, objecttype
                        ) AS tab
                        GROUP BY easting, northing";


//////////////////////////////////////////////////
//Summary
$scale_count_unit = "Total finds";

$query_select_summary = "pas_findspots.easting AS easting,
                         pas_findspots.northing AS northing,
                         pas_findspots.findid AS name,
                         pas_findspots.pre74countycode AS pre74cc,
                         COUNT(*) as count";

$query_table_summary = " FROM 
                          pas_findspots
                         LEFT JOIN pas_finds ON pas_findspots.findid=pas_finds.secuid 
                         WHERE 
                          easting IS NOT NULL 
                          AND 
                          northing IS NOT NULL";
/////////////////////////////////////////////////////////////



$query="";
 
////////////////////////////////////////
//Deal with counties
if(array_key_exists('0',$san_county_code) && $san_county_code[0] == "all")
{
    $query_summary['County']="All counties";
}
else
{
    $places;

    //Cycle through each one
    foreach($san_county_code as $this_place)
    {
        $places[] = mysql_real_escape_string($this_place);
    }

    //Get rid of any duplicates.
    $places = array_unique($places);

    //Sort out the summary data
    foreach($places as $key => $value)
    {
        //Get the long version of this language
        $query_temp = "SELECT county FROM gref_county WHERE countycode = '".$value."'";
	$temp_result = mysql_query($query_temp);
	$temp_row = mysql_fetch_row($temp_result);
	$query_summary_temp[] = $temp_row[0];
    }

    $query_summary['County'] = implode(", ",$query_summary_temp);
   
    $query .= " AND pre74countycode IN ('";
    $query .= implode("','",$san_county_code);
    $query .= "')";
}


if($san_broadperiod != "all")
{
    $query .= " AND broadperiod='$san_broadperiod'";
    $query_summary['Broad period']=$san_broadperiod;
}
else
{
    $query_summary['Broad period']="All periods";
}

$fine_query    = "SELECT ".$query_select_fine." ".$query_table_fine.$query.$query_group_fine;//." GROUP BY easting,northing,broadperiod,objecttype";
$summary_query = "SELECT ".$query_select_summary." ".$query_table_summary.$query." GROUP BY pre74countycode";


//Get the coordinate data
$result = mysql_query($fine_query);

//See how many rows we got
$number_of_results = mysql_num_rows($result);

//Store the web query as a session variable
$_SESSION['fine_query'] = $fine_query;
$_SESSION['summary_query'] = $summary_query;
$_SESSION['number_of_results'] = $number_of_results;
?>
