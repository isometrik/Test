<?php

/**
 * Copyright 2017 isometric
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

namespace Templatevar;

use stdClass;

class SaveFile
{
	
public function tpv_saveValue()
{
	$debug = new Debug;
	
    global $pth,$plugin_tx;
    $o = '';
	$tplValueChange = $_POST;
	// XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_post_tpv_saveValue34.json', json_encode($_POST));
	$debug->debugFile(__FILE__,__LINE__,$_POST);
	foreach ($tplValueChange as $key => $value) {
		if(strpos($key,"Key")!==false) {
		$changedValue['value'][]=$value;
		}
	}

    // SAVE to file
    //==============
	$tplConfig = json_decode(XH_readFile($pth['folder']['plugins'].'templateVar/config/variables.json'),true);
	// $tplConfig = (array) XH_decodeJson(XH_readFile($pth['folder']['plugins'].'templateVar/config/variables.json'));
	$replacements = $changedValue;
	$changedFile = array_replace($tplConfig, $replacements);
    	// $result = XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/variables.json', XH_encodeJson($changedFile));
			$result = XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/variables.json', json_encode($changedFile));
				if ($result) {
				$o .= XH_message('success', $plugin_tx['templateVar']['message_saved']);
			} else {
				$o .= XH_message('failure', $plugin_tx['templateVar']['message_not_saved']);
			}
	return $o;
}

// receiving and saving changes variables config
public function tpv_saveConfig()
{
    global $plugin_cf, $pth, $plugin_tx, $sl;
    $functions = new Functions;
	$debug = new Debug;
	
	
	$languages  = $functions->getLanguages();
	// $languages = templateVar_get_languages();
	$savefile = 'templateVar/config/variables.json';
	// $savefile = 'templateVar/config/test.json';
	$o = '';
	$tplValueChange = $_POST;
    
	$debug->debugFile(__FILE__,__LINE__ . 'post',$_POST);
	XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_post_tpv_saveConfig67.json', json_encode($_POST));
   
    $newtplVar['new']         =isset($_POST['tplVar']['new'])   	? $_POST['tplVar']['new']    	: array();
    $newtplVar['var']         =isset($_POST['tplVar']['var'])     	? $_POST['tplVar']['var']     	: array();
    $newtplVar['type']        =isset($_POST['tplVar']['type'])    	? $_POST['tplVar']['type']    	: array();
    $newtplVar['add']         =isset($_POST['tplVar']['add'])     	? $_POST['tplVar']['add']     	: array();
    $newtplVar['up']          =isset($_POST['tplVar']['up'])     	? $_POST['tplVar']['up']     	: array();
    $newtplVar['delete']      =isset($_POST['tplVar']['delete'])  	? $_POST['tplVar']['delete']  	: array();
    $newtplVar['options']     =isset($_POST['tplVar']['options']) 	? $_POST['tplVar']['options'] 	: array();
    $newtplVar['help_'.$sl]   =isset($_POST['tplVar']['help'])		? $_POST['tplVar']['help']		: array();
    /*/ $newtplVar['help']        =isset($_POST['tplVar']['help'])    	? $_POST['tplVar']['help']    	: array();
	$newtplVar['page']          =isset($_POST['tplVar']['page'])    ? $_POST['tplVar']['page']          : array();
	$newtplVar['value']         =isset($_POST['tplVar']['value'])   ? $_POST['tplVar']['value']         : array();
