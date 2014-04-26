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
/////////////////////////////////////////////////////////////
//KEPN - process the form input
//Olly Butters 
//14/10/2011
//Tidied up and sanitized (23/8/2011)
/////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////
//Get the rest of the kepn specific args and sanitize them
/////////////////////////////////////////////////////////////

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

//Language. This can be one or many alpha strings
$unsan_lang = (isset($_REQUEST['lang'])) ? $_REQUEST['lang'] : NULL;
if(!is_null($unsan_lang))
{
  foreach($unsan_lang as $this_lang)
  {
    if(ctype_alpha($this_lang)){$san_lang[] = $this_lang;}
  }
}
unset($unsan_lang);

//If someone select all and other languages then delete all.
if(count($san_lang)>1)
{
  if(array_search('all',$san_lang) !== FALSE)
  {
    unset($san_lang[array_search('all',$san_lang)]);
  }
}

//hword - select from drop down list. Only one of these options.
//$unsan_hword_list = (isset($_REQUEST['hword_list'])) ? $_REQUEST['hword_list'] : NULL;
//$san_hword_list = (preg_match("/^[a-zA-Z-]+$/",$unsan_hword_list)) ? $unsan_hword_list : NULL;
//unset($unsan_hword_list);

$unsan_hword_list = (isset($_REQUEST['hword_list'])) ? $_REQUEST['hword_list'] : NULL;
if(!is_null($unsan_hword_list))
{
  foreach($unsan_hword_list as $this_hword)
  {
    if(preg_match("/^[a-zA-Z-]+$/",$this_hword))
    {
      $san_hword_list[] = $this_hword;
    }
  }
}
unset($unsan_hword_list);

//If someone select all and other elements then delete all.
if(count($san_hword_list)>1)
{
  if(array_search('all',$san_hword_list) !== FALSE)
  {
    unset($san_hword_list[array_search('all',$san_hword_list)]);
  }
}


//Just one placename, maybe with SQL wildcards
$unsan_placename = (isset($_REQUEST['placename'])) ? $_REQUEST['placename'] : NULL;
$san_placename = (preg_match("/^[a-zA-Z-%_\' ]+$/",$unsan_placename)) ? $unsan_placename : NULL;
unset($unsan_placename);

//Just one option from list
$unsan_placename_match_type = (isset($_REQUEST['placename_match_type'])) ? $_REQUEST['placename_match_type'] : NULL;
$san_placename_match_type = (preg_match("/^[a-zA-Z_]+$/",$unsan_placename_match_type)) ? $unsan_placename_match_type : NULL;
unset($unsan_placename_match_type);

////////////////////////////////////////////////


/////////////////////////////////////////////////////////////
//Define the main parts of the fine and summary queries
/////////////////////////////////////////////////////////////


////////////////////////////////////////////////
//FINE query
//The GROUP_CONCAT will CONCAT all the language columns together when a single placename has multiple languages.
////////////////////////////////////////////////
$query_select_fine = "kepn_place.easting AS easting,
                      kepn_place.northing AS northing,
                      kepn_place.placename AS name,
                      kepn_place.etymology AS etymology,
                      '<br/><br/><strong>Derivation of elements</strong><br/>',
                      GROUP_CONCAT(DISTINCT '(',kev.position,') ',' <strong>',kev.hword,'</strong> ', kev.uncertain, kepn_language.description,' - <em>',kes.note,'</em>' ORDER BY kev.position SEPARATOR '<br/>') AS langs,
                      '<br/><br/><strong>References</strong> (<a href=\"../kepn/kepn_reference/\">What do these mean?</a>)<br/>',
                      GROUP_CONCAT(DISTINCT kref.short_title,' ',kref.page ORDER BY kref.short_title SEPARATOR '<br/>') AS refs";

//Define how any tables are JOINed.
$query_table_fine = "FROM kepn_place
                      LEFT JOIN kepn_elementv ON kepn_place.placeno=kepn_elementv.placeno
                      RIGHT JOIN kepn_elementv AS kev ON kepn_place.placeno=kev.placeno
                      LEFT JOIN kepn_elements AS kes ON kev.hword=kes.hword AND kev.hversion=kes.hversion AND kev.langcode=kes.langcode
                      LEFT JOIN kepn_reference AS kref ON kepn_place.placeno=kref.placeno
                      LEFT JOIN kepn_language ON kev.langcode=kepn_language.code
                     WHERE 1";
//                     WHERE kepn_place.easting IS NOT NULL AND kepn_place.northing IS NOT NULL";
////////////////////////////////////////////////



