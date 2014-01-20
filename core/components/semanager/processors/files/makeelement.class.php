<?php

require_once MODX_CORE_PATH.'model/modx/processors/element/create.class.php';

class modSEManagerMakeElementFromFileProcessor extends modElementCreateProcessor {


    /*
    public $classKey = 'modSnippet';
    public $languageTopics = array('snippet','category','element');
    public $permission = 'new_snippet';
    public $elementType = 'snippet';
    public $objectType = 'snippet';
    public $beforeSaveEvent = 'OnBeforeSnipFormSave';
    public $afterSaveEvent = 'OnSnipFormSave';
    */

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {

        $this->modx->log(E_ERROR, 'ffff');

        $pp = $this->getProperties();

        $this->modx->log(E_ERROR, json_encode($pp));

        //$this->object = $this->modx->newObject($this->classKey);
        //return true;
        return false;
    }

    /**
     * Cleanup the process and send back the response
     * @return array
     */
    public function cleanup() {
        $this->clearCache();
        $fields = array('id', 'description', 'locked', 'category');
        array_push($fields,($this->classKey == 'modTemplate' ? 'templatename' : 'name'));

        $this->modx->log(E_ERROR, $fields);



        //return $this->success('',$this->object->get($fields));
    }

}
return 'modSEManagerMakeElementFromFileProcessor';