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
use XH_Pages;

class Formular
{

public 	$fa = array (
    'delete'            => 'fa fa-trash-o fa-fw',
    'up'                => 'fa fa-arrow-up fa-fw',
    'add'               => 'fa fa fa-plus fa-fw',
    'href'              => 'fa fa-share-square-o fa-fw',
    'pagesline'         => 'fa fa-book fa-fw',
    'helpline'          => 'fa fa-question-circle fa-fw',
    'link_editvalue'    => 'fa fa-share-square-o fa-fw'
	);

    /**
    * @return string
    * above mainformular
    */
public function tplVar_navForm(){
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
public function tplVar_value(){
    global $cf, $c, $cl, $h, $hjs, $bjs, $l, $plugin, $plugin_cf, $plugin_tx, $pth, $sl, $sn, $su, $tx, $u, $txc;
    
    $debug = new Debug;
	$faIcon = $this->fa;	

    $tplVar = json_decode(XH_readFile($pth['folder']['plugins'].'templateVar/config/variables.json'),true);


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
    $w .= '<table id="tplVarValue" class="tplVar_tableValue">'
       .  "\n";

    // headline
    //=========
     $w .='<tr><td><a href="?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=config&amp;normal" '
       .  ' title="' . $plugin_tx['templateVar']['title_link_editcfg']. '"/>Werte</a></td></tr>';
    $w .= '<tr class="tplVar_darker">'
       . '<th title="'
       .  $plugin_tx['templateVar']['title_var_name']
       .  '">'
       .  $plugin_tx['templateVar']['theader_var_name']
       .  '</th>'
       . '<th>Test</th>'
       . '<th title="'
       .  $plugin_tx['templateVar']['title_help']
       .  '">'
       .  $plugin_tx['templateVar']['theader_help']
       .  '</th>'

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
                   .  '<input type="hidden" name="Key'   . $field . '" value="0">'
                   . '<input type="checkbox" id ="' . $field . '" name="Key' . $field . '" value="1"' . $checked .'>';
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
    if ($tplVar['activeTpv'][$key]) {
	// Type your code here

    $w .= '<tr">'
        . '<td>'
        . $value
        . '</td>'
        . '<td>'
        // auf welchen Seiten benutzt wird
        . '<td>' . $tplVar['page'][$key] . '</td>'
        // Buttons
        . '<div class="tplvar_buttons">'
        . $myHint . $myHelp
/*/

        . '<a href="?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=config&amp;normal"  title="' . $plugin_tx['templateVar']['title_link_editcfg']. '"/><i class="fa fa fa-edit fa-fw"></a>'

// */
        . '</div>'
        . '</td>'
        . '</tr>'."\n";
}        
    }
    $w .= '</table>';
    
    $html  = "\n\n<!-- templateVar Plugin -->\n";
    $html .= "\n\t".'<div class="tplVar_view">';

    $html .= "\n".'<form method="POST" action="' . $sn .'?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=save&amp;normal" id="templateVar" name="templateVar">';
    $html .= $w;
    $html .= "\n\t".'<input name="save_templateValue" type="hidden">';
    $html .= "\n\t".'<div style="text-align: right;">';
    $html .= "\n\t\t".'<input type="submit" value="'.ucfirst($tx['action']['save']).'"><br>';
    $html .= "\n\t".'</div>';
    $html .= "\n".'</form>';
    $html .= "\n\t".'</div>';
// end html tplVar_value
    return $html;
    }

public function tplVar_config() {
    global $cf, $c, $cl, $h, $hjs, $bjs, $l, $plugin_cf, $plugin, $plugin_tx, $pth, $sl, $sn, $su, $tx, $u, $txc;
	
	$faIcon = $this->fa;	

    $debug = new Debug;
    // $debug->debugFile(__FILE__,__LINE__,$this->$fa); 
    
    $functions = new Functions;
    $languages  = $functions->tplVar_get_languages();
    
    // XH_writeFile($pth['folder']['plugins'] . 'templateVar/config/00_languages.json', json_encode($languages));
    $html = '';
    // read the stored data
    $raw_tplVar = XH_readFile($pth['folder']['plugins'].'templateVar/config/variables.json');
    $tplVar = json_decode($raw_tplVar,true);


    // CMS is put into edit mode as 1.6 error message insist on edit mode for changing content, and
    // page data are content since 1.6
    $html  = "\n\n<!-- templateVar Plugin -->\n";
    $html .= "\n\t".'<div class="tplVar_view1">';
// start form
    $html .= '<form method="POST" action="' . $sn .  '?&templateVar&amp;admin=plugin_main&amp;action=save_cfg">'
       .  "\n".tag('input type="submit" value="' . ucfirst($tx['action']['save']) . '"'). ' ';
/*/
       
       .  tag('input type="image" src="'.$pth['folder']['plugins'].$plugin . '/images/add.gif" style="width:12px;height:12px" name="tplVar[add][0]" value="add" alt="Add entry" title="'
       .  $plugin_tx['templateVar']['title_add_on_top'].'"');


// */
    // start table for entering config data
    //=====================================
    $html .= '<table id="tplVar" class="tplVar_tableCfg">'
       .  "\n";

    // headline
    //=========
    $html .= '<tr class="tplVar_darker">'
       . '<th title="'
       .  $plugin_tx['templateVar']['title_var_name']
       .  '">'
       .  $plugin_tx['templateVar']['field_var']
       .  '</th>'
       . '<th title="'
       .  $plugin_tx['templateVar']['title_var_name']
       .  '">'
       .  'xx'
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

        $tplVar_type_select = '';
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
            $tplVar_type_select .= "<option value='$value'$selected>$value</option>";
        }

