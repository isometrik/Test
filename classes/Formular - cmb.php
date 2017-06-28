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

class Formular
{   
	/**
    * @return string
	* above mainformular
    */
public function tpv_navForm1(){
	global $plugin;
	$view = '';
	    $view  = "\n\n<!-- templateVar Plugin -->\n";
		$view .=  "\n" . '<a href="?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=' . $this->action . '" />' . $this->text . '</a>&nbsp; ';
	return $view;
	}
public function tpv_navForm(){
	global $plugin;
		$view  = '';
	    $view  = '<!-- templateVar Plugin -->';
		$view .= '<a href="?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=' . $this->action . '&amp;normal" />' . $this->text . '</a>';
	return $view;
	}
   /**
    * @return string
	* enter values for variables
    */
public function tpv_value(){
	global $cf, $c, $cl, $h, $hjs, $bjs, $l, $plugin, $plugin_cf, $plugin_tx, $pth, $sl, $sn, $su, $tx, $u, $txc;
	
	$debug = new Debug;

	$tplVar = json_decode(XH_readFile($pth['folder']['plugins'].'templateVar/config/variables.json'),true);
	$debug->debugFile(__FILE__,__LINE__,$tplVar);

	// load js color picker -- if needed
	if(in_array('color_picker',$tplVar['type'])) {
		$hjs .= '<script type="text/javascript" src="'.$pth['folder']['plugins'].'templateVar/js/jscolor/jscolor.js"></script>';
	}

	// load js for autogrowing input field -- if needed
	if(in_array('input_field',$tplVar['type'])) {
		$bjs .= '<script src="'.$pth['folder']['plugins'].'templateVar/js/autogrow.js" type="text/javascript"></script>';
	}
	$debug->debugFile(__FILE__,__LINE__,$tplVar['var']);

	$w = '';
	// start table for entering value data
	//=====================================
	$w .= '<table id="tplVarValue" class="tpv_tableValue">'
	   .  "\n";

	// headline
	//=========
	$w .= '<tr class="tpv_darker">'
	. '<th title="'
	   .  $plugin_tx['templateVar']['title_var_name']
	   .  '">'
	   .  $plugin_tx['templateVar']['theader_var_name']
	   .  '</th>'
	   . '<th title="'
	   .  $plugin_tx['templateVar']['title_help']
	   .  '">'
	   .  $plugin_tx['templateVar']['theader_help']
	   .  '</th>'
	   . '<th><a href="?&templateVar&admin=plugin_main&action=config&normal">Variablendefinition</a></th>'
	   .  '</tr>'."\n";
	foreach($tplVar['var'] as $key => $field){
			$value = '';
			switch ($tplVar['type'][$key]) {

			case 'color_picker':
			
				$value.= '<input type="text"  class="color" name="'
				   . 'Key'.$field
				   . '" id="'
				   . $field
				   . '" value="'
				   .  $tplVar['value'][$key]
				   . '">';
			break;
			case 'input_field':

				$value.=  '<div id="tplVar">'
				   . "\n" . ' <label for = "' . $field . '">$'. $tplVar['var'][$key] . ' </label>'
				   .'<div class="expandingArea active"><pre><span></span>'
				   .  '<br>'
				   .  '</pre>'
				   .  '<textarea style="width: 50%;" name="'
				   . 'Key'.$field
				   .  '" id="'
				   .  $field
				   .  '">'
				   .  $tplVar['value'][$key]
				   .  '</textarea>'
				   .  '</div></div>';
			break;


			case 'template_image':
			case 'image_folder':

				$path = $tplVar['type'][$key]=='template_image'
					  ? $pth['folder']['template'].$plugin_cf['templateVar']['path_template_images']
					  : $pth['folder']['base'].$plugin_cf['templateVar']['path_image_folder'];

				if(is_dir($path)){

					$handle=opendir($path);
					$images = array();
					while (false !== ($file = readdir($handle))) {
						if($file != "." && $file != "..") {
							$images[] = $file;
						}
					}
					closedir($handle);
					natcasesort($images);
					$images_select = '';
					foreach($images as $file){
						$selected = '';
						if($tplVar['value'][$key] == $file) {$selected = ' selected';}
						$images_select .= "\n" . '<option  value="'. $file . '"' . $selected . '> &nbsp; ' . $file . ' &nbsp; </option>';
					}
				} else $images_select='';

				$value.= "\n" . '<select name="Key1' . $field . '" >'
				   .  "\n" . '<option value=""> &nbsp; ' . $plugin_tx['templateVar']['default_selection'] . ' &nbsp; </option>'
				   .  "\n" . $images_select
				   .  "\n" . '</select>';
			break;

			case 'checkbox':
				@$checked =($tplVar['value'][$key] == '1')? ' checked':'';
				$value.= "\n\n\t"
				   .  tag('input type="hidden" name="Key'   . $field . '" value="0"')
				   .  tag('input type="checkbox" id ="' . $field . '" name="Key' . $field . '" value="1"' . $checked);
			break;

			case 'option_list':
				$options = explode('|', $tplVar['options'][$key]);
				$options_select = '';
				foreach($options as $j=>$value){
					$selected = '';
					if($value) { // check if $j even
						if($tplVar['value'][$key] == $value) {$selected = ' selected';}
						if(!$options[($j+1)]) $options[($j+1)] = $value;
						$options_select .= "\n"
										 . '<option value="'
										 . $value
										 . '"'
										 . $selected
										 . '> &nbsp; '
										 . $options[($j)]
										 . ' &nbsp; </option>';
					}
					$j++;
				}
				$value.= '<select name="Key'
				   . $field
				   . '" >'
				   .  "\n"
				   . '<option value=""> &nbsp; '
				   . $plugin_tx['templateVar']['default_selection']
				   . ' &nbsp; </option>'
				   .  "\n"
				   . $options_select
				   .  "\n"
				   . '</select>';
			break;
		}   
	   	if($tplVar['type'][$key] != 'input_field') {
				$value.=  "\n" . ' <label for = "' . $field . '">$'. $tplVar['var'][$key] . ' </label>' . "\n".tag('br');
			}
		$myHint = '';
		if($tplVar['type'][$key] == 'color_picker') {
				$myHint = '<button type ="button" title="' . $plugin_tx['templateVar']['hint_' . $tplVar['type'][$key]] . '"><span class="fa fa-question fa-fw"></span></button>';
			}
		$myHelp = '';
		if($tplVar['help_'.$sl][$key]) {
				$myHelp = '<button type ="button" title="' . $tplVar['help_'.$sl][$key] . '"><span class="fa fa-info fa-fw"></span></button>';
			}
	// start row for entering value data
	//=====================================
	   $w .= '<tr">'
		. '<td>'
		. $value
		. '</td>'
		. '<td>'
		// Buttons
		. '<div class="tplvar_buttons">'
		. $myHint . $myHelp
		. '<a href="?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=config&amp;normal"  title="' . $plugin_tx['templateVar']['title_link_editcfg']. '"/><i class="fa fa fa-edit fa-fw"></a>'
		. '</div>'
		. '</td>'
		. '<td></td>'
		. '</tr>'."\n";
	}
	$w .= '</table>';
	
	$html  = "\n\n<!-- templateVar Plugin -->\n";
	$html .= "\n\t".'<div class="tpv_view">';

	$html .= "\n".'<form method="POST" action="' . $sn .'?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=save&amp;normal" id="templateVar" name="templateVar">';
	$html .= $w;
	$html .= "\n\t".'<input name="save_templateValue" type="hidden">';
	$html .= "\n\t".'<div style="text-align: right;">';
	$html .= "\n\t\t".'<input type="submit" value="'.ucfirst($tx['action']['save']).'"><br>';
	$html .= "\n\t".'</div>';
	$html .= "\n".'</form>';
	$html .= "\n\t".'</div>';
// end html tpv_value
	return $html;
	}

public function tpv_config() {
	global $cf, $c, $cl, $h, $hjs, $bjs, $l, $plugin_cf, $plugin, $plugin_tx, $pth, $sl, $sn, $su, $tx, $u, $txc;
	
	$debug = new Debug;

	$functions = new Functions;
	$languages  = $functions->getLanguages();
	
	// XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_languages.json', json_encode($languages));
	$html = '';
	// read the stored data
	$raw_tplVar = XH_readFile($pth['folder']['plugins'].'templateVar/config/variables.json');
	$tplVar = json_decode($raw_tplVar,true);


	// CMS is put into edit mode as 1.6 error message insist on edit mode for changing content, and
	// page data are content since 1.6
	$html  = "\n\n<!-- templateVar Plugin -->\n";
	$html .= "\n\t".'<div class="tpv_view1">';
	$html .= '<form method="POST" action="' . $sn .  '?&templateVar&amp;admin=plugin_main&amp;action=save_cfg">'
	   .  "\n".tag('input type="submit" value="'.ucfirst($tx['action']['save']).'"'). ' ';
/*/
	   
	   .  tag('input type="image" src="'.$pth['folder']['plugins'].$plugin . '/images/add.gif" style="width:12px;height:12px" name="tplVar[add][0]" value="add" alt="Add entry" title="'
	   .  $plugin_tx['templateVar']['title_add_on_top'].'"');


// */
	// start table for entering config data
	//=====================================
	$html .= '<table id="tplVar" class="tpv_tableCfg">'
	   .  "\n";

	// headline
	//=========
	$html .= '<tr class="tpv_darker">'
	   . '<th title="'
	   .  $plugin_tx['templateVar']['title_var_name']
	   .  '">'
	   .  $plugin_tx['templateVar']['field_var']
	   .  '</th>'

	  . '<th title="'
	   .  $plugin_tx['templateVar']['title_var_name']
	   .  '">'
	   .  $plugin_tx['templateVar']['field_value']
	   .  '</th>'
	   
	   .  '<th title="'
	   .  $plugin_tx['templateVar']['title_var_type']
	   .  '">'
	   .  $plugin_tx['templateVar']['field_type']
	   .  '</th>'
	   .  '<th></th>'
	   .  '</tr>'."\n";


	// prepare option list for variable type
	foreach ($tplVar['var'] as $key=>$value) {

		$tpv_type_select = '';
		foreach (array(
			'checkbox',
			'color_picker',
			'image_folder',
			'input_field',
			'option_list',
			'template_image'
			) as $value) {

			$selected = '';
			if($tplVar['type'][$key] == $value) {$selected = ' selected';}
			$tpv_type_select .= "<option value='$value'$selected>$value</option>";
		}

		// input data 1st row
		//===================
			   $tpv_up_title =  $key === 0
				  ?  $plugin_tx['templateVar']['title_top_var_up']
				  :  $plugin_tx['templateVar']['title_up'];
			$html .= '<tr>'."\n"
		   .  '<td>';
		// variable name
/*/
		if ($plugin_cf['templateVar']['admin_editmode']) {
		$html .= '<input type="text" value="'.$tplVar['var'][$key].'" name="tplVar[var]['.$key.']">'
			  .  '<input type="hidden" value="true" name="tplVar[new]['.$key.']">'
		.  '</td>';
		}
		else {
// */
		// make $tplVar['var'] read only 
		$html .= $tplVar['var'][$key]
			? '<p class="tpv_variable">'.$tplVar['var'][$key].'</p>'
			. '<input type="hidden" value="'.$tplVar['var'][$key].'" name="tplVar[var]['.$key.']">'
			: '<input type="text" value="'.$tplVar['var'][$key].'" name="tplVar[var]['.$key.']">'
			. '<input type="hidden" value="true" name="tplVar[new]['.$key.']">'
			. '</td>';
/*/
		}
// */
		$html .= '<td>'
		 // make $tplVar['value'] read only
			.'<span class="tpv_value">'.$tplVar['value'][$key].'</span>'
			. '<input type="hidden" value="'.$tplVar['value'][$key].'" name="tplVar[value]['.$key.']"">'
			. '</td>';

		// choose type = functionality of variable
		$html .= '<td><select name="tplVar[type]['.$key.']" id="tpv_type['.$key
		   .  ']" style="width:96%" OnChange="
				if(this.options[this.selectedIndex].value == \'option_list\') {
					document.getElementById(\'options['.$key.']\').style.display = \'table-row\';
				} else {
					document.getElementById(\'options['.$key.']\').style.display = \'none\';
				}
				if(this.options[this.selectedIndex].value == \'color_picker\') {
					document.getElementById(\'help['.$key.']\').placeholder = \''.$plugin_tx['templateVar']['placeholder_automatic_help'].'\';
				} else {
					document.getElementById(\'help['.$key.']\').placeholder = \''.$plugin_tx['templateVar']['placeholder_help_field'].'\';
				}
				; ">'.$tpv_type_select.'</select></td>'

			. '<td>'
			// Buttons
			. '<div class="tplvar_buttons">'
			// up button  
			. '<button name="tplVar[up]['.$key.']" value="true" title="' . $tpv_up_title . '"><span class="fa fa-arrow-up fa-fw"></span></button>';
			if ($plugin_cf['templateVar']['admin_editmode']) {
			// delete button
			$html .=  '<button name="tplVar[delete]['.($key + 1).']" value="true" title="' . $plugin_tx['templateVar']['title_delete'].'"'
				  . 'onClick="return confirm(\'' . $plugin_tx['templateVar']['onklick_delete'] . '\')"><span class="fa fa-trash-o fa-fw"></span></button>';
				}
			// Add button
			$html .= '<button name="tplVar[add]['.($key + 1).']" value="true" title="' .  $plugin_tx['templateVar']['title_add'].'">'
				  .'<span class="fa fa-plus fa-fw"></span></button>'

			// buttons to show hidden lines
		   .  '<img src="'.$pth['folder']['plugins'].$plugin
		   .  '/images/help.png" style="width:16px;height:16px;cursor:pointer;"
				OnClick="
				if(document.getElementById(\'helpline['.$key.']\').style.display == \'none\') {
					document.getElementById(\'helpline['.$key.']\').style.display = \'table-row\';
				} else {
					document.getElementById(\'helpline['.$key.']\').style.display = \'none\';
				}
				if(document.getElementById(\'helpline1['.$key.']\').style.display == \'none\') {
					document.getElementById(\'helpline1['.$key.']\').style.display = \'table-row\';
				} else {
					document.getElementById(\'helpline['.$key.']\').style.display = \'none\';
				}
				" title="'
			. $plugin_tx['templateVar']['title_help_line'].'">'

		   // 
		   . '<img src="'.$pth['folder']['plugins'].$plugin
		   .  '/images/template.gif" style="width:13px;height:16px;cursor:pointer;"
				OnClick="
				if(document.getElementById(\'pagesline['.$key.']\').style.display == \'none\') {
					document.getElementById(\'pagesline['.$key.']\').style.display = \'table-row\';
				} else {
					document.getElementById(\'pagesline['.$key.']\').style.display = \'none\';
				}
				if(document.getElementById(\'pagesline1['.$key.']\').style.display == \'none\') {
					document.getElementById(\'pagesline1['.$key.']\').style.display = \'table-row\';
				} else {
					document.getElementById(\'pagesline1['.$key.']\').style.display = \'none\';
				}
				" title="'
				. $plugin_tx['templateVar']['title_template_line'].'">'
		. '<a href="?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=plugin_text&amp;normal" title="' . $plugin_tx['templateVar']['title_link_editvalue']. '"/><i class="fa fa fa-edit fa-fw"></a>'
			    . '</div>'
				. '</td></tr>'."\n";

		$htmlptionsline_visibility = ($tplVar['type'][$key] == 'option_list')
					? 'style="display:table-row"'
					: 'style="display:none"';
		$helpline_visibility = $tplVar['help_'.$sl][$key]
					? 'style="display:table-row"'
					: 'style="display:none"';
		$pagesline_visibility = $tplVar['page'][$key]
					? 'style="display:table-row"'
					: 'style="display:none"';
		$placeholder = ($tplVar['type'][$key] == 'color_picker')
					? $plugin_tx['templateVar']['placeholder_automatic_help']
					: $plugin_tx['templateVar']['placeholder_help_field'];

		// input data row 2
		//===================
		$html .= '<tr '.$htmlptionsline_visibility.' id="options['.$key.']">' . "\n\t"
		   .  '<td colspan="4">' . "\n\t"
		   .  '<input type="text" style="width: 90%;" value="'.$tplVar['options'][$key]
		   .  '" name="tplVar[options]['.$key.']" placeholder="'.$plugin_tx['templateVar']['placeholder_option_list'].'">' . '</td>'
		   .  '</tr>' . "\n";

		// input data row 3 - help text
		//===================
		$html .= '<tr '.$helpline_visibility.' class="helpline" id="helpline['.$key.']">' . "\n\t"
		   .  '<td colspan="4">' . "\n\t"
		   .  $plugin_tx['templateVar']['field_help_text'] .':'
		   .  '</td>'
		   .  '</tr>' . "\n";
		// input data row 4 - help text
		//===================
		$html .= '<tr '.$helpline_visibility.' class="helpline1" id="helpline1['.$key.']">' . "\n\t"
		   .  '<td colspan="4">' . "\n\t"
		   .  '<div class="expandingArea active"><pre><span></span><br></pre>'
		   .  '<textarea style="width: 100%;" name="tplVar[help]['.$key.']" id="help['.$key.']" placeholder="'.$placeholder.'">'
		   .  $tplVar['help_'.$sl][$key]
		   .  '</textarea>'
		   .  '</div>'
		   .  '</tr>' . "\n";
		// input data row 5
		//===================
		$html .= '<tr '.$pagesline_visibility.' class="pagesline" id="pagesline['.$key.']">' . "\n\t"
		   .  '<td colspan="4">' . $plugin_tx['templateVar']['field_template'] .': </td>'
		   .  '</tr>' . "\n";
		// input data row 6
		//===================
		   $html .= '<tr '.$pagesline_visibility.' id="pagesline1['.$key.']">' . "\n\t"
		   .  '<td colspan="4">' . "\n\t"
		   .  '<input type="text" class="pages" value="' . $tplVar['page'][$key]
		   .  '" name="tplVar[page]['.$key.']" id="tpv_template['.$key.']" placeholder="'
		   .  $plugin_tx['templateVar']['placeholder_template_list'].'">'  . "\n\t";
		$myLinklist = ((new Pages)->linkList());
		$ol = '';
		foreach ($myLinklist as $value) {
			$ol .= '<option value="' . $value[1] . '">' . $value[0] . '</option>';

		}
		$html .= '<select class="page_select" id="select['.$key.']">'
		   . '<option>Add an option:</option>'
		   . $ol;
		$html .='</select>';
		$html .=  '</td>'
		   .  '</tr>' . "\n";
	}

	$html .= "\n".'</table>';

	$html .=  "\n".'<input type="hidden" value="true" name="save_cfg">'
	   .  "\n".'<input type="submit" value="'.ucfirst($tx['action']['save']).'"><br>'
	   .  "\n".'</form>'."\n";
	$html .= "\n\t".'</div>';   
// end html tpv_config
	$bjs .=    '<script>
Array.prototype.forEach.call(document.getElementsByClassName("page_select"), function (select) {
    select.onchange = function () {
        if (!this.selectedIndex) {
            return; // 1. Option gewählt => nix machen
        }
        var input = this.previousElementSibling; // Caveat: <input> must come immediately before <select>
        if (input.value) {
            input.value += ","; // falls schon was im <input> steht, dann Komma anhängen
        }
        input.value += this.value; // gewählten Wert anhängen
    }
});
    </script>';
/*/
	
	$bjs .=    '<script>
        document.getElementById("select['.$key.']").onchange = function () {
            if (!this.selectedIndex) {
                return; // 1. Option gewählt => nix machen
            }
            var input = document.getElementById("tpv_template['.$key.']");
            if (input.value) {
                input.value += ","; // falls schon was im <input> steht, dann Komma anhängen
            }
            input.value += this.value; // gewählten Wert anhängen
        }
    </script>';
	
// */
	
	$bjs .= '<script src="'.$pth['folder']['plugins'].'templateVar/js/autogrow.js" type="text/javascript"></script>';
	return $html;
}

public  function getLanguages()
{
	if ( isset ( $plugin_cf['templateVar']['languages'] ) ) {
		$languages = explode(',', $plugin_cf['templateVar']['languages']);
	} else {
		$languages = explode(',', "en, de");
	}
	$languages = array_map('trim', $languages);
	return $languages;
}
	

}