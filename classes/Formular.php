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
    'pagesline'         => 'fa fa-sitemap fa-fw',
    'helpline'          => 'fa fa-question-circle fa-fw',
    'themesline'        => 'fa fa-code fa-fw',
    'link_editvalue'    => 'fa fa-share-square-o fa-fw'
	);

    /**
    * @return string
    * above mainformular
    */
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
    // $debug->debugFile(__FILE__,__LINE__,$tplVar['var']);

    $table = '';
    // start table for entering value data
    //=====================================
    $table .= '<table id="tplVarValue" class="tpv_tableValue">' .  "\n";
    // headline
    //=========
    $table .='<tr><td><a href="?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=config&amp;normal" '
       .  ' title="' . $plugin_tx['templateVar']['title_link_editcfg']. '"/>Werte</a></td></tr>'."\n";
    $table .= '<tr class="tpv_darker">'
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

    $table  .= '<tr>'
            . '<td>'
            . $value
            . '</td>'
 
            // Buttons
            . '<td>'
            . '<div class="tpv_buttons">'
            . $myHint . $myHelp
            . '</div>'
            . '</td>';
    // Buttons
    $table  .= '<td><ul class="tpv_buttons">';  
    $table  .='<li>'
            // Button - save
            . '<button name="save" value="true" title="' .  $plugin_tx['templateVar']['title_save'] . '"><span class="fa fa-save fa-fw"></span></button>'    
            . '</li>';
    $table  .='<li>'
            // Button - Link to value form
            . '<button type = "button" name="link_editcfg" value="true" title="' . $plugin_tx['templateVar']['title_link_editcfg'] .'"'
            . 'onClick="location.href=\'?&templateVar&admin=plugin_main&action=config&normal\';">'
            . '<span class="' . $faIcon['link_editvalue'] .'"></button>'            
            .'</li>';
    $table  .= '</ul>'."\n"            
            . '</td>'
            . '</tr>'."\n";
    $table .= '<tr>'        
             // auf welchen Seiten die Variable benutzt wird
            . '<td colspan=3">' . $tplVar['page'][$key] . '</td>'
            .  '</tr>'."\n";
}        
    }
    $table .= '</table>'."\n";
/*/

    // Begin output html
    $html  = "\n\n<!-- templateVar Plugin -->\n";
    $html .= "\n\t".'<div class="tpv_view">';
    $html .= "\n".'<form method="POST" action="' . $sn .'?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=save&amp;normal" id="templateVar" name="templateVar">';
    $html .= $table;
    $html .= "\n\t".'<input name="save_templateValue" type="hidden">';
    $html .= "\n\t".'<div style="text-align: right;">';
    $html .= "\n\t\t".'<input type="submit" value="' . ucfirst($tx['action']['save']) . '"><br>';
    $html .= "\n\t".'</div>';
    $html .= "\n".'</form>';
    $html .= "\n\t".'</div>';
// end html tpv_value

// */
    $myAction = $sn .'?&amp;' . $plugin . '&amp;admin=plugin_main&amp;action=save_value&amp;normal';
    $html = $this->outputHtml($table, $myAction);
    return $html;
}

