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
//Census parish data processing
//Olly Butters 
//22/9/2011
//Tidied and sanitized (23/8/2011) 
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


/////////////////////////////////////////
//Parish name (A-Za-z- )
$unsan_parish = (isset($_REQUEST['parish'])) ? $_REQUEST['parish'] : NULL;
$san_parish = (preg_match("/^[a-zA-Z-\s]+$/",$unsan_parish)) ? $unsan_parish : NULL;
unset($unsan_parish);
////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////
//Start to build the queries

//////////////////////////////////////////////////
//Fine
$query_select_fine = "cen_parish_1881.easting AS easting,
                      cen_parish_1881.northing AS northing,
                      cen_parish_1881.parish AS name,
                      CONCAT(cen_parish_1881.parish_pop, ' people') AS people,
                      '<br/>',
                      CONCAT(cen_parish_1881.num_surnames,' different surnames') AS surnames,
                      '<br/>',
                      CONCAT(ROUND(cen_parish_1881.area/1000000,0), ' square km') AS area,
                      '<br/>', 
                      CONCAT(ROUND(1000000*cen_parish_1881.parish_pop/cen_parish_1881.area,1),' people per square km') AS people_density,
                      '<br/>',
                      CONCAT(ROUND(1000000*cen_parish_1881.num_surnames/cen_parish_1881.area,1),' different surnames per square km') AS surname_density,
                      '<br/>',
                      CONCAT(ROUND(100*cen_parish_1881.num_surnames/cen_parish_1881.parish_pop,1), ' unique surnames per 100 people') AS unique_surname_density";


$query_table_fine = " FROM cen_parish_1881 
                      WHERE easting IS NOT NULL AND northing IS NOT NULL";

//////////////////////////////////////////////////
//Summary
$scale_count_unit = "Number per square km";
$query_select_summary = "cen_parish_1881.easting AS easting,
                         cen_parish_1881.northing AS northing,
                         cen_parish_1881.parish AS name,
                         cen_parish_1881.pre74countycode AS pre74cc,
                         1000000*cen_parish_1881.parish_pop/cen_parish_1881.area AS count";


$query_table_summary = " FROM cen_parish_1881 
                         WHERE easting IS NOT NULL AND northing IS NOT NULL";
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


/////////////////////////////////////////
//Deal with surname
if($san_parish!="")
{
    $query_summary['Parish'] = $san_parish;
    $query .= " AND parish='".mysql_real_escape_string($san_parish)."'";
}


$fine_query    = "SELECT ".$query_select_fine." ".$query_table_fine.$query;
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
