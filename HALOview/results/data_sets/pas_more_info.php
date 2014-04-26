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
//PAS data processing
//Olly Butters 
//6/9/2011
///////////////////////////////////////////////////

require("../../common/global_vars.php");

include("../../common/header.php");

//Connect to DB - read only of course!
require("../../common/db_connect.php");

/////////////////////////////////////////
//Coords
$unsan_easting = (isset($_REQUEST['easting'])) ? $_REQUEST['easting'] : NULL;
//unset($unsan_easting);

$unsan_northing = (isset($_REQUEST['northing'])) ? $_REQUEST['northing'] : NULL;
//unset($unsan_easting);

///////////////////////////////////////////////////
//Broadperiod
$unsan_broadperiod = (isset($_REQUEST['broadperiod'])) ? $_REQUEST['broadperiod'] : NULL;
$san_broadperiod = (preg_match("/^[a-zA-Z ]+$/",$unsan_broadperiod)) ? $unsan_broadperiod : NULL; 
unset($unsan_broadperiod);



////////////////////////////////////////////////////////////
//Start to build the queries

//////////////////////////////////////////////////
//Fine
$query = "SELECT 
            broadperiod,
            objecttype,
            description
          FROM
            pas_findspots
          LEFT JOIN pas_finds ON pas_findspots.findid = pas_finds.secuid
          WHERE 
            easting = $unsan_easting 
            AND
            northing = $unsan_northing";

//Get the coordinate data
$result = mysql_query($query);

//See how many rows we got
$number_of_results = mysql_num_rows($result);

echo "<table id=\"stripy_table\">";
echo "<tr><th>Period</th><th>Type</th><th>Description</th></tr>";
while($row = mysql_fetch_row($result))
{
  echo "<tr><td>".$row[0]."</td><td>". $row[1]."</td><td>".$row[2]."</td></tr>";
}
echo "</table>";


include("../../common/footer.php");

?>
