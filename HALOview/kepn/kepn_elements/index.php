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
//List of the elements in the DB.
//Olly Butters 19/8/11
//////////////////////////////////////////////////

$this_page_title="KEPN elements definitions";
include("../../common/header.php");

require("../../common/db_connect.php");

$hword_result = mysql_query('SELECT hword,description,note FROM kepn_elements LEFT JOIN kepn_language ON kepn_elements.langcode = kepn_language.code ORDER BY hword');
?>


<h3>Alphabetical list of elements in the KEPN data</h3>


<br/>
<a href="#-">-</a>
<a href="#A">A</a>
<a href="#B">B</a>
<a href="#C">C</a>
<a href="#D">D</a>
<a href="#E">E</a>
<a href="#F">F</a>
<a href="#G">G</a>
<a href="#H">H</a>
<a href="#I">I</a>
<a href="#J">J</a>
<a href="#K">K</a>
<a href="#L">L</a>
<a href="#M">M</a>
<a href="#N">N</a>
<a href="#O">O</a>
<a href="#P">P</a>
<a href="#Q">Q</a>
<a href="#R">R</a>
<a href="#S">S</a>
<a href="#T">T</a>
<a href="#U">U</a>
<a href="#V">V</a>
<a href="#W">W</a>
<a href="#Y">Y</a>

<?php 

$current_letter="";
while($hword_row = mysql_fetch_assoc($hword_result))
  {
    //If it is a new letter then put in heading etc
    if(strcmp($current_letter, ucfirst(substr($hword_row["hword"],0,1)))!=0)
      {
	//Only clost this list if one has already been started
	if(strcmp($current_letter,"")!=0)
	  {
	    echo "</ul>";
	  }
	
	echo "<hr/><h3><a name=\"".ucfirst(substr($hword_row["hword"],0,1))."\">".ucfirst(substr($hword_row["hword"],0,1))."</a></h3>";
	echo "<ul>";
	
	//Make a note of this NEW letter
	$current_letter = ucfirst(substr($hword_row["hword"],0,1));
      }

    echo "<li><strong>".$hword_row["hword"]."</strong> (<i>".$hword_row["description"]."</i>) ".$hword_row["note"]."</li>";
  }
?>

</ul>

<br/>
<a href="#-">-</a>
<a href="#A">A</a>
<a href="#B">B</a>
<a href="#C">C</a>
<a href="#D">D</a>
<a href="#E">E</a>
<a href="#F">F</a>
<a href="#G">G</a>
<a href="#H">H</a>
<a href="#I">I</a>
<a href="#J">J</a>
<a href="#K">K</a>
<a href="#L">L</a>
<a href="#M">M</a>
<a href="#N">N</a>
<a href="#O">O</a>
<a href="#P">P</a>
<a href="#Q">Q</a>
<a href="#R">R</a>
<a href="#S">S</a>
<a href="#T">T</a>
<a href="#U">U</a>
<a href="#V">V</a>
<a href="#W">W</a>
<a href="#Y">Y</a>


<?php
include("../../common/footer.php");
?>
