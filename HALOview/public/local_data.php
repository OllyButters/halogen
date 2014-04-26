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
//Connect to DB - read only of course!
require("../common/db_connect.php");

//To convert to easting/northing
require("phpcoord-2.3.php");

$lng = isset($_REQUEST['lng']) ? $_REQUEST['lng'] : 0;
$lat = isset($_REQUEST['lat']) ? $_REQUEST['lat'] : 0;

if(!ctype_digit($lng) || !ctype_digit($lat))
{
 $lng = 0;
 $lat = 0;
}

//Convert coords to long/lat                                                                             
$ll1 = new LatLng($lat,$lng);
$os1 = $ll1->toOSRef();

$easting = $os1->toStringEasting();
$northing = $os1->toStringNorthing();

$radius = 15000;

$easting_min = $easting - $radius;
$easting_max = $easting + $radius;
$northing_min = $northing - $radius;
$northing_max = $northing + $radius;


$query = "SELECT placename AS name, easting, northing, etymology FROM kepn_place WHERE easting BETWEEN $easting_min AND $easting_max AND northing BETWEEN $northing_min AND $northing_max LIMIT 250"; 


//Define a random file name in the public part to write this data to.
//The summary will have an over view and the rest will have smaller
//chunks of the whole lot
$temp_file_name = rand();
$summary_file_path = "../cache/local_".$temp_file_name.".kml";

//Do the summary query first then decide if we want to do more later.
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

//Iterates through the rows, printing a node for each row.                      
while($row = @mysql_fetch_assoc($result))
  {
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
    $kml[] = '   <Point>';
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
$cmd = "zip ../cache/local_".$temp_file_name.".kmz ".$summary_file_path;
exec($cmd);

//Delete the kml file                                                           
unlink($summary_file_path);

echo $temp_file_name;
?>