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
var oldmap;

function add_map_overlay(map_type)
{
    if(map_type.match(/soil/))
	{
	    if(oldmap)
		{
		    oldmap.setMap(null);
		}
	    //This still isnt great - I think the projection makes it off a bit.
	    var imageBounds = new google.maps.LatLngBounds(new google.maps.LatLng(49.7443,-6.4586),new google.maps.LatLng(55.9279,2.3592));
	    var url = 'http://'+document.domain+'/public/overlays/soil.jpg';
	    oldmap = new google.maps.GroundOverlay(url,imageBounds);
	    oldmap.setMap(map);

	    document.getElementById("variable_copyright").innerHTML="<br/>Soil map overlay - &copy; B. W. Avery, D. C. Findlay and D. Mackney.";

	}
    else if(map_type.match(/roman_roads/))
	{
	    if(oldmap)
		{
		    oldmap.setMap(null);
		}
	    var imageBounds = new google.maps.LatLngBounds(new google.maps.LatLng(48.811094,-11.173251),new google.maps.LatLng(59.703128,2.117981));
	    var url = 'http://'+document.domain+'/public/overlays/roman_geo2.jpg';
	    oldmap = new google.maps.GroundOverlay(url,imageBounds);
	    oldmap.setMap(map);

	    document.getElementById("variable_copyright").innerHTML="<br/>Roman road overlay - &copy; www.british-towns.net";
	}
    else
	{
	    oldmap.setMap(null);
	    document.getElementById("variable_copyright").innerHTML="";
	}
}