////////////////////////////////////////////////
//SUMMARY query
////////////////////////////////////////////////
$scale_count_unit = "Number";
$query_select_summary = "kepn_place.easting AS easting,
                         kepn_place.northing AS northing,
                         gref_county.county AS name,
                         kepn_place.pre74countycode AS pre74cc,
                         COUNT(DISTINCT kepn_place.placename,kepn_place.easting,kepn_place.northing) AS count,
                         CONCAT(COUNT(DISTINCT kepn_place.placename,kepn_place.easting,kepn_place.northing),' places found') AS text";

$query_table_summary = "FROM kepn_place
                          LEFT JOIN kepn_elementv ON kepn_place.placeno=kepn_elementv.placeno
                          LEFT JOIN kepn_language ON kepn_elementv.langcode=kepn_language.code
                          LEFT JOIN gref_county ON kepn_place.pre74countycode=gref_county.countycode
                        WHERE 1";

//Took out to optimize                        //WHERE kepn_place.easting IS NOT NULL AND kepn_place.northing IS NOT NULL";
////////////////////////////////////////////////


////////////////////////////////////////////////////////////
//Collect all the query items together
////////////////////////////////////////////////////////////
$query = "";

/////////////////////////////////////////
//Deal with the spatial part of the query

//See how many county code entries we have.
$number_of_regions=count($san_county_code);

if(array_key_exists('0',$san_county_code) && $san_county_code[0] == "all")
{
    //Want all counties - so dont need to do anything with the query
    $query_summary['County']="All counties";
}
else
{
    //Must be a (or more) SPECIFIC county specified then.
    $places;

    //Cycle through each one, escaping at the same time (just to be doubley sure) 
    foreach($san_county_code as $this_place)
    {
	$places[] = mysql_real_escape_string($this_place);
    }
    
    //Get rid of any duplicates.
    $places = array_unique($places);

    //Sort out the summary data array
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
    $query .= implode("','",$places);
    $query .= "')";
}   

/////////////////////////////////////////
//Deal with language part of query

$langs;
foreach($san_lang as $this_lang)
{
    if($this_lang == "all")
    {
        //Do nothing
        $query_summary['Language']="All languages";
    }
    else
    {
        //Few special cases where the laguages are aggregated 
	if($this_lang == "AGGOE")
	  {
	    //Old English
	    $langs[] = "O";    //Old English
	    $langs[] = "OA";   //Anglian
	    $langs[] = "OAE";  //East Anglian
	    $langs[] = "OAM";  //Mercian
	    $langs[] = "OAN";  //Northumbrian
	    $langs[] = "OE";   //East Saxon
	    $langs[] = "OK";   //Kentish
	    $langs[] = "OW";   //West Saxon
	    $langs[] = "OWE";  //Early West Saxon
	    $langs[] = "OWL";  //Late West Saxon
	  }
	elseif($this_lang == "AGGON")
	  {
	    //Old Norse
	    $langs[] = "N";    //Old Norse
	    $langs[] = "ND";   //Old Danish
	    $langs[] = "NE";   //Old East Scandinavian
	    $langs[] = "NN";   //Old Norwegian
	    $langs[] = "NW";   //Old West Scandinavian
	  }
	elseif($this_lang == "AGGCE")
	  {
	    //Celtic
	    $langs[] = "C";    //Celtic
	    $langs[] = "CB";   //British
	    $langs[] = "CBA";  //Primitive Welsh
	    $langs[] = "CBB";  //Primitive Cornish
	    $langs[] = "CBC";  //Primitive Cumbrian
	    $langs[] = "CBD";  //Old Welsh
	    $langs[] = "CBE";  //Old Cornish
	    $langs[] = "CBF";  //Welsh
	    $langs[] = "CBG";  //Cornish
	    $langs[] = "CBH";  //Cumbrian
	    $langs[] = "CBI";  //Middle Cornish
	    $langs[] = "CBR";  //Romano-British
	    $langs[] = "CG";   //Goidelic
	    $langs[] = "CGA";  //Manx
	    $langs[] = "CGI";  //Irish
	    $langs[] = "CGM";  //Middle Irish
	    $langs[] = "CGO";  //Old Irish
	    $langs[] = "CGS";  //Scots Gaelic
	  }
	elseif($this_lang == "AGGFR")
	  {
	    //French
	    $langs[] = "F";    //French
	    $langs[] = "FN";   //Old Northern French
	  }
	else
	  {
	    $langs[] = mysql_real_escape_string($this_lang);
	  }
    }
}

