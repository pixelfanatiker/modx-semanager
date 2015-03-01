<?php

class modSEManagerGetListOfElementsProcessor extends modObjectGetListProcessor {
    //public $permission = '';
    public $defaultSortField = 'name';

    /**
     * Get a collection of modChunk objects
     * @return array
     */
    public function getData() {
        $data = array();

        $nf = $this->getProperty('namefilter');
        $cf = $this->getProperty('categoryfilter');

        $type = $this->getProperty('type');
        $this->classKey = 'mod'.ucfirst($type);

        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);

        if(!empty($nf)){
            $key_filter = ($this->classKey=='modTemplate')?'templatename':'name';
            $c->where(array($key_filter.':LIKE'=>'%'.$nf.'%'));
        }

        if(!empty($cf)){
            $c->where(array('category'=>$cf));
        }

        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey,$c);
        $c = $this->prepareQueryAfterCount($c);

        $sortField = $this->getProperty('sort');
        $sortField = ($sortField == 'name' and $this->classKey=='modTemplate')?'templatename':'name';

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey,$this->getProperty('sortAlias',$sortClassKey),'',array($sortField));
        if (empty($sortKey)) $sortKey = $sortField;
        //$c->sortby($sortKey,$this->getProperty('dir'));

        $c->sortby('static', 'ASC');

        if ($limit > 0) {
            $c->limit($limit,$start);
        }

        $data['results'] = $this->modx->getCollection($this->classKey, $c);
        $data['results'] = $this->checkElementIfIsChanged($data['results']);

        foreach ($data['results'] as $result) {
            $resultItem = $result->toArray();
            //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [getData] : ' . print_r($resultItem, true));
        }
        return $data;
    }

    /**
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        return $object->toArray();
    }

    /**
     * @param $results
     * @return mixed
     */
    public function checkElementIfIsChanged ($results) {

        foreach ($results as $result) {
            $content = sha1($result->get('content'));

            $file = $result->get('static_file');
            //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [file] ' . $file);

            if(!file_exists($file)) {
                $contentNew = "File not found";
                //die("File not found");
            } else {
                $contentNew = sha1_file($file);
            }

            $actionDelete = json_decode('{"className":"times","text":"Löschen"}');
            $actionUpdate = json_decode('{"className":"refresh","text":"Aktualisieren"}');
            $statusUpdate = json_decode('{"className":"refresh","text":"Aktualisieren"}');

            if ($contentNew) {
                if($content != $contentNew) {
                    $result->set('status', 'changed');
                    $result->set('actions', array($actionDelete, $actionUpdate));
                } else {
                    $result->set('status', 'unchanged');
                    $result->set('actions', array($actionDelete));
                }
            } else {
                $result->set('status', 'deleted');
                $result->set('actions', array($actionDelete, $actionUpdate));
            }

            // TODO: Optimize Status
            //$fileName = array_reverse ($file)[0];
            //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [getData] : ' . $fileName);
        }
        return $results;
    }
}
return 'modSEManagerGetListOfElementsProcessor';
