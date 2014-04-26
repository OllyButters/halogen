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
function draw_base_map()
{

var latlng = new google.maps.LatLng(53.4,-2.23);
var myOptions = {
    zoom: 6,
    minZoom: 6,
    maxZoom: 12,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.HYBRID,
    scaleControl: true,
    streetViewControl: false,
    navigationControl: true, navigationControlOptions:{style: google.maps.NavigationControlStyle.DEFAULT}
    
};

map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
}

