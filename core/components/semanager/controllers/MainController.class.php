<?php

require_once dirname(dirname(__FILE__)) . '/model/semanager/semanager.class.php';

abstract class SEManagerManagerController extends modExtraManagerController {

    /** var SEManager $semanager */
    public $semanager;

    public function initialize(){
        $this->semanager = new SEManager($this->modx);

        $this->addCss($this->semanager->config['cssUrl'].'semanager.css');
        $this->addJavascript($this->semanager->config['jsUrl'].'semanager.js');

        $this->addHtml('
            <script>
                Ext.onReady(function(){
                    SEManager.config = '.$this->modx->toJSON($this->semanager->config).';
                });
            </script>
        ');

        return parent::initialize();
    }

    public function getLanguageTopics() {
        return array('semanager:default');
    }


}
