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
//Build the kml files. What is in them depends on how much data
//was returned in the query. It is not much (less than 1000 say)
//then the summary will have ALL the FINE data in it. If it is
//more then it will have a summary for each county THAT CONTAINS 
//DATA. AND a set of finer detailed kml files will be made -
//one for each degree of latitude. These get concatenated on the 
//fly by the web interface when the user moves the map.

//GET the session ID from the URL and make this session that one
//This needs to be done like this as it is JS that calls this
//and sessions dont seem to be passed automatically.
//Once we have the query we want to destroy this session
//so it doesnt leak into future calls.

//The data from the DB is in UTF8 so it needs to be decoded.

//For both the summary query and the fine query the expected
//queries will have a name, easting, northing the rest
//of the columns get concatenated into the description
//part of the kml file.

//Returns the random name of the kml file - this serves as the root
//for all calls to the kml files.

//I had real issues with the encoding of the XML files.
//The content of the description has a lot of HTML in
//it (generated from the DB query) which is parsed
//by google maps. Piping UTF-8 into it just seemed to
//bork it, so I unencode the UTF-8 from the DB and
//put it in CDATA tags.

//Olly Butters
//20-11-2011
///////////////////////////////////////////////////

$SID = $_GET['SID'];
if(!ctype_alnum($SID))
{
  exit(1);
}
session_id($SID);
session_start();
$summary_query = $_SESSION['summary_query'];
$fine_query = $_SESSION['fine_query'];
$number_of_results = $_SESSION['number_of_results'];
//session_destroy();


//If theres not a valid SID or session vars need to stop!
//
//
//
//


require("phpcoord-2.3.php");  //this is the modified version!
require("../common/db_connect.php");

//Define a random file name in the public part to write this data to.
//The summary will have an over view and the rest will have smaller 
//chunks of the whole lot
$temp_file_name = rand();
$summary_file_path = "../cache/".$temp_file_name."_summary.kml";


//Only do the summary query if there is a lot of results to show
if($number_of_results>1000)
{
  //////////////////////////////////////////////////
  //Do the summary query first then decide if we want to do more later.
  //////////////////////////////////////////////////
  $query = $summary_query;
  $result = mysql_query($query);
  if(!$result) 
    {
      die('Invalid summary query: ' . mysql_error());
    }
  
  $number_of_rows = mysql_num_rows($result);
  
  //Get the returned column names
  $i=0;
  while($i < mysql_num_fields($result)) 
    {
      $meta = mysql_fetch_field($result, $i);
      if(!$meta) 
	{
	  next;
	}
      
      $columnNames[] = $meta->name;
      $i++;
    }
  
  //I want to filter out name, easting and northing
  $columnFilter= array("name","easting","northing");
  $descriptionColumns = array_diff($columnNames,$columnFilter);
  
  //Creates an array of strings to hold the lines of the KML file.
  //$kml = array('<?xml version="1.0" encoding="UTF-8"? >');
  $kml = array('<?xml version="1.0" encoding="ISO-8859-1"?>');
  $kml[] = '<kml xmlns="http://earth.google.com/kml/2.2">';
  $kml[] = ' <Document>';
  
  
  //$color_county;
  $colour;
  
  //Iterates through the rows, printing a node for each row.
  while($row = @mysql_fetch_assoc($result)) 
    {
      
      //Only need to do the colour stuff 
      //  if($number_of_results>1000)
      // {
      //THIS IS A HACK (the making sure it is not '') TO FIX THE CENSUS DATA UNTIL THE MISSING PARISH IS INSERTED 19/8/2011
      if($row['pre74cc'] != "")
	{
	  $colour[$row['pre74cc']]=$row['count'];
	}
      //}
      
      //Convert coords to long/lat
      $os1 = new OSRef($row['easting'],$row['northing']);
      $ll1 = $os1->toLatLng();
      
      $coords = $ll1->toStringGoogle();
      
      //Get the rest of the columns and concat them all together.
      $description = '';
      foreach($descriptionColumns as $thisColumn)
	{
	  $description .= $row[$thisColumn]."<br/>";
	}
      
      
      $kml[] = ' <Placemark>';
      $kml[] = '   <name>'.htmlentities($row['name']).'</name>';
      $kml[] = '   <description><![CDATA['.utf8_decode($description).']]></description>';
      $kml[] = '     <coordinates>'.trim($coords).'</coordinates>';
      $kml[] = '   </Point>';
      $kml[] = ' </Placemark>';
    } 
  
  // End XML file
  $kml[] = ' </Document>';
  $kml[] = '</kml>';
  $kmlOutput = join("\n", $kml);
  
  $file=fopen($summary_file_path,"w");
  fwrite($file,$kmlOutput);
  fclose($file);
  
  //Zip up the summary file
  $cmd = "zip ../cache/".$temp_file_name."_summary.kmz ".$summary_file_path;
  exec($cmd);

  //Delete the kml file
  unlink($summary_file_path);


  //////////////////////////////////////////////////
  //Now figure out the colour coding for the counties
  //and stick it in an XML file
  $max_color=max($colour);
  $min_color=min($colour);
  $range_color = $max_color-$min_color;
  
  $color_file_path = "../cache/".$temp_file_name."_color.xml";
  $file=fopen($color_file_path,"w");
  fwrite($file,"<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n");
  fwrite($file, "<countysummarycolors>\n");
  foreach($colour as $thiscounty => $thiscount)
    {
      //Make this color 0-255
      $temp = (($thiscount-$min_color)/$range_color)*255;
      
      $r=$temp;
      $g=0;
      $b=255-$temp;
      
      //Make sure 0-255
      $r = dechex($r<0?0:($r>255?255:$r));
      $g = dechex($g<0?0:($g>255?255:$g));
      $b = dechex($b<0?0:($b>255?255:$b));
      
      //Make sure 2 characters and add to a string
      $color = (strlen($r) < 2?'0':'').$r;
      $color .= (strlen($g) < 2?'0':'').$g;
      $color .= (strlen($b) < 2?'0':'').$b;
      
      $output = "<county><code>".$thiscounty."</code><color>".$color."</color></county>\n";
      fwrite($file,$output);
    }
  fwrite($file, "</countysummarycolors>");
  fclose($file);

}

