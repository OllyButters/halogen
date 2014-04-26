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
//Wrapper to do a query on a given data set
//Olly Butters 23/8/11
//Tidied and sanitized (23/8/2011)
///////////////////////////////////////////////////

require("../common/global_vars.php");

//Get the data set name and check it is ok.
$unsan_data_set = (isset($_REQUEST['data_set'])) ? $_REQUEST['data_set'] : NULL;
$san_data_set = (preg_match("/^[a-zA-Z0-9_]+$/",$unsan_data_set)) ? $unsan_data_set : NULL;
unset($unsan_data_set);

if(in_array($san_data_set, $global_allowed_data_sets))
{    
    $file = "data_sets/".$san_data_set.".php";
}                                                               
else
{
    $file = "";
    echo "An error seems to have occurred! Please go back to the <a href=\"../index.php\">main page</a> and try again.";
    exit;
}


//Grab the common header stuff and start to load the page.
$this_page_title=$global_data_set_long_name[$san_data_set]." query";
$this_page_onload_action="initialize()";
$this_page_javascript  = "  <script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>\n";
$this_page_javascript .= "  <script type=\"text/javascript\" src=\"../common/js/draw_base_map.js\"></script>\n";
$this_page_javascript .= "  <script type=\"text/javascript\" src=\"../common/js/county_boundaries.js\"></script>\n";
$this_page_javascript .= "  <script type=\"text/javascript\" src=\"../common/js/county_behaviour.js\"></script>\n";
$this_page_javascript .= "  <script type=\"text/javascript\" src=\"../common/js/query.js\"></script>\n";

include("../common/header.php");

//Connect to DB - read only of course!
require("../common/db_connect.php");
?>

<h2><?php echo $global_data_set_long_name[$san_data_set];?></h2>

<div id="left_column">
  <div id="map_container">
    <div id="map_canvas" style="width:100%;height:600px;background-color:#B1C3D4">Loading map. If the map does not appear then check that you have javascript enabled.</div>
  </div>

  <div id="copyright_container">
    County boundaries - &copy; <a href="http://www.port.ac.uk/research/gbhgis/" target="_blank">Great Britain Historical GIS Project</a>, University of Portsmouth.
  </div>

</div>

<div id="right_column">
  <?php
  require($file);
  ?>
</div>


<?php
include("../common/footer.php");
?>