// */
	foreach (array(
	'page',
    'themes',
	'value',
	'activeTpl',
	'activeTpv'
	) as $value) {
		$newtplVar[$value]=isset($_POST['tplVar'][$value]) ? $_POST['tplVar'][$value]: array();
	}
   // load the old data to check for existing var
    //$tplVar = json_decode(file_get_contents($pth['folder']['plugins'].'templateVar/config/variables.json'),true);
    $tplVar = json_decode(XH_readFile($pth['folder']['plugins'].'templateVar/config/variables.json'),true);
	foreach ($languages as $language) {
			if ($language!=$sl) {
				$newtplVar['help_' . $language] =isset($tplVar['help_' . $language]) ? $tplVar['help_' . $language] : array();
			}
	}
	if($tplVar == null) $tplVar = array();

    // a little clean up
    foreach ($newtplVar['var'] as $key=>$value) {
        $newtplVar['var'][$key]        = preg_replace("/[^a-z_\-A-Z0-9]/", '', $value);
        if(!isset($newtplVar['options'][$key]))         $newtplVar['options'][$key]     = '';
        if(!isset($newtplVar['page'][$key]))            $newtplVar['page'][$key]        = '';
        //if(!isset($newtplVar['help'][$key]))            $newtplVar['help'][$key]         = '';
		if(!isset($newtplVar['help_'.$sl][$key]))       $newtplVar['help_'.$sl][$key]   = '';
		if(!isset($newtplVar['value'][$key]))    		$newtplVar['value'][$key]   	= '';
        
	foreach (array(
        'themes',
		'activeTpl',
		'activeTpv'
	) as $value) {
		if(!isset($newtplVar[$value][$key])) $newtplVar[$value][$key] = '';
	}
		$newtplVar['page'][$key]        = urldecode($newtplVar['page'][$key]);
		
		$newtplVar['options'][$key]     = stsl($newtplVar['options'][$key]);
        $newtplVar['page'][$key]        = stsl($newtplVar['page'][$key]);
	    $newtplVar['theme'][$key]       = stsl($newtplVar['theme'][$key]);
		$newtplVar['help_'. $sl][$key]  = stsl($newtplVar['help_'.$sl][$key]);
		$newtplVar['value'][$key]       = stsl($newtplVar['value'][$key]);

 // check if new variables are already in use by the system
        // select only new variables for the checking
        if(isset($newtplVar['new'][$key]) && $newtplVar['var'][$key]) {
            // initializing
            $refuse_var = $usedastplVar = $tmpadded = 0;
            // general check for collision with momentarily set variables
            if(isset(${$newtplVar['var'][$key]})) {$refuse_var ++;}
            
			// check for collision with any config variables
			$cf_array  = $plugin_cf['templateVar'];
			$myVar     = $newtplVar['var'][$key];
			if (array_key_exists($myVar, $cf_array)) {$refuse_var ++;} ;

            // check for collision with other templateVar variables
            //*/ 
			foreach ($tplVar['var'] as $key2 => $existing_var) {
            	if($existing_var == $newtplVar['var'][$key]) {
                    $usedastplVar ++;
                    if($tplVar['page'][$key2]) {
                        $tplarray = explode(',', $tplVar['page'][$key2]);
                        $i = 0;
                        foreach ($tplarray as $value) {
                        	if(trim($value) == $newtplVar['page'][$key]) $i++;
                        }
                        if(!$i) {
                            $tplVar['page'][$key2].=','.$newtplVar['page'][$key];
                            $tmpadded ++;
                        }
                    }
                }
            }
			// */
            if($refuse_var || $usedastplVar) {
                if($newtplVar['var'][$key]) { //no notice if var is empty
                    $o .= '<p class="cmsimplecore_warning"><b>$'.$newtplVar['var'][$key] . '</b> ';
                    $o .=  $usedastplVar
                       ?   $plugin_tx['templateVar']['error_already_templateVar_variable']
                       :   $plugin_tx['templateVar']['error_variable_already_used'].'</p>';
                    $o .=  $tmpadded
                       ?   "\n" . $plugin_tx['templateVar']['error_template_enabling_added'].'</p>'
                       :   '</p>';
                }
                // unset($newtplVar['var'][$key],$newtplVar['hr'][$key],$newtplVar['options'][$key],$newtplVar['help'][$key],$newtplVar['display'][$key],$newtplVar['type'][$key],$newtplVar['value'][$key]);
				unset($newtplVar['var'][$key],$newtplVar['options'][$key],$newtplVar['help'][$key],$newtplVar['type'][$key],$newtplVar['value'][$key]);
            }
        }
    }

    // DELETE
    //============
    $deletekey = array_search(TRUE, $newtplVar['delete']);
    echo $deletekey;

	if($deletekey) {
       $deletekey--;
        // now delete fields in templateVar arrays
        foreach ($newtplVar as $key=>$value) {
            unset($newtplVar[$key][$deletekey]);
        }
    }

    // ADD
    //==========
    $addkey = array_search(TRUE, $newtplVar['add']);
    if($addkey !== false) {
        foreach ($newtplVar as $key=>$value) {
                array_splice($newtplVar[$key],($addkey),0,'');
        }
    } 

    // Move UP
    //===============
    $upkey = array_search(TRUE, $newtplVar['up']);
    if($upkey > 0 || $upkey === 0) {
        foreach ($newtplVar as $key=>$value) {
            // extract values
            $moving_up = array_slice($newtplVar[$key],$upkey,1);
            // delete extracted values in the original array
            array_splice($newtplVar[$key],$upkey,1);
            // add extracted value higher into the original array
            if($upkey > 0) array_splice($newtplVar[$key],($upkey - 1),0,$moving_up);
            if($upkey === 0) array_splice($newtplVar[$key],count($newtplVar),0,$moving_up);
        }
    }

    // clean the array of data which is not needed in the final file
    unset($newtplVar['add'],$newtplVar['new'],$newtplVar['up'],$newtplVar['delete'],$newtplVar['help']);

    // SAVE to file
    //==============
	// $newtplVar = array_values($newtplVar);
	 foreach ($newtplVar as $key=>$value) {
            $newtplVar[$key]=array_values($newtplVar[$key]);
        }
/*/

		foreach ($newtplVar['var'] as &$value) {
        XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_value.json', json_encode($value));
		$haystack = $value;
		XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_haystack.json', json_encode($haystack));
		if (strpos ($haystack,'tpv_')=== false)  {    
			if (!empty($value)) {
				$value = 'tpv_'.$value;
			}
			echo $value.'<br>';
		}
		
        }
// */

		// str_replace ( $newtplVar['var'], 'tpv_'.$newtplVar['var'] , $newtplVar['var'] );
	XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_tpl.json', json_encode($newtplVar['var']));
	XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_post.json', json_encode($_POST));
	if (isset($_POST['tplVar']['up'])) {
    XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_post1.json', json_encode($_POST['tplVar']['up']));
	}
	$result = XH_writeFile($pth['folder']['plugins'] . $savefile, json_encode($newtplVar));
	$result = XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/variables.json', json_encode($newtplVar));
				if ($result) {
				$o .= XH_message('success', $plugin_tx['templateVar']['message_saved']);
			} else {
				$o .= XH_message('failure', $plugin_tx['templateVar']['message_not_saved']);
			}	 
    return $o;

}



}