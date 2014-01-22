<?php

require_once MODX_CORE_PATH.'model/modx/processors/element/create.class.php';

class modSEManagerMakeElementFromFileProcessor extends modElementCreateProcessor {

    public function initialize() {

        $this->modx->log(E_ERROR, 'ffff');

        $pp = $this->getProperties();

        $this->modx->log(E_ERROR, json_encode($pp));

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