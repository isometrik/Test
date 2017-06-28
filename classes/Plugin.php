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

class Plugin
{
    const VERSION = '0.1 beta';

    public function run()
    {
        global $plugin_cf;

        if (XH_ADM) {
            XH_registerStandardPluginMenuItems(true);
            if (XH_wantsPluginAdministration('templateVar')) {
                $this->handlePluginAdministration();
            }
        }
    }

    private function handlePluginAdministration()
    {
        global $o, $action, $admin;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                $o .= $this->info();
				break;
			case 'plugin_main':
                    $controller = new MainController;
                    ob_start();
					switch ($action) {
                        case 'plugin_text':
							$o .= $this->plugintext();
							$o .= $controller->defaultAction();
							break;
                        case 'edit':
							$o .= $this->plugintext();
							$o .= $controller->editAction();
                            break;
						 case 'config':
							$o .= $this->plugintext();
							$o .= $controller->configAction();
                            break;
                        case 'save_value':
							$o .= $this->plugintext();
							$o .= $controller->saveActionValue();
                            break;
                        case 'save_cfg':
							$o .= $this->plugintext();
							$o .= $controller->saveActionCfg();
                            break;
                    }
                     $o .= ob_get_clean();
					
                    break;
            default:
                $o .= plugin_admin_common($action, $admin, 'templateVar');
        }
    }

    private function info()
    {
        global $title, $pth;

        $title = 'TemplateVar';
        $view = new View('info');
        $view->logo = "{$pth['folder']['plugins']}templateVar/template-icon.png";
        $view->version = self::VERSION;
        $checkService = new SystemCheckService;
        $view->checks = $checkService->getChecks();
        return $view;
    }
	private function plugintext()
    {
        global $title, $pth, $plugin_tx;

        $title = 'TemplateVar';
        $view = new View('main');
        $view->logo = "{$pth['folder']['plugins']}templateVar/template-icon.png";
		$view->versuch = $plugin_tx['templateVar']['templateVar_info'];
        return $view;
    }
}
