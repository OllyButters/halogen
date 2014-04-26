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
<!DOCTYPE HTML>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="author" content="Olly Butters http://www.faji.co.uk">
  <title>HALOGEN - <?php if(isset($this_page_title)) {echo $this_page_title;}?></title>
  <link rel="stylesheet" href="/common/css/main.css" type="text/css"/>
  <link rel="icon" href="/common/images/favicon.ico" type="image/png"/>

<?php if(isset($this_page_javascript)) {echo $this_page_javascript;}?>
<?php //google analytics ?>
<script type="text/javascript">

   var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-26915583-1']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
 })();
</script>
</head>
<body <?php if(isset($this_page_onload_action)) {echo "onload=\"".$this_page_onload_action."\"";}?>>

  <div id="uni_logo">
    <a href="http://www.le.ac.uk"><img src="/common/images/unilogo.png" height="50" width="183" border="0" alt="University of Leicester logo" style="position:relative; float:left;"/></a>
  </div>


  <div id="title">HALOGEN</div>
  <div id="navigation_container">
    <a class="nav" href="/index.php">HALOGEN Home</a> | <a class="nav" href="/query/index.php">Query</a> | <a class="nav" href="/guide/">Guide</a> | <a class="nav" href="/faq/">FAQ</a> | <a class="nav" href="/partners/">Partners</a> | <a class="nav" href="/license/">License</a> | <a class="nav" href="/contact/">Contact us</a>
  </div>

  <div id="content">