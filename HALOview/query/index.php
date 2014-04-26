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
$this_page_title="Query";
include("../common/header.php");
?>


Select which data set you want to query.
<br/>

<ul>
<li><a href="/query/kepn">K.E.P.N.</a> - Key to English place names. Look at the etymology and derivation of place names in England.</li>
<li><a href="/query/genetics">Genetics</a> - Look at the haplogroup from two genetics studies across England.</li>
<li><a href="/query/census_surname">1881 census surname</a> - Find surnames in the 1881 census.</li>
<li><a href="/query/census_parish">1881 census parish</a> - Look at the distribution of surnames within a parish in the 1881 census.</li>
<li><a href="/query/pas">P.A.S.</a> - Portable Antiqities Scheme.</li>
</ul>


<?php
include("../common/footer.php");
?>