public function tpv_config() {
    global $cf, $c, $cl, $h, $hjs, $bjs, $l, $plugin_cf, $plugin, $plugin_tx, $pth, $sl, $sn, $su, $tx, $u, $txc;
	
	$faIcon = $this->fa;

    $debug = new Debug;
    // $debug->debugFile(__FILE__,__LINE__,$this->$fa); 
    
    $functions = new Functions;
    $languages  = $functions->getLanguages();
 

    // read the stored data
    $raw_tplVar = XH_readFile($pth['folder']['plugins'].'templateVar/config/variables.json');
    $tplVar = json_decode($raw_tplVar,true);

    // start table for entering config data
    //=====================================
    $table = '';
    $table .= '<table id="tplVar" class="tpv_tableCfg tpv_buttons">'
       .  "\n";

    // headline
    //=========
    $table .= '<tr class="tpv_darker">'
        .  '<th></th>'
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
     $i = 0;
    foreach ($tplVar['var'] as $key=>$value) {
            $z = 0;
            if($i == 0) $tr_class = ' first';
            if($i % 2 == 0 && $z < 5) {
                $tr_class = ' odd';
            }
            else {
                $tr_class = ' even';
            }
            $z++;
            
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
        $table .= '<tr class = "tpv_row1 ' . $tr_class . '">'."\n";
        $tpv_up_title =  $key === 0
              ?  $plugin_tx['templateVar']['title_top_var_up']
              :  $plugin_tx['templateVar']['title_up'];
      // variable name
      // make $tplVar['var'] read only 
        $table   .= '<td>'
                 . '</td>'."\n";
        $table   .= '<td>';
        $table   .= $tplVar['var'][$key]
                ? '<p class="tpv_variable">'.$tplVar['var'][$key].'</p>'
                . '<input type="hidden" value="'.$tplVar['var'][$key].'" name="tplVar[var]['.$key.']"></td>'
                : '<input type="text"   value="'.$tplVar['var'][$key].'" name="tplVar[var]['.$key.']">'
                . '<input type="hidden" value="true" name="tplVar[new]['.$key.']">'
                . '</td>'."\n";
        $table  .= '<td>';
        @$checked =($tplVar['activeTpv'][$key] == '1')? ' checked':'';
        
        $table .=  '<input type="hidden" name="tplVar[activeTpv]['.$key.']" value="0">'
               . '<input type="checkbox" id ="tplVar[activeTpv]['.$key.']" name="tplVar[activeTpv]['.$key.']" value="1"' . $checked .'>'
               . '</td>'."\n";

        $table .= '<td>'
         // make $tplVar['value'] read only
            .'<span class="tpv_value">'.$tplVar['value'][$key].'</span>'
            . '<input type="hidden" value="'.$tplVar['value'][$key].'" name="tplVar[value]['.$key.']">'
            . '</td>'."\n";
        // choose type = functionality of variable
        $table .= '<td><select name="tplVar[type]['.$key.']" id="tpv_type['.$key
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
                ; ">'.$tpv_type_select.'</select></td>';
    // Buttons
        $table  .= '<td><ul class="tpv_buttons">';
        $table  .='<li>'
            // up button  
            . '<button name="tplVar[up]['.$key.']" value="true" title="' . $tpv_up_title . '"><span class="' . $faIcon['up'] .'"></span></button>'
            . '</li>';
        $table  .= '<li>'
            // delete button
              . '<button name="tplVar[delete]['.($key + 1).']" value="true" title="' . $plugin_tx['templateVar']['title_delete'] .'" '
              . 'onClick="return confirm(\'' . $plugin_tx['templateVar']['onklick_delete'] . '\')">'
              . '<span class="' . $faIcon['delete'] .'"></button>'
        .'</li>';
         $table .='<li>'
            // Add button
            . '<button name="tplVar[add]['.($key + 1).']" value="true" title="' .  $plugin_tx['templateVar']['title_add'] .'">'
            .'<span class="fa fa-plus fa-fw"></span></button>'
        .'</li>';
        $table  .='<li>'
            // buttons to show hidden line helpline
                . '<button type = "button" name="buttonhelpline['.$key.']" '
                . 'title="' . $plugin_tx['templateVar']['title_help_line'] .'" '
                . 'onclick="tpvToggleLine(\'' . $key . '\', \'helpline0\', \'helpline1\')">'
    /*/
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

    // */
                . '<span class="' . $faIcon['helpline'] .'"></button>'
                .'</li>';     
        $table  .='<li>'
               // buttons to show hidden line pagesline
               . '<button type = "button" name="buttonpagesline['.$key.']" '
               . ' title="' . $plugin_tx['templateVar']['title_template_line'] .'" '
               . 'onclick="tpvToggleLine(\'' . $key . '\', \'pagesline0\', \'pagesline1\')">'
                . '<span class="' . $faIcon['pagesline'] .'"></button>'
               .'</li>';  
        $table  .='<li>'
               // buttons to show hidden line themesline
               . '<button type = "button" name="buttonthemesline['.$key.']" '
               . ' title="' . $plugin_tx['templateVar']['title_template_line'] .'" '
               . 'onclick="tpvToggleLine(\'' . $key . '\', \'themesline0\', \'themesline1\')">'
                . '<span class="' . $faIcon['themesline'] .'"></button>'
               .'</li>';                
        $table  .='<li>'
                // Button - save
                . '<button name="save" value="true" title="' .  $plugin_tx['templateVar']['title_save'] . '"><span class="fa fa-save fa-fw"></span></button>'    
                . '</li>';
        $table  .='<li>'
                // Button - Link to value form
              . '<button type = "button" name="link_editvalue" value="true" title="' . $plugin_tx['templateVar']['title_link_editvalue'] .'"'
              . 'onClick="location.href=\'?&templateVar&admin=plugin_main&action=plugin_text&normal\';">'
              . '<span class="' . $faIcon['link_editvalue'] .'"></button>'            
              .'</li>';
        $table .= '</ul>'."\n";
        $table .= '</td></tr>'."\n";

        $tableptionsline_visibility = ($tplVar['type'][$key] == 'option_list')
                    ? 'style="display:table-row"'
                    : 'style="display:none"';
        $helpline_visibility = $tplVar['help_'.$sl][$key]
                    ? 'style="display:table-row"'
                    : 'style="display:none"';
        $pagesline_visibility = $tplVar['page'][$key]
                    ? 'style="display:table-row"'
                    : 'style="display:none"';
        $themesline_visibility = $tplVar['page'][$key]
        ? 'style="display:table-row"'
        : 'style="display:none"';                    
                    
        $placeholder = ($tplVar['type'][$key] == 'color_picker')
                    ? $plugin_tx['templateVar']['placeholder_automatic_help']
                    : $plugin_tx['templateVar']['placeholder_help_field'];


        // input data row 2 
        //===================
        $table  .= '<tr class = "tpv_row2 ' . $tr_class . '" ' . $tableptionsline_visibility . ' id="options['.$key.']">' . "\n\t";
        $table  .= '<td>'
                . '</td>'."\n";        
        $table  .= '<td>'
                .  '</td>'."\n";
        $table  .=  '<td colspan="6">' . "\n\t"
                .  '<input type="text" style="width: 90%;" value="'.$tplVar['options'][$key]
                .  '" name="tplVar[options]['.$key.']" placeholder="'.$plugin_tx['templateVar']['placeholder_option_list'].'">' . '</td>'
                .  '</tr>' . "\n";

        // input data row 3 - help text
        //===================
        $table  .= '<tr class = "tpv_row3 helpline ' . $tr_class . '" ' . $helpline_visibility . '  id="helpline0['.$key.']">' . "\n\t";
        $table  .= '<td>'
                . '</td>'."\n";
        $table  .='<td colspan="6">' . "\n\t"
                . $plugin_tx['templateVar']['field_help_text'] .':'
                . '</td>'
                . '</tr>' . "\n";
                
            
    // */    
        // input data row 4 - help text
        //===================
        $table  .= '<tr class = "tpv_row4 helpline1 ' . $tr_class . '" ' . $helpline_visibility . ' id="helpline1['.$key.']">' . "\n\t";
        $table  .= '<td>'
        . '<button type = "button" name="buttonhelpline['.$key.']" title="' . $plugin_tx['templateVar']['title_help_line'] .'" '
        . 'onClick="alert(\'' . $plugin_tx['templateVar']['title_help_line'] . '\')">'
        . '<span class="' . $faIcon['helpline'] .'"></button>'
        . '</td>'."\n";        

        $table  .= '<td colspan="6">' . "\n\t"
           .  '<div class="expandingArea active"><pre><span></span><br></pre>'
           .  '<textarea style="width: 100%;" name="tplVar[help]['.$key.']" id="tplVar[help]['.$key.']" placeholder="'.$placeholder.'">'
           .  $tplVar['help_'.$sl][$key]
           .  '</textarea>'
           .  '</div>'
           .  '</tr>' . "\n";
           
        // input data row 5 - label pagesline  
        //===================
        $table .= '<tr class = "tpv_row5 pagesline' . $tr_class . '" ' . $pagesline_visibility . ' id="pagesline0['.$key.']">' . "\n\t";
        $table  .= '<td>'
                . '</td>'."\n";        
        $table  .= '<td colspan="6">' . $plugin_tx['templateVar']['field_template'] .': </td>'
           .  '</tr>' . "\n";
        // input data row 6 - pagesline
        //===================
        $table .= '<tr class = "tpv_row6 ' . $tr_class . '" '. $pagesline_visibility . ' id="pagesline1['.$key.']">' . "\n\t";
        $table  .= '<td>'
                . '<button type = "button" name="pagesline1['.$key.']" title="' . $plugin_tx['templateVar']['title_pages_line'] .'" '
                . 'onClick="alert(\'' . $plugin_tx['templateVar']['title_pages_line'] . '\')">'
                . '<span class="' . $faIcon['pagesline'] .'"></button>' 
                . '</td>'."\n";           
        $table  .= '<td colspan="6">' . "\n\t"
           .  '<input type="text" class="pages" value="' . $tplVar['page'][$key]
           .  '" name="tplVar[page]['.$key.']" id="tpv_template['.$key.']" placeholder="'
           .  $plugin_tx['templateVar']['placeholder_template_list'].'">'  . "\n\t";
        // $myLinklist = ((new Pages)->linkList());
        $pages = new Pages;
        $myLinklist = $pages->linkList();
        $ol = '';
        foreach ($myLinklist as $value) {
            $ol .= '<option value="' . $value[1] . '">' . $value[0] . '</option>'."\n";
        }
        $table .= '<select class="page_select" id="select['.$key.']">'
		   . '<option>Add an option:</option>'
		   . $ol;
		$table .= '</select>';
		$table .= '</td>'
               .  '</tr>' . "\n";
        
        // input data row 7 - label themesline  
        //===================
        $table .= '<tr class = "tpv_row7 themesline' . $tr_class . '" ' . $themesline_visibility . ' id="themesline0['.$key.']">' . "\n\t";
        $table  .= '<td>'
                . '</td>'."\n";        
        $table  .= '<td colspan="6">' . $plugin_tx['templateVar']['field_template'] .': </td>'
           .  '</tr>' . "\n";
        // input data row 8 - themesline
        //===================
        $table .= '<tr class = "tpv_row8 ' . $tr_class . '" '. $themesline_visibility . ' id="themesline1['.$key.']">' . "\n\t";
        $table  .= '<td>'
                . '<button type = "button" name="themesline1['.$key.']" title="' . $plugin_tx['templateVar']['title_themes_line'] .'" '
                . 'onClick="alert(\'' . $plugin_tx['templateVar']['title_themes_line'] . '\')">'
                . '<span class="' . $faIcon['themesline'] .'"></button>' 
                . '</td>'."\n";           
        $table  .= '<td colspan="6">' . "\n\t"
           .  '<input type="text" class="themes" value="' . $tplVar['themes'][$key]
           .  '" name="tplVar[themes]['.$key.']" id="tpv_template['.$key.']" placeholder="'
           .  $plugin_tx['templateVar']['placeholder_themes_list'].'">'  . "\n\t";
        
        $themes = array();

        foreach (XH_templates() as $theme) {
                $themes[] = $theme;
        }

        $myThemelist = $themes;
        $ol = '';
        foreach ($myThemelist as $value) {
            // $ol .= '<option value="' . $value[1] . '">' . $value[0] . '</option>'."\n";
            $ol .= '<option>' . $value . '</option>'."\n";
        }
        
        
        $table .= '<select class="theme_select" id="select['.$key.']">'
    // Text ersetzen
                . '<option>Add an option:</option>' // onchange function pageselect
                . $ol;
        $table  .='</select>';
        $table  .=  '</td>'
                .  '</tr>' . "\n";
        $i++;
    }
    unset($i, $z); 
    $table .= "\n".'</table>'."\n";
    
    $tpvActionCfg = $sn . '?&templateVar&amp;admin=plugin_main&amp;action=save_cfg';
    $html = $this->outputHtml($table, $tpvActionCfg);
    
    $bjs .= '<script src="'.$pth['folder']['plugins'].'templateVar/js/tpv.js" type="text/javascript"></script>';
    $bjs .= '<script src="'.$pth['folder']['plugins'].'templateVar/js/autogrow.js" type="text/javascript"></script>';

    return $html;
    }

      
    private function outputHtml($table, $action)
    {
        // global $cf, $c, $cl, $h, $hjs, $bjs, $l, $plugin_cf, $plugin, $plugin_tx, $pth, $sl, $sn, $su, $tx, $u, $txc;
        global $sn, $tx;
        
        $html  = "\n\n<!-- templateVar Plugin -->\n";
        $html .= "\n\t".'<div class="tpv_view">';
        $html .= '<form method="POST" action="'. $action .'">';
        $html .= "\n\t\t".'<input type="submit" value="' . ucfirst($tx['action']['save']) . '"><br>';
        $html .= $table;
        $html .= "\n".'<input type="hidden" value="true" name="save_cfg">'
              .  "\n".'<input type="submit" value="'.ucfirst($tx['action']['save']).'"><br>'
              .  "\n".'</form>'."\n";
        $html .= "\n\t".'</div>';
        return $html;
    }
}
/* //
    
// Begin output html
    $html  = "\n\n<!-- templateVar Plugin -->\n";
    $html .= "\n\t".'<div class="tpv_view1">';
    $html .= '<form method="POST" action="' . $sn .  '?&templateVar&amp;admin=plugin_main&amp;action=save_cfg">';
    $html .= "\n\t\t".'<input type="submit" value="' . ucfirst($tx['action']['save']) . '"><br>';
    $html .= $table;
    $html .=  "\n".'<input type="hidden" value="true" name="save_cfg">'
       .  "\n".'<input type="submit" value="'.ucfirst($tx['action']['save']).'"><br>'
       .  "\n".'</form>'."\n";
    $html .= "\n\t".'</div>';
// end html tpv_config

// */

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
// */ 
	
/*/ **************** veraltet *******************
    
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
/*/

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
 
// */  