    // input data 1st row
    //===================
    $tplVar_up_title =  $key === 0
          ?  $plugin_tx['templateVar']['title_top_var_up']
          :  $plugin_tx['templateVar']['title_up'];
    $html .= '<tr>'."\n";

  // variable name
  // make $tplVar['var'] read only 
    $html   .= '<td>';
    $html   .= $tplVar['var'][$key]
            ? '<p class="tplVar_variable">'.$tplVar['var'][$key].'</p>'
            . '<input type="hidden" value="'.$tplVar['var'][$key].'" name="tplVar[var]['.$key.']"></td>'
            : '<input type="text" value="'.$tplVar['var'][$key].'" name="tplVar[var]['.$key.']">'
            . '<input type="hidden" value="true" name="tplVar[new]['.$key.']">'
            . '</td>';
    $html   .= '<td>';
    @$checked =($tplVar['activeTpv'][$key] == '1')? ' checked':'';
    
    $html .=  '<input type="hidden" name="tplVar[activeTpv]['.$key.']" value="0">'
       . '<input type="checkbox" id ="tplVar[activeTpv]['.$key.']" name="tplVar[activeTpv]['.$key.']" value="1"' . $checked .'>'
        . '</td>';

    $html .= '<td>'
     // make $tplVar['value'] read only
        .'<span class="tplVar_value">'.$tplVar['value'][$key].'</span>'
        . '<input type="hidden" value="'.$tplVar['value'][$key].'" name="tplVar[value]['.$key.']">'
        . '</td>';
    // choose type = functionality of variable
    $html .= '<td><select name="tplVar[type]['.$key.']" id="tplVar_type['.$key
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
            ; ">'.$tplVar_type_select.'</select></td>';
    // Buttons
    $html  .= '<td><ul class="tplvar_buttons">';
    $html  .='<li>'
        // up button  
        . '<button name="tplVar[up]['.$key.']" value="true" title="' . $tplVar_up_title . '"><span class="' . $faIcon['up'] .'"></span></button>'
        . '</li>';
    $html  .= '<li>'
    // delete button
          . '<button name="tplVar[delete]['.($key + 1).']" value="true" title="' . $plugin_tx['templateVar']['title_delete'] .'" '
          . 'onClick="return confirm(\'' . $plugin_tx['templateVar']['onklick_delete'] . '\')">'
          . '<span class="' . $faIcon['delete'] .'"></button>'
    .'</li>';
     $html .='<li>'
        // Add button
        . '<button name="tplVar[add]['.($key + 1).']" value="true" title="' .  $plugin_tx['templateVar']['title_add'] .'" '
		. 'onclick="myFunction(\'' .  $plugin_tx['templateVar']['title_add'] . '\')">'
        .'<span class="fa fa-plus fa-fw"></span></button>'
    .'</li>';
 $html  .='<li>'
         . '<img src="'.$pth['folder']['plugins'].$plugin
         . '/images/help.png" style="width:16px;height:16px;cursor:pointer;"'
		 . 'onclick="myHelpline(\'' . $key . '\', \'helpline\', \'helpline1\')" '
		 . 'title="' . $plugin_tx['templateVar']['title_help_line'] . '">'
. '</li>';    
    $html .='<li>'
        // buttons to show hidden line helpline
          . '<button type = "button" name="buttonhelpline['.$key.']" title="' . $plugin_tx['templateVar']['title_help_line'] .'" '
          . 'onClick="
            if(document.getElementById(\'helpline['.$key.']\').style.display == \'none\') {
                document.getElementById(\'helpline['.$key.']\').style.display = \'table-row\';
            } else {
                document.getElementById(\'helpline['.$key.']\').style.display = \'none\';
            }
            if(document.getElementById(\'helpline1['.$key.']\').style.display == \'none\') {
                document.getElementById(\'helpline1['.$key.']\').style.display = \'table-row\';
            } else {
                document.getElementById(\'helpline1['.$key.']\').style.display = \'none\';
            }
            ">'
          . '<span class="' . $faIcon['helpline'] .'"></button>'
        .'</li>';     
    $html  .='<li>'
           // buttons to show hidden line pagesline
           . '<button  type = "button" name="buttonpagesline['.$key.']"  title="' . $plugin_tx['templateVar']['title_template_line'] .'"'
           . 'onClick="
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
             ">'
           . '<span class="' . $faIcon['pagesline'] .'"></button>'
           .'</li>';                

 $html  .='<li>'
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
 . '</li>';


           $html  .='<li>'
            // Button - save
            . '<button name="save" value="true" title="' .  $plugin_tx['templateVar']['title_save'] . '"><span class="fa fa-save fa-fw"></span></button>'    
            . '</li>';
    $html  .='<li>'
            // Button - Link to value form
          . '<button name="link_editvalue" value="true" title="' . $plugin_tx['templateVar']['title_link_editvalue'] .'"'
          . 'onClick="location.href=\'?&templateVar&admin=plugin_main&action=plugin_text&normal\';">'
          . '<span class="' . $faIcon['link_editvalue'] .'"></button>'            
          .'</li>';
    $html .= '</ul>';
    $html .= '</td></tr>'."\n";

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
           .  '" name="tplVar[page]['.$key.']" id="tplVar_template['.$key.']" placeholder="'
           .  $plugin_tx['templateVar']['placeholder_template_list'].'">'  . "\n\t";
        $myLinklist = ((new Pages)->linkList());
		$pages = new Pages;
$myLinklist = $pages->linkList();
		$ol = '';
        foreach ($myLinklist as $value) {
            $ol .= '<option value="' . $value[1] . '">' . $value[0] . '</option>';

        }
        $html .= '<select class="page_select" id="select['.$key.']">'
           . '<option>Add an option:</option>' // onchange function pageselect
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

/*
		
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
						input.value +=  decodeURI(this.value); // gewählten Wert anhängen
					}
					});

    </script>';
	
// */
	/* /
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
						input.value +=  decodeURI(this.value); // gewählten Wert anhängen
					}
					});

    </script>';
// */ 
    
/*

    // end html tplVar_config
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
// */ 
	
/*/ **************** veraltet *******************
    
    $bjs .=    '<script>
        document.getElementById("select['.$key.']").onchange = function () {
            if (!this.selectedIndex) {
                return; // 1. Option gewählt => nix machen
            }
            var input = document.getElementById("tplVar_template['.$key.']");
            if (input.value) {
                input.value += ","; // falls schon was im <input> steht, dann Komma anhängen
            }
            input.value += this.value; // gewählten Wert anhängen
        }
    </script>';
    
// */
/*
	
	$bjs .= '<script>function myFunction(info) {
    var info = info;
	alert(info);
		}</script>';

// */
    $bjs .=    '<script>
	function myHelpline(key, helpline, helpline1) {
			var helpline  = helpline  + "[" + key + "]";
			var helpline1 = helpline1 + "[" + key + "]";
            if(document.getElementById(helpline).style.display == \'none\') {
                document.getElementById(helpline).style.display = \'table-row\';
            } else {
                document.getElementById(helpline).style.display = \'none\';
            }
            if(document.getElementById(helpline1).style.display == \'none\') {
                document.getElementById(helpline1).style.display = \'table-row\';
            } else {
                document.getElementById(helpline1).style.display = \'none\';
            }
	}
	</script>';
    $bjs .= '<script src="'.$pth['folder']['plugins'].'templateVar/js/tpv.js" type="text/javascript"></script>';
    $bjs .= '<script src="'.$pth['folder']['plugins'].'templateVar/js/autogrow.js" type="text/javascript"></script>';
    return $html;
}

public  function tplVar_get_languages()
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