<?php
/*
 * SE Manager
 *
 * Copyright 2012 by LOVATA Group <info@lovata.com>
 *
 * SE Manager is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * SE Manager is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * SE Manager; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package semanager
 * @subpackage controllers
 */

class SEManagerHomeControllerManagerController extends SEManagerManagerController {

    public function process(array $scriptProperties = array()){}

    public function getPageTitle() {
        return $this->modx->lexicon('semanager.title');
    }

    public function getTemplateFile() {
        return $this->semanager->config['templatesPath'].'home.tpl';
    }

    public function loadCustomCssJs() {
        $this->addCss($this->semanager->config['cssUrl'].'semanager.css');
        $this->addJavascript($this->semanager->config['jsUrl'].'widgets/elements.grid.js');
        $this->addJavascript($this->semanager->config['jsUrl'].'widgets/files.grid.js');
        $this->addJavascript($this->semanager->config['jsUrl'].'widgets/common.tab.js');

        $this->addJavascript($this->semanager->config['jsUrl'].'widgets/home.panel.js');
        $this->addJavascript($this->semanager->config['jsUrl'].'sections/home.js');
        $this->addHtml('<script>
             Ext.onReady(function() {
                 MODx.load({ xtype: "semanager-page-home"});
             });
         </script>');
    }

}
