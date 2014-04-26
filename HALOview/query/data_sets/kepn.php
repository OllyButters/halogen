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
//////////////////////////////////////////////////////////////
//Make the KEPN query page
//Olly Butters 
//14/10/11
//////////////////////////////////////////////////////////////

//Get the data from the DB to fill the select options.
$county_result = mysql_query('SELECT countycode, county FROM gref_county WHERE in_kepn=\'T\' ORDER BY county');
$hword_result = mysql_query('SELECT hword,note FROM kepn_elements ORDER BY hword LIMIT 10000');
?>

<div id="whole_query_container">
  <form id="query" name="query" action="../results/results.php" method="get">

    <!--Spatial part-->
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

    <!--Language part-->
    <!--These all actually contain data - hence not a DB query-->
    <div class="section_query_container">
      <div class="section_query_container_left">
	  <strong>Search by language:</strong><br/>(Hold Ctrl to select multiple languages.)
      </div>

      <div class="section_query_container_right">
        <select name="lang[]" multiple="multiple" size=8 style="width:100%">
        <option value=all selected="selected">ALL LANGUAGES</option>
        <option value=null>----------</option>
	<option value=AGGCE>Celtic-like (Grouped)</option>
	<option value=AGGFR>French-like (Grouped)</option>
	<option value=AGGOE>Old English-like (Grouped)</option>
	<option value=AGGON>Old Norse-like (Grouped)</option>
        <option value=L>Latin</option>
        <option value=null>----------</option>
        <option value=OA>Anglian</option>
        <option value=CB>British</option>
        <option value=C>Celtic</option>
        <option value=CBG>Cornish</option>
        <option value=DE>Early Modern English</option>
        <option value=I>Italian</option>
        <option value=OK>Kentish</option>
        <option value=OAM>Mercian</option>
        <option value=CBI>Middle Cornish</option>
        <option value=M>Middle English</option>
        <option value=D>Modern English</option>
        <option value=DD>Modern English Dialect</option>
        <option value=OAN>Northumbrian (inc. Old N)</option>
        <option value=CBE>Old Cornish</option>
        <option value=ND>Old Danish</option>
        <option value=NE>Old East Scandinavian</option>
        <option value=O>Old English</option>
        <option value=F>Old French</option>
        <option value=G>Old German</option>
        <option value=CGO>Old Irish</option>
        <option value=N>Old Norse</option>
        <option value=FN>Old Northern French</option>
        <option value=NN>Old Norwegian</option>
        <option value=CBD>Old Welsh</option>
        <option value=NW>Old West Scandinavian</option>
        <option value=CBB>Primitive Cornish</option>
        <option value=CBA>Primitive Welsh</option>
        <option value=Z>Unknown</option>
        <option value=CBF>Welsh</option>
        <option value=OW>West-Saxon</option>
        </select>
      </div>

    </div>

    <br/>

    <!--ELEMENT part-->
    <div class="section_query_container">
      <div class="section_query_container_left">
	  <strong>Search by element:</strong><br/>Hover your mouse over the elements for their definition, or look at the list of <a href="../kepn/kepn_elements/">valid elements</a>.  Hold Ctrl to select multiple elements from the list.
      </div>

      <div class="section_query_container_right">
<!--	<select name="hword_list" style="width:100%">-->
	<select name="hword_list[]" multiple="multiple" size=8 style="width:100%"> 
        <option value=all selected="selected">ALL ELEMENTS</option>
        <option value=null>----------</option>
        <?php
        //Get all the hwords.
	  while($hword_row = mysql_fetch_row($hword_result))
	  {
	    echo "<option style=\"width:50px;\" value=".$hword_row[0]." title=\"".$hword_row[1]."\">".$hword_row[0]."</option>";// (".$hword_row[1].")</option>";
	  }
	  ?>
        </select>
<!--        <br/>
        OR
        <br/>

        <input type="text" name="hword_user" style="width:100%"/>-->
	</div>
    </div>

    <br/>

    <!--Name part-->
    <div class="section_query_container">
      <div class="section_query_container_left">
        <strong>Search by place name:</strong><br/>
      </div>

      <div class="section_query_container_right">
	  <input type="text" name="placename" style="width:100%"/><br/>
	  <select name="placename_match_type" style="width:100%">
	    <option value="exact">Exact match</option>
	    <option value="begin">Begins with</option>
	    <option value="end">Ends with</option>
	    <option value="contains">Contains</option>
	    <option value="persn_begin">Personal name (at beginning)</option>
	    <option value="sounds_like">Sounds like</option>
	    <option value="pattern">Matches a pattern</option>
	  </select>
      </div>
    </div>

    <input type="hidden" name="data_set" value="kepn" />
    <br/>
    <input type="submit" class="button"/>
    <input type="reset" class="button"/>
  </form>
</div>

