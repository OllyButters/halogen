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
function local_map()
{
    var local_lat;
    var local_lon;
    var local_placename;

    //Figure out where we are - default to Leicester if we dont know.
    //if(google.loader.ClientLocation.latitude && google.loader.ClientLocation.longitude)
    if(google.loader.ClientLocation)
    {
	local_lat = google.loader.ClientLocation.latitude;
	local_lon = google.loader.ClientLocation.longitude;
	local_placename = google.loader.ClientLocation.address.city;
	local_country_code = google.loader.ClientLocation.address.country_code;

	//Make sure in UK
	if(local_country_code == "GB")
	{
	    //Roughly where Leicester is
	    local_lat = 52.634;
	    local_lon = -1.139;
	    local_placename = 'Leicester';
	}
    }
    else
    {
	//Roughly where Leicester is
	local_lat = 52.634;
	local_lon = -1.139;
	local_placename = 'Leicester';
    }

    var latLng = new google.maps.LatLng(local_lat, local_lon);

    var myOptions = {
	zoom:11,
	center: latLng,
	mapTypeId: google.maps.MapTypeId.HYBRID,
        scaleControl: false,
        streetViewControl: false,
        navigationControl: false
    };

    //Draw the map
    var map = new google.maps.Map(document.getElementById("map_container"),myOptions);

    //Sort out the caption
    document.getElementById("map_caption").innerHTML="Key to English place name data around "+local_placename+".";

    
    //Now query the DB and get some data to plot
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
		var returned_data=xmlhttp.responseText;//.split(",");                                          

		kml_root = returned_data;

		var this_kml='local_'+returned_data+'.kmz';
		var kml_url = 'http://'+document.domain+'/cache/'+this_kml;

		dataLayer = new google.maps.KmlLayer(kml_url, {preserveViewport:true});
		dataLayer.setMap(map);
	    }
    }

    url='http://'+document.domain+'/public/local_data.php?lat='+local_lat+'&lng='+local_lon;

    xmlhttp.open("GET",url,true);
    xmlhttp.send();
}



