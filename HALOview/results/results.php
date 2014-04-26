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
//////////////////////////////////////////////////////////////////////
//Main results wrapper page
//Olly Butters 
//9/11/11
//A key point to note is that input data is prepended with unsan_
//and sanitised data with san_ All unsan is then deleted :)
//Tidied up and sanitized (23/8/2011)
/////////////////////////////////////////////////////////////////////

//Sort out any session variables that might be hanging around.
session_start();
$SID=session_id();
unset($_SESSION['fine_query']);
unset($_SESSION['summary_query']);
unset($_SESSION['error']);

require("../common/global_vars.php");

//////////////////////////////////////////////////////////////////////
//Get the initial common data on from the URL
//////////////////////////////////////////////////////////////////////

////////////////////////////////////////
//Get the data set flag - this defines which bit of code to run.
//This should be checked against the allowed values.

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
  echo "An unexpected error occurred! Go back to the <a href=\"../index.php\">main page</a> and try again.<br/>";
  echo "If you keep getting these errors then please get in contact with us.";
  exit;
}
 
//Connect to DB
require("../common/db_connect.php");

//Grab the common header stuff and start to load the page.
$this_page_title=$global_data_set_long_name[$san_data_set]." results";
$this_page_onload_action="initialize('$SID')";
$this_page_javascript =<<<EOT
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;region=GB"></script>
<script type="text/javascript" src="../common/js/draw_base_map.js"></script>
<script type="text/javascript" src="../common/js/county_boundaries.js"></script>
<script type="text/javascript" src="../common/js/plot_county_summary_data.js"></script>
<script type="text/javascript" src="../common/js/results.js"></script>
<script type="text/javascript" src="../common/js/overlays.js"></script>
EOT;
include("../common/header.php");
?>

<div style="overflow:auto;">
  <div style="width:40%; float:left;">
    <h2><?php echo $global_data_set_long_name[$san_data_set];?></h2>
  </div>
  <div id="status_box" style="width:40%; float:right;  padding-top:1em; text-align:right">
    Loading... <img src="../common/images/loading.gif"/> 
  </div>
</div>

<div id="left_column">
  <div id="map_container">
    <div id="map_canvas" style="width:100%;height:600px"></div>
  </div>
  <div id="copyright_container">
    County boundaries - &copy; <a
  href="http://www.port.ac.uk/research/gbhgis/" target="_blank">Great Britain Historical GIS Project</a>, University of Portsmouth.<br/>
    Coordinate system - &copy; Ordinance Survey Crown copyright.
    <span id="variable_copyright"></span>
  </div>


</div>

<div id="right_column">
<div class="metadata_container">
<strong>Summary</strong><br/>
<hr/>

<?php
/////////////////////////////////////////////////////////////
//Use the input to query the DB and plot the data.
////////////////////////////////////////////////////////////


//Define these here so can use it later.  
$query;
$number_of_results=0;

//Put a summary of the query data in this to display to the user
$query_summary;


////////////////////////////////////////////////////////////
//Each include file follows the same format and sets the 
//same variables that are then used here (and elsewhere)
//to do the queries. Those variables being:
//$_SESSION['fine_query'] - the full query.
//$_SESSION['summary_query'] - query aggregated up over counties.
//$_SESSION['number_of_results'] - the number of results from the fine query.
//$scale_count_unit - if the summary query is used (and more than 1000 results found) this is the key on the color bar.
//$query_summary[] - array of the parameters used in the query that are displayed on the page for the user.
//
//The SELECT columns must have a name,easting,northing. Any 
//further columns are appended automatically and appear in
//the balloons.
//
//The format of the files is along the lines of:
//1-Get all the GET/POST data and sanitize it!
//2-Define the SELECTS and JOINS for the queries, this 
//  involves a fine query and a summary query.
//3-Process the inputs, adding them to the constraints in 
//  the queries as needed.
//4-Save some data in session variables
//5-Do the fine query.
//
//When the page has loaded the $_SESSION[queries] get passed
//to a script (via JS and SID) that builds kml files and 
//throw them at the google servers.
////////////////////////////////////////////////////////////

require($file);

//If an error message was set then lets give up and let the user know.
if(isset($_SESSION['error']))
{
  echo "<strong>ERROR</strong>: ".$_SESSION['error'];

  echo "</div>";
?>
  <script>
  document.getElementById("status_box").innerHTML="Error"; 
  </script>
<?php
}
else
{


  //query summary
  //lets set the query summary as a session variable so the list view can see it.
  $_SESSION['query_summary']=$query_summary;

?>
<table id="summary">
<tr><th>Constraint</th><th>Value</th></tr>
<?php
foreach ($query_summary as $key => $value)
{
  echo "<tr><td>".$key."</td><td>".$value."</td></tr>";
}
?>
</table>

<br/><?php echo $_SESSION['number_of_results'] ?> matches to your query<br/> 


</div>

<?php
//Throw in a scale to the plot if needed
if($_SESSION['number_of_results']>1000)
{
?>
<div class="metadata_container" id="scale_div">
<div><strong>Scale</strong><span style="float:right">(<?php echo $scale_count_unit;?>)</span>
</div>
<div id="scale_bar"></div>
<span id="scale_bar_min" style="float:left;"></span>
<span id="scale_bar_max" style="float:right"></span>
</div>
<?php
}
?>

<div class="metadata_container">
<strong>Actions</strong><br/>
<hr/>

<?php
  //Very experimental at the moment - best to try this on small amounts of returned data.<br/>

  //Plot
  //<form id="plot_nearby">
  //<select name="plot_nearby">
  //<option value="none">None</option>
  //<option value="kepn">K.E.P.N.</option>
  //<option value="census_parish">1881 parish.</option>
  //</select>
  //</form>
  //within 10 km

  //<button class="button" onclick="plot_nearby_data2('<?php echo $SID? >',getElementById('plot_nearby').elements[0].value)">Go</button>

  //<hr/>
?>

<form>
Add an overlay:
<select name="overlay" onchange="add_map_overlay(this.options[selectedIndex].value)">
<option value="none">None</option>
<option value="soil">Soil types</option>
<option value="roman_roads">Roman roads</option>
</select>
</form>


<?php
if($san_data_set!="genetics")
{
?>

<hr/>

<button onclick="window.open('result_list.php?data_set=<?php echo $san_data_set?>&SID=<?php echo $SID?>')">Display results as a list</button>

<?php
}
?>

<?php 
  //if($san_data_set=="kepn"||$san_data_set=="genetics"||$san_data_set=="census_surname")
  //{
//<form action="download.php" method="post">
//  <input type="hidden" name="data_set" value="< ? php echo $san_data_set ? >" / >
//  <input type="hidden" name="download_query" value="< ? php echo $download_query ? >" / >
//  <input type="hidden" name="download_columns" value="< ? php echo $download_columns ? >" / >
//<input type="submit" value="Download this data!"/>(In tab separated format - i.e. ArcGIS)
//</form>
  //}
?>

<div id="kml"></div>



</div>


<?php
//if there wasnt an error if statement
}
?>

</div>

<?php
  include("../common/footer.php");
?>
