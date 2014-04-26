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
//Census surname data processing
//Olly Butters
//23/8/11
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
//Surname (A-Za-z-)
$unsan_surname = (isset($_REQUEST['surname'])) ? $_REQUEST['surname'] : NULL;
$san_surname = (preg_match("/^[a-zA-Z-]+$/",$unsan_surname)) ? $unsan_surname : NULL;
unset($unsan_surname);
////////////////////////////////////////////////////////////

//Lets make an error message if there isnt a surname
if(is_null($san_surname))
{
   $_SESSION['error'] = "You really need to input a surname for this query to make sense. <a href=\"/query/census_surname\">Go back</a> and try again.";
}


////////////////////////////////////////////////////////////
//Start to build the queries


/////////////////////////////////////////
//Fine grained resolution
$query_select_fine = "cen_parish_1881.easting AS easting,
                      cen_parish_1881.northing AS northing,
                      cen_parish_1881.parish AS name,
                      CONCAT(SUM(cen_people_1881.occurrences),'/',cen_parish_1881.parish_pop,' (',100*SUM(cen_people_1881.occurrences)/cen_parish_1881.parish_pop,'%)') AS percent,
                      '<br/>',
                      CONCAT(1000000*SUM(cen_people_1881.occurrences)/cen_parish_1881.area,' per square km') AS density";

$query_table_fine = " FROM cen_people_1881 
                       LEFT JOIN cen_parish_1881 ON cen_people_1881.par_link=cen_parish_1881.par_link 
                      WHERE cen_parish_1881.easting IS NOT NULL AND cen_parish_1881.northing IS NOT NULL";


/////////////////////////////////////////
//Summary resolution
$scale_count_unit = "%";
$query_select_summary = "cen_parish_1881.easting AS easting,
                         cen_parish_1881.northing AS northing,
                         gref_county.county AS name,
                         cen_parish_1881.pre74countycode AS pre74cc,
                         100*SUM(cen_people_1881.occurrences)/SUM(cen_parish_1881.parish_pop) AS count,
                         CONCAT(SUM(cen_people_1881.occurrences),'/',SUM(cen_parish_1881.parish_pop),' (',100*SUM(cen_people_1881.occurrences)/SUM(cen_parish_1881.parish_pop),'%)') AS percent,
                         CONCAT(1000000*SUM(cen_people_1881.occurrences)/SUM(cen_parish_1881.area),' per square km') AS density";

$query_table_summary = " FROM cen_people_1881 
                           LEFT JOIN cen_parish_1881 ON cen_people_1881.par_link=cen_parish_1881.par_link
                           LEFT JOIN gref_county ON cen_parish_1881.pre74countycode=gref_county.countycode
                         WHERE cen_parish_1881.easting IS NOT NULL";
/////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////
$query="";

/////////////////////////////////////////
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
	//Get the long version of this county
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
if($san_surname!="")
{
    $query_summary['Surname'] = $san_surname;
    $query .= " AND surname='".mysql_real_escape_string($san_surname)."'";
}


$fine_query = "SELECT ".$query_select_fine." ".$query_table_fine." ".$query." GROUP BY cen_people_1881.par_link LIMIT 10000";
$summary_query = "SELECT ".$query_select_summary." ".$query_table_summary." ".$query." GROUP BY countycode";

//Get the coordinate data
$result = mysql_query($fine_query);

//See how many rows we got
$number_of_results = mysql_num_rows($result);

//Store the web query as a session variable
$_SESSION['fine_query'] = $fine_query;
$_SESSION['summary_query'] = $summary_query;
$_SESSION['number_of_results'] = $number_of_results;
?>
