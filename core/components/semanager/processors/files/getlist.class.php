<?php

class modSEManagerGetListOfFilesProcessor extends modObjectGetListProcessor {

    public $semanager = null;
    public $defaultSortField = 'name';

    /**
     * Get a collection of modChunk objects
     * @return array
     */
    public function getData() {
        $data = array();

        $this->modx->loadClass('semanager.SEManager');
        $this->semanager = new SEManager($this->modx);

        $data['results'] = $this->semanager->getNewFiles();
        $data['total'] = count($data['results']);

        return $data;

    }

    public function prepareRow($object) {
        return $object;
    }

}
return 'modSEManagerGetListOfFilesProcessor';