if(isset($langs) && count($langs)>0)
{
    //Get rid of any duplicates.
    $langs = array_unique($langs);

    //Sort out the summary data
    unset($query_summary_temp);
    foreach($langs as $key => $value)
    {
        //Get the long version of this language
        $query_temp = "SELECT description FROM kepn_language WHERE code = '".$value."'";
	$temp_result = mysql_query($query_temp);
	$temp_row = mysql_fetch_row($temp_result);
	$query_summary_temp[] = $temp_row[0];
    }
      
    $query_summary['Language'] = implode(", ",$query_summary_temp);

    $query .= " AND kepn_elementv.langcode IN ('";
    $query .= implode("','",$langs);
    $query .= "')";
}


////////////////////////////////////////////
//Add the hword requirement if it is set
$hword;


if(isset($san_hword_list) && count($san_hword_list)>0)
{
  if(count($san_hword_list)==1 && $san_hword_list[0]=="all")
  {
      $query_summary['Elements'] = "All elements";
  }
  else
  {
      $query_summary['Elements'] = implode(", ",$san_hword_list);

      $query .= " AND kepn_elementv.hword IN ('";
      $query .= implode("','",$san_hword_list);
      $query .= "')";
  }
}
//From the list - there can be only one!
//if($san_hword_list!="all")
//{
//   $hwords[] = mysql_real_escape_string($san_hword_list);
//}
//else
//{
//   $query_summary['Element']="All elements";
//}

//From the text box - could be many
//if($san_hword_user!="")
//{
    //These can be comma separated, so lets split them up
//    $hwords = array_map('trim',explode(",",$san_hword_user));
//}
//else
//{
//   $query_summary['Element']="All elements";
//}

//Add it all in if any are set
//if(isset($hwords) && count($hwords)>0)
//{
    //Get rid of any duplicates.
//    $hwords = array_unique($hwords);
//    $hwords_san = array_map('mysql_real_escape_string',$hwords);

//    $query_summary['Elements'] = implode(", ",$hwords_san);
    
//    $query .= " AND kepn_elementv.hword IN ('";
//    $query .= implode("','",$hwords_san);
//    $query .= "')";
//}


////////////////////////////////////////
//Deal with placename inputs
if($san_placename!="" || ($san_placename=="" && $san_placename_match_type=="persn_begin"))
{
    $san_placename = mysql_real_escape_string($san_placename);
    
    if($san_placename_match_type=="exact")
    {
        $query .= " AND kepn_place.placename='".$san_placename."'"; 
	$query_summary['Place name'] = "Equals '".$san_placename."'";
    }
    elseif($san_placename_match_type=="begin")
    {
	$query .= " AND kepn_place.placename LIKE '".$san_placename."%'";
	$query_summary['Place name'] = "Begins with '".$san_placename."'";
    }
    elseif($san_placename_match_type=="end")
    {
	$query .= " AND kepn_place.placename LIKE '%".$san_placename."'";
	$query_summary['Place name'] = "Ends with '".$san_placename."'";
    }
    elseif($san_placename_match_type=="contains")
    {
	$query .= " AND kepn_place.placename LIKE '%".$san_placename."%'";
	$query_summary['Place name'] = "Contains '".$san_placename."'";
    }
    elseif($san_placename_match_type=="persn_begin")
    {
	$query .= " AND kepn_place.placename LIKE '".$san_placename."%' AND kepn_elementv.hword='pers.n.' AND kepn_elementv.position=1";
	$query_summary['Place name'] = "Begins with personal name '".$san_placename."'";
    }
    elseif($san_placename_match_type=="sounds_like")
    {
	$query .= " AND kepn_place.placename SOUNDS LIKE '".$san_placename."'";
	$query_summary['Place name'] = "Sounds like '".$san_placename."'";
    }
    elseif($san_placename_match_type=="pattern")
    {
	$query .= " AND kepn_place.placename LIKE '".$san_placename."'";
	$query_summary['Place name'] = "Matches pattern '".$san_placename."'";
    }
    else
    {
        //ERROR!
        $query = "";
	echo "An error has occurred! Go back to the <a href=\"../index.php\">main page</a> and try again.";
	exit;
    }
}

//Store the queries as a session variables
$_SESSION['fine_query'] = "SELECT ".$query_select_fine." ".$query_table_fine." ".$query." GROUP BY name, easting, northing"; 
$_SESSION['summary_query'] = "SELECT ".$query_select_summary." ".$query_table_summary." ".$query." GROUP BY pre74countycode";

//Do the fine query
$result = mysql_query($_SESSION['fine_query']);

//See how many rows we got
$_SESSION['number_of_results'] = mysql_num_rows($result); 

?>