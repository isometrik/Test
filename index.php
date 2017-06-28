<?php

/**
 * Copyright 2017 isometric and Christoph M. Becker
 *
 * This file is part of TemplateVar_XH.
 *
 * TemplateVar_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TemplateVar_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TemplateVar_XH.  If not, see <http://www.gnu.org/licenses/>.
 */
$temp = new Templatevar\Plugin;
$temp->run();
$temp = null;

// Get the field arrays
$myFile = $pth['folder']['plugins'] . 'templateVar/config/variables.json';
$tplVarArray = (array) XH_decodeJson(XH_readFile($myFile));

function templateVar($myVar='')
 // returns the value of the variable
{
    global $plugin_cf, $pth, $tplVarArray;
	$key = array_search($myVar, $tplVarArray['var']);
    $o = '';
	$o .= $tplVarArray['value'][$key];
	return $o;
}

function templateVarPage($myPage='')
 // 
{
    global $pth, $tplVarArray;

	// $myPage = 'start';
	$show = 1;
	$pos  = 0;
	
    foreach ($tplVarArray['page'] as $value) {
		if ($myPage!='') $pos = strpos($value,$myPage);
		if ($pos === false) {
			$show = 0;
		} else {
		   $show = 1;
		   break;
		}
	}
	return $show;
}

function templateVarDate()
 // Checks whether the current date is between start date and end date
{
    global $plugin_cf, $pth, $tplVarArray;
	$date_start = strtotime($plugin_cf['templateVar']['date_start']);
	$date_end   = strtotime($plugin_cf['templateVar']['date_end']);
	$date       = time();
	$show = 0;
   if($date >= $date_start && $date <= $date_end){
		$show = 1;
	} else {
		$show = 0;
	}
	return $show;
}

function templateVarCheck($myVar='')
// Checks whether variable ist true or false
{
    global $plugin_cf, $pth, $tplVarArray;
	$myVar=$myVar;
	$varCheck = '';
	if (in_array($myVar, $tplVarArray['var'])) {
		$key = array_search($myVar, $tplVarArray['var']);
		$varCheck = $tplVarArray['value'][$key];
	}
    return $varCheck;
}

function templateImage($myImage='')
// Returns the image with path
{
	
	global $plugin_cf, $pth, $tplVarArray;
	
	$key = array_search($myImage, $tplVarArray['var']);
	switch ($tplVarArray['type'][$key]) {
			case 'template_image':
			case 'image_folder':
				$path = $tplVarArray['type'][$key]=='template_image'
					  ? $pth['folder']['template'].$plugin_cf['templateVar']['path_template_images']
					  : $pth['folder']['base'].$plugin_cf['templateVar']['path_image_folder'];
			break;
	}
    $image = '';
	$image .= $path . $tplVarArray['value'][$key];
    return $image;
}
