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
//////////////////////////////////////////////////
//Genetics data processing
//Olly Butters 
//23/8/2011
//Tidied and sanitized (23/8/2011)
//////////////////////////////////////////////////


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

////////////////////////////////////////
//Haplogroups can be a-zA-Z0-9()*
$unsan_nowhg = (isset($_REQUEST['nowhg'])) ? $_REQUEST['nowhg'] : NULL;
$san_nowhg = (preg_match("/^[a-zA-Z0-9()*]+$/",$unsan_nowhg)) ? $unsan_nowhg : NULL;
unset($unsan_nowhg);

////////////////////////////////////////
//Data set source - Capelli or Jobling
$unsan_gen_data_source = (isset($_REQUEST['gen_data_source'])) ? $_REQUEST['gen_data_source'] : NULL;
$san_gen_data_source = ($unsan_gen_data_source=='all' || $unsan_gen_data_source=='C' || $unsan_gen_data_source=='J') ? $unsan_gen_data_source : NULL;
unset($unsan_gen_data_source);


/////////////////////////////////////////////////////////////
//This entire dataset is less than the maximum that can be plotted, so we dont
//need to worry about summary data etc.
$query_select_fine = "easting,
                      northing, 
                      loc AS name,
                      pre74countycode AS pre74cc,
                      1 AS count,
                      GROUP_CONCAT(DISTINCT summ.nh ORDER BY summ.nh SEPARATOR '<br/>')";

$query_table_fine = "FROM 
                      (SELECT 
                        easting, 
                        northing, 
                        CONCAT(IFNULL(CONCAT(villagetown,' '),''), IFNULL(county,'')) AS loc,
                        CONCAT(nowhg, ' ',COUNT(*)) AS nh,
                        data_source,
                        nowhg,
                        pre74countycode
                       FROM gen_ancestry 
                       GROUP BY nowhg, easting, northing) 
                      AS summ
                     WHERE easting IS NOT NULL AND northing IS NOT NULL";

//                     GROUP BY easting,northing";
/////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////
//Process the inputs
$query = "";

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

////////////////////////////////////////
//Deal with nowhg
if($san_nowhg!="all")
{
    $query_summary["Haplogroup"] = $san_nowhg;
    $query .= " AND nowhg='".mysql_real_escape_string($san_nowhg)."'";
}    
else
{
    $query_summary["Haplogroup"] = "All haplogroups";
}

////////////////////////////////////////
//Deal with source data
$dataset['J'] = "Jobling";
$dataset['C'] = "Capelli";
if($san_gen_data_source!="all")
{
    $query_summary["Dataset"] = $dataset[$san_gen_data_source];
    $query .= " AND data_source='".mysql_real_escape_string($san_gen_data_source)."'";
}
else
{
    $query_summary["Dataset"] = "Jobling and Capelli";
}



$query .= " GROUP BY easting,northing";


$fine_query = "SELECT ".$query_select_fine." ".$query_table_fine." ".$query;

//Get the coordinate data
$result = mysql_query($fine_query);

//Store the web query as a session variable
$_SESSION['fine_query'] = $fine_query;
$_SESSION['summary_query'] = $fine_query;
$_SESSION['number_of_results'] = mysql_num_rows($result);
?>