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
//Look at the range of latitiudes requested and make a kmz file
//that covers it.

//Olly Butters
//20-11-2011

$kml_root = (isset($_GET['kml_root'])) ? $_GET['kml_root'] : NULL;
$kml_min  = (isset($_GET['kml_min'])) ? $_GET['kml_min'] : NULL;
$kml_max  = (isset($_GET['kml_max'])) ? $_GET['kml_max'] : NULL;

//Dirty check
if(!ctype_digit($kml_root) || !ctype_digit($kml_min) || !ctype_digit($kml_max))
{
  exit(1);
}

$output_file = $kml_root."_".$kml_min."_".$kml_max;

//There is every chance that this kml/z file exists - they could
//be going back to an area they have been to before
if(!file_exists("../cache/".$output_file.".kmz"))
{
  $file=fopen("../cache/".$output_file.".kml","w");

  //$header  = '<?xml version="1.0" encoding="UTF-8"? >';
  $header  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
  $header .= '<kml xmlns="http://earth.google.com/kml/2.2">';
  $header .= ' <Document>';
  fwrite($file,$header);
  
  //Read in part of file
  for($i=$kml_min; $i<$kml_max; $i++)
    {
      $file_path = "../cache/".$kml_root."_".$i.".kml";
      //readfile($file_path);
      //file_put_contents($output_file, 
      $source = fopen($file_path, "r");
      stream_copy_to_stream($source, $file);
      fclose($source);
    }
  
  // End XML file
  $footer = ' </Document>';
  $footer .= '</kml>';
  fwrite($file,$footer);
  
  fclose($file);

  //Zip up the summary file
  $cmd = "zip ../cache/".$output_file.".kmz ../cache/".$output_file.".kml";
  exec($cmd);
  
  //Get rid of the kml file
  unlink("../cache/".$output_file.".kml");
}
echo $output_file.".kmz";

?>