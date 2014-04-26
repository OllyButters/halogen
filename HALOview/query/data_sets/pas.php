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
//PAS query form
//Olly Butters
//29/9/11 
//////////////////////////////////////////////////

//Get the data from the DB to fill the select options.
$county_result = mysql_query("SELECT countycode, county FROM gref_county WHERE in_kepn='T' ORDER BY county ASC LIMIT 100");
?>

<div id="whole_query_container">
  <form id="query" name="query" action="../results/results.php" method="get">
    <div class="section_query_container">
      <div class="section_query_container_left">
        <strong>Search by county:</strong><br/>
        Click on the counties on the map, or select from the list. Hold Ctrl to select multiple counties from the list.
      </div> 
 
      <div class="section_query_container_right"> 
        <select id="county" name="county[]" multiple="multiple" onchange="list_sel()" size=8 style="width:100%">
        <?php
        echo "<option value=all selected=\"selected\">ALL COUNTIES</option>";
        echo "<option value=null>----------</option>";
        while($county_row = mysql_fetch_row($county_result))
        {
	  echo "<option value=".$county_row[0].">".$county_row[1]."</option>";
        }
        ?>
        </select>
      </div>
    </div>

    <br/>

    <div class="section_query_container">
      <div class="section_query_container_left">
        <strong>Search by period:</strong><br/>
      </div> 
 
      <div class="section_query_container_right"> 
	<select id="broadperiod" name="broadperiod" size=8 style="width:100%">
         <option value=all selected="selected">ALL PERIODS</option>
         <option value=null>----------</option>
	 <option value="BRONZE AGE">Bronze age</option>
	 <option value="BYZANTINE">Byzantine</option>
	 <option value="EARLY MEDIEVAL">Early Medieval</option>
	 <option value="FOREIGN">Foreign</option>
	 <option value="GREEK AND ROMAN PROVINCIAL">Greek and Roman provincial</option>
	 <option value="IRON AGE">Iron age</option>
	 <option value="MEDIEVAL">Medieval</option>
	 <option value="MESOLITHIC">Mesolithic</option>
	 <option value="MODERN">Modern</option>
	 <option value="NEOLITHIC">Neolithic</option>
	 <option value="PALAEOLITIC">Palaeolithic</option>
	 <option value="POST MEDIEVAL">Post Medieval</option>
	 <option value="PREHISTORIC">Prehistoric</option>
	 <option value="ROMAN">Roman</option>
	 <option value="UNKNOWN">Unknown</option>
        </select>
      </div>
    </div>

    
    <input type="hidden" name="data_set" value="pas" />
    <br/>
    <input type="submit" class="button"/>
    <input type="reset" class="button"/>
  </form>

</div>
