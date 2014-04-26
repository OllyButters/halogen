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
var xmlhttp;
var kml_root;
var dataLayer;

function change_county_colours(kml_root)
{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange=function()
    {
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
	    //var response=xmlhttp.responseText;
	    var xml=xmlhttp.responseXML.documentElement.getElementsByTagName("county");

	    for(i=0;i<xml.length;i++)
	    {
		code  = xml[i].getElementsByTagName("code");
		color = xml[i].getElementsByTagName("color");
				
		var temp='regions[\''+code[0].firstChild.nodeValue+'\'].setOptions({fillColor:"#'+color[0].firstChild.nodeValue+'"})';
		eval(temp);
	    }
	}
    }
    
    var this_kml=kml_root+'_color.xml';
    var kml_url = 'http://'+document.domain+'/cache/'+this_kml;
    xmlhttp.open("GET",kml_url,true);
    xmlhttp.send();
}


function plot_county_summary_data(SID)
{     
    var number_of_rows;

    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    xmlhttp.onreadystatechange=function()
    {
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
	    var returned_data=xmlhttp.responseText.split(",");
	    kml_root = returned_data[0];
	    number_of_rows = returned_data[1];
	    min_color = returned_data[2];
	    max_color = returned_data[3];	    

	    //If there is more than 1000 then plot colors, else plot markers.
	    if(number_of_rows>1000)
	    {
		draw_county_boundaries();
		change_county_colours(kml_root);
		
		//Sort the scale out
		document.getElementById("scale_bar_min").innerHTML=min_color;
		document.getElementById("scale_bar_max").innerHTML=max_color;

		//Change the status box to finished
		document.getElementById("status_box").innerHTML="Finished loading"; 
	    }
	    else
	    {
		var this_kml=kml_root+'_summary.kmz';
		var kml_url = 'http://'+document.domain+'/cache/'+this_kml;
		dataLayer = new google.maps.KmlLayer(kml_url, {preserveViewport:true});
		dataLayer.setMap(map);

		//Change the status box to finished
		document.getElementById("status_box").innerHTML="Finished loading"; 
	    }
	}
    }
    
    url='http://'+document.domain+'/public/make_kmls.php?SID='+SID;
    
    xmlhttp.open("GET",url,true);
    xmlhttp.send();

    //Check to see if we need to add any more data when the map has been moved/zoomed
    google.maps.event.addListener(map, 'idle', showMarkers);

    
    function showMarkers() 
    {



	//If there is less than 1000 results we should have the summary
        //(and therefore all) data plotted so we dont need to replot
	if(number_of_rows>1000)
	{

	    //Approximate latitude extremities for England
	    var master_min_lat = 49;
	    var master_max_lat = 60;
	    
	    //Only want to show new data if we have zoomed in (bigger=more zoomed)
	    var zoom = map.getZoom();
	    if(zoom>=9)
	    {
		//If we were zoomed out before then we need to drop the coloured polygons
		if(zoom_level=='summary')
		{
		    for(x in regions)
		    {
			regions[x].setMap(null);
		    }
		}

		//We also dont need the scale bar box anymore.
		document.getElementById("scale_div").style.display="none";
		

		//Look at the new bounds and decide if we want to update what data is being shown.
		var bounds = map.getBounds();
		var NE = bounds.getNorthEast();
		var SW = bounds.getSouthWest();	    
		var target_max_lat = NE.lat();
		var target_min_lat = SW.lat();
		    
		var min_kml_file = Math.floor(target_min_lat-master_min_lat);
		var max_kml_file = Math.ceil(target_max_lat-master_min_lat);
		
		//The kml files range from 0 to 6
		if(min_kml_file<0){min_kml_file=0;}
		if(max_kml_file>7){max_kml_file=7;}
		
		//Look to see if a different kml file is needed
		if((min_kml_file<current_min_kml_file || max_kml_file>current_max_kml_file) || zoom_level=='summary')
		{
		    //Probably do another AJAX call here.
		    if (window.XMLHttpRequest)
		    {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		    }
		    else
		    {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		    }
		    
		    xmlhttp.onreadystatechange=function()
		    {
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			    //URL of concatted file
			    var file_name=xmlhttp.responseText;
			    var kml_url = 'http://'+document.domain+'/cache/'+file_name;
			    
			    //Output file url to page for debugginh
			    document.getElementById("temp_kml_name").innerHTML=kml_url;
			    
			    //Get rid of any old overlays
			    if(typeof dataLayer != 'undefined')
			    {
				dataLayer.setMap(null);
			    }

			    //Add this kml data
			    dataLayer = new google.maps.KmlLayer(kml_url, {preserveViewport:true});
			    dataLayer.setMap(map);
			}
		    }
		    
		    //The php that concats all the separate kmls together.
		    var url='http://'+document.domain+'/public/concat_kml2.php?kml_root='+kml_root+'&kml_min='+min_kml_file+'&kml_max='+max_kml_file;
		    
		    xmlhttp.open("GET",url,true);
		    xmlhttp.send();
		    
		    //Make a note of these values so we dont do this again if we just move a bit
		    current_min_kml_file = min_kml_file;
		    current_max_kml_file = max_kml_file;
		    

		    zoom_level='fine';
		}		
	    }
	    else
	    {
		//Was zoomed in, but am now zoomed out 
		if(zoom_level=='fine')
		{
		    //Was zoomed in
		    zoom_level='summary';
		    

		    //Get rid of any old overlays
		    if(typeof dataLayer != 'undefined')
		    {
			dataLayer.setMap(null);
		    }


		    //Put the colored polygons back on
		    draw_county_boundaries();
		    change_county_colours(kml_root);		    

		    //Put the scale bar back
		    document.getElementById("scale_div").style.display="block";
		}
	    }
	}
    }
}

