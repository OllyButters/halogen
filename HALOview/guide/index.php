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
//Guide to using the web interface and the data sets
//Olly Butters
//14/10/11
//////////////////////////////////////////////////

$this_page_title="The manual";
include("../common/header.php");
?>

<p>This how-to guide is split into two main parts, the first (general) part explains some of the features common to all data sets. The second part has information specific to individual data sets. Jump to <a href="#kepn">K.E.P.N.</a>, <a href="#pas">P.A.S</a>, <a href="#genetics">genetics</a>, <a href="#1881_surname">1881 census surname</a> and <a href="#1881_parish">1881 census parish</a>.</p>

<p>If you are interested in the real technical documentation then have a look at the <a href="http://www2.le.ac.uk/offices/itservices/resources/cs/pso/project-websites/halogen/documents/HALOGEN-Data-glossary-V1-final.pdf">data glossary</a>.</p>

<hr/>

<h2>General</h2>
<ul>
  <li>
     <strong>Overview</strong> - HALOGEN holds several data sets, each can be queried with a set of predefined parameters by filling in a web form. The results of these queries are maps with data plotted on them. 
  </li>
  <li>
    <strong>County definition</strong> - Counties change name and shape all the time, for this reason we define our counties to conform to the 1961 county boundries with the following adjustments:
    <ul>
      <li>East and West Suffolk were merged to form Suffolk.</li>
      <li>East and West Sussex were merged to form Sussex.</li>
      <li>Lincolnshire Lindsey, Lincolnshire Holland and Lincolnshire Kesteven were merged to form Lincolnshire.</li>
      <li>Soke of Peterbourgh was added to Huntingdonshire.</li>
      <li>Cambridge and Isle of Ely were added to Cambridgeshire.</li>
    </ul>
  </li> 
  <li>
	 <strong>Doing a query</strong> - Each data set can be queried by filling in a web form with the parameters that you are interested in. In each case the query page consists of a map on the left hand side and a set of drop-down boxes and text boxes on the right. If you are interested in spatially constraining the data then you can click on a county on the map to select it, or you can select if from the list on the right. You can select multiple counties on the map by clicking them, or from the list by holding the Ctrl key when you click on them. Each data set has further parameters you can query on in addition to the county, these are outlined below.</li>

  <li>
    <strong>Understanding the results</strong> - The results page will have three distinct parts; a map of the U.K., a summary of the parameters you input, and an 'actions' box.
    <ul>
      <li>
        <strong>The map</strong> - A google map will show a satellite image of the U.K., after a brief interval all the results from your query will be displayed. If your query was such that over 1000 results matched it then outlines of each of the counties will be drawn and their colour set to represent how many are in each county. A scale bar will be displayed on the right along with the units of what is being represented on the map. If you zoom in using the controls on the map then the coloured in counties will be removed and markers displayed in their place.

  If your query returned less than 1000 results, or you have zoomed in then the markers will be displayed on the map. You can navigate around by clicking on parts of the map, using the clickable controls on the map, or using key strokes. If you click on a marker it will display a bubble with further information about that location. An explanation of the information in the bubbles is availabe in each data-set specific section below.
      </li>
      <li>
	 <strong>The summary</strong> - This box will give a summary of the options that you selected on the query form.
      </li>
      <li>
         <strong>The actions</strong> - This box shows what actions are available for these results. 
         <ul>
           <li>The overlay menu allows you to add an overlay to the map.</li>
           <li>The display results as a list button opens a new page with all the data from the bubbles in an HTML table format.</li>
         </ul>
      </li>
    </ul>
  </li>
</ul>

<hr/>

  <a name="kepn"></a>
  <h2>Key to English Place Names (K.E.P.N.)</h2>
  The key to English Place Names data set has the etymology of thousands of places spread across England. It is a project hosted at the University of Nottingham in the <a href="http://www.nottingham.ac.uk/~aezins/kepn.php">Institue for Name-Studies</a>.

  <h3>Doing a query</h3>
  You can constrain your query on a combination of spatial, language, element and place name parameters. If you don&#39;t want to use a field then just leave it blank. 

All the text input boxes do not depend of the case of the text.

<ul>
  <li>
    <strong>Search by Language</strong> - By default 'ALL LANGUAGES' is selected, this means that all languages will be included in the query. Below this is a list of grouped languages which is defined as in the table below. Below that are the more specific languages.

<table border=1 style="margin:1em;">
  <tr><th style="width:8em;">Group name</th><th>Constituent languages</th></tr>
  <tr>
    <td>Celtic-like</td><td>Celtic, British, Primitive Welsh, Primitive Cornish, Primitive Cumbrian, Old Welsh, Old Cornish, Welsh, Cornish, Cumbrian, Middle Cornish, Romano-British, Goidelic, Manx, Irish, Middle Irish, Old Irish and Scots Gaelic.</td>
  </tr>
  <tr>
    <td>French-like</td><td>Old French and Old Northern French.</td>
  </tr>
  <tr>
    <td>Old English-like</td><td>Old English, Anglian, East Anglian, Mercian, Northumbrian (including Old Norse), East Saxon, Kentish, West Saxon, Early West Saxon and Late West Saxon.</td>
  </tr>
  <tr>
    <td>Old Norse-like</td><td>Old Norse, Old Danish, Old East Scandinavian, Old Norwegian and Old West Scandinavian.</td>
  </tr>
