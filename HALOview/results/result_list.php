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
//Display the results as a list
//Olly Butters
//28/10/2011
///////////////////////////////////////////////////

$SID = $_GET['SID'];
session_id($SID);
session_start();
$summary_query = $_SESSION['summary_query'];
$fine_query = $_SESSION['fine_query'];
$number_of_results = $_SESSION['number_of_results'];
$query_summary = $_SESSION['query_summary'];
//session_destroy();

$unsan_data_set = $_GET['data_set'];

$this_page_title="HALOGEN";
include("../common/header.php");
require("../common/db_connect.php");

$fine_result = mysql_query($fine_query);
if(!$fine_result) 
{
    die('Invalid fine query: ' . mysql_error());
}

$number_of_rows = mysql_num_rows($fine_result);

?>
<table id="summary" style="width:50%">
<tr><th>Constraint</th><th>Value</th></tr>
<?php
  foreach ($query_summary as $key => $value)
{
  echo "<tr><td>".$key."</td><td>".$value."</td></tr>";
}

echo "</table>";



echo $number_of_rows." results found.<br/><br/>";




if($unsan_data_set == "kepn")
{
  // Iterates through the rows, printing a node for each row.
  echo "<table id=\"stripy_table\">";
  echo "<tr><th style=\"width:10%\">Place-name</th><th style=\"width:20%\">Etymology</th><th style=\"width:20%\">Derivation</th><th style=\"width:10%\">References</th></tr>";
  while($row = @mysql_fetch_assoc($fine_result)) 
    {
      echo "<tr>";
      echo "<td>".htmlentities($row['name'])."</td>";
      echo "<td>".$row['etymology']."</td>";
      echo "<td>".$row['langs']."</td>";
      echo "<td>".$row['refs']."</td>";
      echo "</tr>";
    } 
  echo "</table>";
}
elseif($unsan_data_set == "census_parish")
{
  // Iterates through the rows, printing a node for each row.
  echo "<table id=\"stripy_table\">";
  echo "<tr><th>Parish</th><th>Population</th><th>Number of unique surnames</th><th>Area<br/>(square km)</th><th>Population density<br/>(Number per square km)</th><th>Surname density (Number of unique surnames per square km)</th><th>Unique surname density (Number of unique surnames per 100 people)</th></tr>";
  while($row = @mysql_fetch_assoc($fine_result)) 
    {
      echo "<tr>";
      echo "<td>".htmlentities($row['name'])."</td>";
      
      //This is an UGLY hack. The units are put in at the SQL level, so I Am 
      //striping them out here, it would be better if they were not there at all 
      //in the first place.
      $temp = explode(" ",$row['people']);
      echo "<td>".$temp[0]."</td>";

      $temp = explode(" ",$row['surnames']);
      echo "<td>".$temp[0]."</td>";

      $temp = explode(" ",$row['area']);
      echo "<td>".$temp[0]."</td>";

      $temp = explode(" ",$row['people_density']);
      echo "<td>".$temp[0]."</td>";

      $temp = explode(" ",$row['surname_density']);
      echo "<td>".$temp[0]."</td>";

      $temp = explode(" ",$row['unique_surname_density']);
      echo "<td>".$temp[0]."</td>";
      echo "</tr>";
    } 
  echo "</table>";
}
elseif($unsan_data_set == "census_surname")
{
  // Iterates through the rows, printing a node for each row.
  echo "<table id=\"stripy_table\">";
  echo "<tr><th>Parish</th><th>Percent of parish</th><th>Number per square km</th></tr>";
  while($row = @mysql_fetch_assoc($fine_result)) 
    {
      echo "<tr>";
      echo "<td>".htmlentities($row['name'])."</td>";
      
      echo "<td>".$row['percent']."</td>";

      $temp = explode(" ",$row['density']);
      echo "<td>".$temp[0]."</td>";

      echo "</tr>";
    } 
  echo "</table>";
}
elseif($unsan_data_set == "pas")
{
  // Iterates through the rows, printing a node for each row.
  echo "<table id=\"stripy_table\">";
  echo "<tr><th>Location</th><th>Finds</th></tr>";
  while($row = @mysql_fetch_assoc($fine_result)) 
    {
      echo "<tr>";
      echo "<td>".$row['easting'].",".$row['northing']."</td>";
      echo "<td>".$row['data']."</td>";
      echo "</tr>";
    } 
  echo "</table>";
}
else
{

}

include("../common/footer.php");

?>