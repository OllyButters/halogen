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
$this_page_title="HALOGEN";
$this_page_onload_action="local_map()";
$this_page_javascript =<<<EOT
<script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAABy6uIqzwrNGgD6t7BIPoDRQ09ci9GFkYgEta4gXdt5jyNQc1MxSh6674IUhNpfxRcNvt15h0SSCpOw"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="../common/js/local_map.js"></script>
EOT;
include("common/header.php");
?>
<h2>Welcome to the HALOGEN geospatial search facility</h2>


<div id="left_column"> 
<div id="map_container" style="width:100%; height:400px; background-color:#B1C3D4"></div>
<span id="map_caption"></span>
</div>

<div id="right_column"> 
  <p>We host geospatial data covering the etymology of English place names, the 1881 census, genetics data and the portable antiquities data. A more in depth summary of each data set is available in the <a href="guide/">user guide</a></p>

<p>The map on the left shows an example of the data available near you. You can search specific data sets by following the links below.</p>

Do a <a href="query/kepn">English place-name etymology query</a>.<br/>
Do a <a href="query/genetics">genetics query</a>.<br/>
Do a <a href="query/census_surname">census surname query</a>.<br/>
Do a <a href="query/census_parish">census parish query</a>.<br/>
Do a <a href="query/pas">portable antiquities query</a>.<br/>


<p>Further information about the HALOGEN project can be found at <a href="http://www.le.ac.uk/halogen" target="_blank">www.le.ac.uk/halogen</a>.</p>
</div>


<?php
include("common/footer.php");
?>