///////////////////////////////////////////////////
//Now do the more indepth query
///////////////////////////////////////////////////
$fine_result = mysql_query($fine_query);
if(!$fine_result) 
{
    die('Invalid fine query: ' . mysql_error());
}

$number_of_rows = mysql_num_rows($fine_result);

//Get the returned column names
//$fine_columnNames[];
$i=0;
while($i < mysql_num_fields($fine_result)) 
{
  $fine_meta = mysql_fetch_field($fine_result, $i);
  if(!$fine_meta) 
  {
    next;
  }
  
  $fine_columnNames[] = $fine_meta->name;
  $i++;
}

//I want to filter out name, easting and northing
$columnFilter= array("name","easting","northing");
$fine_descriptionColumns = array_diff($fine_columnNames,$columnFilter);

// Creates an array of strings to hold the lines of the KML file.
$kmla[0] = '';
$kmla[1] = '';
$kmla[2] = '';
$kmla[3] = '';
$kmla[4] = '';
$kmla[5] = '';
$kmla[6] = '';
$kmla[7] = '';

// Iterates through the rows, printing a node for each row.
while($row = @mysql_fetch_assoc($fine_result)) 
{
  //Convert coords to long/lat
  $os1 = new OSRef($row['easting'],$row['northing']);
  $ll1 = $os1->toLatLng();

  //$coords = $ll1->toStringGoogle();

  $lat = $ll1->toStringLat();
  $long = $ll1->toStringLong();

  //This is used as an index
  $lat_region = floor($lat-49);


  //Get the rest of the columns and concat them all together.
  $description = '';
  foreach($fine_descriptionColumns as $thisColumn)
  {
    $description .= $row[$thisColumn];
  }

  $kmla[$lat_region] .= ' <Placemark>';
  $kmla[$lat_region] .= '   <name>'.htmlentities($row['name']).'</name>';
  $kmla[$lat_region] .= '   <description><![CDATA['.utf8_decode($description).']]></description>';
  $kmla[$lat_region] .= '   <Point>';
  $kmla[$lat_region] .= '     <coordinates>'.trim($long.", ".$lat).'</coordinates>';
  $kmla[$lat_region] .= '   </Point>';
  $kmla[$lat_region] .= ' </Placemark>';
  $kmla[$lat_region] .= "\n";
} 


//If there are <1000 rows then put all this in one summary
//file, otherwise make all the smaller ones.
if($number_of_rows<1000)
{
  $kml  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
  $kml .= '<kml xmlns="http://earth.google.com/kml/2.2">';
  $kml .= ' <Document>';
  
  $file=fopen($summary_file_path,"w");
  fwrite($file,$kml);
  fwrite($file,$kmla[0]);
  fwrite($file,$kmla[1]);
  fwrite($file,$kmla[2]);
  fwrite($file,$kmla[3]);
  fwrite($file,$kmla[4]);
  fwrite($file,$kmla[5]);
  fwrite($file,$kmla[6]);
  fwrite($file,$kmla[7]);
  $kml  = ' </Document>';
  $kml .= '</kml>';
  fwrite($file,$kml);
  fclose($file);

  //Zip up the summary file
  $cmd = "zip ../cache/".$temp_file_name."_summary.kmz ".$summary_file_path;
  exec($cmd);

  //Delete the kml file
  unlink($summary_file_path);

}
else
{
  for($i=0; $i<=7; $i++)
    { 
      
      //$kmlOutput = implode("\n", $kml[$i]);
      $fine_file_path = "../cache/".$temp_file_name."_".$i.".kml";
      $file=fopen($fine_file_path,"w");
      fwrite($file,$kmla[$i]);
      fclose($file);
    }
}

//This gets passed to the JS that called this
$output_string = $temp_file_name.",".$number_of_rows;
if(isset($min_color) && isset($max_color))
{
  $output_string .= ",".$min_color.",".$max_color;
}

echo $output_string;

?>