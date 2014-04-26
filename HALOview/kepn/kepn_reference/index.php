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
//List of the reference material used in KEPN.
//Olly Butters 19/8/11
//////////////////////////////////////////////////

$this_page_title="KEPN elements definitions";
include("../../common/header.php");

require("../../common/db_connect.php");
?>


<h3>List of source references in the KEPN data</h3>

<table id="stripy_table">
<?php 

$result = mysql_query('SELECT short_title,long_title FROM kepn_reference_book ORDER BY short_title');
while($row = mysql_fetch_assoc($result))
  {
    echo "<tr><td>".$row["short_title"]."</td><td>".$row["long_title"]."</td></tr>";
  }
?>

</table>


<?php
include("../../common/footer.php");
?>