</table>

You can select multiple languages by clicking on different languages while holding the 'Ctrl' key (or the 'command'/'apple' key on a Macintosh).
  </li>

  <li>
   <strong>Search by Element</strong> - By default 'ALL ELEMENTS' is selected, this means all elements will be included in your search. You can pick specific elements from the list, choosing multiple elements by holding the 'Ctrl' key as you select them. Hovering your mouse over an element should show its definition, a complete list of elements, along with their definitions, can be found on the  <a href="../kepn/kepn_elements/">list of valid elements page</a>.
  </li>

  <li>
    <strong>Search by place name</strong> - By default this text box is empty, this means there are no constraints put on the place name. The value of the drop down box dictates how any text in the box is dealt with.
    <ul>
      <li>
        <strong>Exact match</strong> - This will find only places with an exact match to the input.
      </li>
      <li>
        <strong>Begins with</strong> - This will find places that begin with the input.
      </li>
      <li>
        <strong>Ends with</strong> - This will find places that end with the input.
      </li>
      <li>
        <strong>Contains</strong> - This will find places that contain the input, this includes places that match exactly.
      </li>
      <li>
        <strong>Personal name (at the beginning)</strong> - Many places have their name derived from a persons name, using this option will find places that begin with the text in the text input box where the beginning element is personal name. If you select this option, but leave the text input box empty then all the places with a personal name at the beginning will be returned.
      </li>
      <li>
        <strong>Sounds like</strong> - This will return places that sound like the text input. This is based on the <a href="http://en.wikipedia.org/wiki/Soundex">soundex algorithm</a> and so may not give you an intuitive answer.
      </li>
      <li>
        <strong>Matches a pattern</strong> - This allows you to input a pattern of text, '&#37;' will match zero or more characters '&#95;' (underscore) will match exactly one character. As an example; '&#95;iss' matches 'Diss' and 'Liss', while '&#37;iss' matches both those and 'Stoke Bliss'. You can build arbitrarily complex queries, for example '&#95;ott&#37;ham' will match 'Bottisham','Cottenham','Cottingham' (x2),'Mottingham','Nottingham','Potter Heigham' and 'Tottenham'.
      </li>
    </ul>
  </li>
</ul>

<h3>Understanding the results</h3>

<strong>The bubbles</strong> - In this data set the bubbles have information specific to a place name. This begins with the etymology of the place name. Then each element is listed along with its language and its meaning. Since the true derivation of each element may not always be certain there may be multiple suggestions for each one. Finally a list of references is included.



<a name="pas"></a><hr/>
<h2>Portable Antiquities Scheme (P.A.S.)</h2>

The Portable Antiqities Scheme data has information about finds around the UK. It is a snapshot of the data held by the British Museum at their <a href="http://finds.org.uk">finds website</a> taken at the start of 2011. The data held here has had sensitive find spots removed, and each find has had its coordinates rounded to the nearest kilometre.

  <h3>Doing a query</h3>

<ul>
  <li>
    <strong>Search by period</strong> - By default 'ALL PERIODS' is selected, this means that all periods will be included in the query.
  </li>
</ul>


<h4>Understanding the results</h4>

   <strong>The bubbles</strong> - In this data set the bubbles have information specific to all find spots within a square kilometre (aggregated to a single point). Each bubble will have a list of objects found at this location comprising three parts; the broad period it is from, what it is, and how many were found. The bubble also contains a link to find out more information about this area, clicking on it will take you a page with a list of all the objects found in this area along with an in depth description about each.

<hr/>

<a name="genetics"></a>
<h2>Genetics</h2>

The genetics data set is an amalgamation of two different haplogroup data sets; Cappelli and Jobling.

  <h3>Doing a query</h3>
  
<ul>
  <li>
    <strong>Search by haplogroup</strong> - By default 'ALL HAPLOGROUPS' is selected, this means that all haplogroups will be included in the query.
  </li>
  <li>
    <strong>Search by data set</strong> - By default 'BOTH' is selected, this means that both the Capelli and Jobling data sets will be included in the query.
  </li>
</ul>

<h3>Understanding the results</h3>

<strong>The bubbles</strong> - In this data set the bubbles have the number of people in each haplogroup at that location.

<hr/>

<a name="1881_surname"></a>
<h2>1881 census surname</h2>
The 1881 census data has summary information of the 1881 census provided by KS (project name?). This search page allows you to search the 1881 parishes based on a surname.

<h3>Doing a query</h3>
  
<ul>
  <li>
    <strong>Search by surname</strong> - Input a surname to search for.
  </li>
</ul>

<h3>Understanding the results</h3>

<strong>The bubbles</strong> - In this data set the bubbles have information about the parish they are centred in. The first line of numbers shows the number of people with that surname and the total number number of people in that parish. The second line has the spatial number density of people with that surname.  

<hr/>

<a name="1881_parish"></a>
<h2>1881 census parish</h2>
The 1881 census data has summary information of the 1881 census provided by KS (project name?). This search page allows you to search the 1881 parishes directly.


<h3>Doing a query</h3>
  
<ul>
  <li>
    <strong>Search by parish</strong> - Input a parish to search for.
  </li>
</ul>

<h3>Understanding the results</h3>

<strong>The bubbles</strong> - The results bubbles have summary information about the parish.

<?php
include("../common/footer.php");
?>
