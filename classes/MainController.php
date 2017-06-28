<?php

/**
 * Basiccode Plugin Copyright 2017 Christoph M. Becker
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

class MainController
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var array
     */
    private $conf;

    /**
     * @var array
     */
    private $lang;

    /**
     * @var XH_CSRFProtection
     */
    private $csrfProtector;


    /**
     * @return string
     */
    public function defaultAction()
    {
        global $pth, $sn, $sl, $hjs;
        $html = '';
        $formular = new Formular;
        $formular->action = 'config';
        $formular->text   = 'Variablendefinition';
        $html .= 'defaultAction<br>';
        $html .= $formular->tpv_navForm();
        $html .= $formular->tpv_value();
        return $html;
    }

     public function configAction()
    {
       global $pth, $sn, $sl, $hjs;
        $html = '';
        $save = new SaveFile;
        $formular = new Formular;
        $formular->action = 'plugin_text';
        $formular->text   = 'Variablenwerte';
        $html .= 'configAction<br>';
        $html .= $formular->tpv_navForm();
        $html .= $formular->tpv_config();
        return $html;
    }

    /**
     * @return string
     */
    public function saveActionValue()
    {
        $html = '';
        $t    = '';
        $save = new SaveFile;
        $formular = new Formular;
        $formular->action = 'save_value';
        $formular->text   = 'Variabledefinition';
        $html .= 'saveActionValue<br>';
        $html .= $formular->tpv_navForm();
        
        // receiving and saving changes variables value
        $t .= $save->tpv_saveValue();
        $html .= $formular->tpv_value() . $t;
        return $html;
    }
    /**
     * @return string
     */
    public function saveActionCfg()
    {
        $html = '';
        $t    = '';
        $save = new SaveFile;
        $formular = new Formular;
        $formular->action = 'plugin_text';
        $formular->text   = 'Variablenwerte';
        $html .= 'saveActionCfg<br>';
        // $html .= $formular->tpv_navForm();
        if(isset($_POST['save_cfg']))  {
        // receiving and saving changes variables value
        $t .= $save->tpv_saveConfig();
        $html .= $t . $formular->tpv_config() . $t;
        }
    return $html;
    }
    
     /**
     * @return string
     */
    private function baseUrl()
    {
        global $sn;

        return 'http'
            . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
            . '://' . $_SERVER['HTTP_HOST']
            . preg_replace('/index\.php$/', '', $sn);
    }

    /**
     * @param bool $success
     * @param string $filename
     * @return string
     */
    private function saveMessage($success, $filename)
    {
        $type = $success ? 'success' : 'fail';
        return XH_message($type, $this->lang["message_save_{$type}"], $filename);
    }
    /**
    * @return string
    */

}