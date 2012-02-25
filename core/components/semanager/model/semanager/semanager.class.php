<?php
/**
 * SE Manager
 *
 * Copyright 2012 by Ivan Klimchuk <ivan@klimchuk.com>
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
 */
class SEManager
{
    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config=array()){
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('semanager.core_path',$config,$this->modx->getOption('core_path').'components/semanager/');
        $assetsUrl = $this->modx->getOption('semanager.assets_url',$config,$this->modx->getOption('assets_url').'components/semanager/');
        $connectorUrl = $assetsUrl.'connector.php';
        $connectorsUrl = $assetsUrl.'connectors/';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'imagesUrl' => $assetsUrl.'img/',

            'connectorUrl' => $connectorUrl,
            'connectorsUrl' => $connectorsUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'chunksPath' => $corePath.'elements/chunks/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',
        ),$config);

        $this->modx->lexicon->load('semanager:default');

        // Вывод ошибок debug only
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    }

    /**
     * Initializes SE Manager into different contexts.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     */
    public function initialize($ctx="web"){
        switch($ctx){
            case "mgr":

                $this->modx->regClientStartupScript($this->config['jsUrl'].'mgr/semanager.js');
                $this->modx->regClientStartupHTMLBlock('
                    <script type="text/javascript">
                        Ext.onReady(function() {
                            SEManager.config = '.$this->modx->toJSON($this->config).';
                            SEManager.config.connector_url = "'.$this->config['connectorUrl'].'";
                            SEManager.config.connectors_url = "'.$this->config['connectorsUrl'].'";
                            SEManager.action = "'.(!empty($_REQUEST['a']) ? $_REQUEST['a'] : 0).'";
                        });
                    </script>');
                $this->modx->regClientStartupScript($this->config['jsUrl'].'mgr/widgets/home.panel.js');
                $this->modx->regClientStartupScript($this->config['jsUrl'].'mgr/sections/home.js');
                $output = '<div id="semanager-panel-home-div"></div>';

                return $output;
                #return 'SE Manager admin';

                break;
            case "web":
                break;
            default:
                break;
        }

    }

}
