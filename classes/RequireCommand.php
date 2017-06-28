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

class RequireCommand
{
    /**
     * @var bool
     */
    private static $isEmitted = false;

    /**
     * @var string
     */
    private $pluginFolder;

    public function __construct()
    {
        global $pth;

        $this->pluginFolder = "{$pth['folder']['plugins']}templateVar/";
    }

    public function execute()
    {
        global $hjs;
    
        if (self::$isEmitted) {
            return;
        }
        self::$isEmitted = true;
    }
}
