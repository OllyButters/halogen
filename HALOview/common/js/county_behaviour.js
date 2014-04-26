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
function add_county_clickable(){
google.maps.event.addListener(regions['NTB'], 'click', function(e){map_sel('NTB');});
google.maps.event.addListener(regions['CMB'], 'click', function(e){map_sel('CMB');});
google.maps.event.addListener(regions['DRH'], 'click', function(e){map_sel('DRH');});
google.maps.event.addListener(regions['WML'], 'click', function(e){map_sel('WML');});
google.maps.event.addListener(regions['YON'], 'click', function(e){map_sel('YON');});
google.maps.event.addListener(regions['LNC'], 'click', function(e){map_sel('LNC');});
google.maps.event.addListener(regions['YOW'], 'click', function(e){map_sel('YOW');});
google.maps.event.addListener(regions['YOE'], 'click', function(e){map_sel('YOE');});
google.maps.event.addListener(regions['CHE'], 'click', function(e){map_sel('CHE');});
google.maps.event.addListener(regions['NTT'], 'click', function(e){map_sel('NTT');});
google.maps.event.addListener(regions['DRB'], 'click', function(e){map_sel('DRB');});
google.maps.event.addListener(regions['NFK'], 'click', function(e){map_sel('NFK');});
google.maps.event.addListener(regions['SHR'], 'click', function(e){map_sel('SHR');});
google.maps.event.addListener(regions['LEI'], 'click', function(e){map_sel('LEI');});
google.maps.event.addListener(regions['RUT'], 'click', function(e){map_sel('RUT');});
google.maps.event.addListener(regions['WAR'], 'click', function(e){map_sel('WAR');});
google.maps.event.addListener(regions['NTP'], 'click', function(e){map_sel('NTP');});
google.maps.event.addListener(regions['WOR'], 'click', function(e){map_sel('WOR');});
google.maps.event.addListener(regions['HRE'], 'click', function(e){map_sel('HRE');});
google.maps.event.addListener(regions['BDF'], 'click', function(e){map_sel('BDF');});
google.maps.event.addListener(regions['BUC'], 'click', function(e){map_sel('BUC');});
google.maps.event.addListener(regions['OXF'], 'click', function(e){map_sel('OXF');});
google.maps.event.addListener(regions['ESX'], 'click', function(e){map_sel('ESX');});
google.maps.event.addListener(regions['GLO'], 'click', function(e){map_sel('GLO');});
google.maps.event.addListener(regions['HRT'], 'click', function(e){map_sel('HRT');});
google.maps.event.addListener(regions['BRK'], 'click', function(e){map_sel('BRK');});
google.maps.event.addListener(regions['MDX'], 'click', function(e){map_sel('MDX');});
google.maps.event.addListener(regions['WLT'], 'click', function(e){map_sel('WLT');});
google.maps.event.addListener(regions['GTL'], 'click', function(e){map_sel('GTL');});
google.maps.event.addListener(regions['KNT'], 'click', function(e){map_sel('KNT');});
google.maps.event.addListener(regions['SOM'], 'click', function(e){map_sel('SOM');});
google.maps.event.addListener(regions['SUR'], 'click', function(e){map_sel('SUR');});
google.maps.event.addListener(regions['HMP'], 'click', function(e){map_sel('HMP');});
google.maps.event.addListener(regions['DEV'], 'click', function(e){map_sel('DEV');});
google.maps.event.addListener(regions['DOR'], 'click', function(e){map_sel('DOR');});
google.maps.event.addListener(regions['CNW'], 'click', function(e){map_sel('CNW');});
google.maps.event.addListener(regions['IOW'], 'click', function(e){map_sel('IOW');});
google.maps.event.addListener(regions['LIN'], 'click', function(e){map_sel('LIN');});
google.maps.event.addListener(regions['SSX'], 'click', function(e){map_sel('SSX');});
google.maps.event.addListener(regions['HNT'], 'click', function(e){map_sel('HNT');});
google.maps.event.addListener(regions['STF'], 'click', function(e){map_sel('STF');});
google.maps.event.addListener(regions['CAM'], 'click', function(e){map_sel('CAM');});
google.maps.event.addListener(regions['SFK'], 'click', function(e){map_sel('SFK');});
}function map_sel(county_code){
var num_sels=document.query.county.length;
var this_index;
var all_index;
for(i=0; i<num_sels; i++)
{
if(document.query.county[i].value==county_code)
{
this_index=i;
}
if(document.query.county[i].value=='all')
{
all_index=i;
}
}
var this_selected=document.query.county[this_index].selected;
var all_selected=document.query.county[all_index].selected;
if(Boolean(all_selected))
{
for(i=0; i<num_sels; i++)
{
var this_region=document.query.county[i].value;
if(this_region=='null' || this_region=='all')
{continue;}
var temp='regions[\''+this_region+'\'].setOptions({fillColor:"#000000"})';
eval(temp);
}
}
if(Boolean(this_selected))
{
document.query.county[this_index].selected='';
var temp='regions[\''+county_code+'\'].setOptions({fillColor:"#000000"})';
eval(temp);
var any_selected=0;
for(i=0; i<num_sels; i++)
{
if(document.query.county[i].selected=='1')
{
any_selected=1;
}
}
if(any_selected==0)
{
document.query.county[0].selected='1';
}
}else{
document.query.county[this_index].selected='1';
var temp='regions[\''+county_code+'\'].setOptions({fillColor:"#0000ff"})';
eval(temp);
var this_selected=document.query.county[0].selected;
if(Boolean(this_selected))
{
document.query.county[0].selected='';
}
}
}
function list_sel(){
var num_sels=document.query.county.length;
for(i=0; i<num_sels; i++)
{
var this_selected=document.query.county[i].selected;
var this_region=document.query.county[i].value;
if(this_region=='null')
{continue;}
if(this_region=='all')
{
if(!Boolean(this_selected))
{continue;}
for(j=0; j<num_sels; j++)
{
var this_region=document.query.county[j].value;
if(this_region=='null' || this_region=='all')
{continue;}
var temp='regions[\''+this_region+'\'].setOptions({fillColor:"#0000ff"})';
eval(temp);
}
break;
}
if(Boolean(this_selected))
{
var temp='regions[\''+this_region+'\'].setOptions({fillColor:"#0000ff"})';
eval(temp);
}else{
var temp='regions[\''+this_region+'\'].setOptions({fillColor:"#000000"})';
eval(temp);
}
}
}
