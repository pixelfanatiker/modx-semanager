<?php

class modSEManagerGetListOfElementsProcessor extends modObjectGetListProcessor
{
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
        $this->classKey = 'mod' . ucfirst($type);

        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);

        if (!empty($nf)) {
            $key_filter = ($this->classKey == 'modTemplate') ? 'templatename' : 'name';
            $c->where(array($key_filter . ':LIKE' => '%' . $nf . '%'));
        }

        if (!empty($cf)) {
            $c->where(array('category' => $cf));
        }

        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);

        $sortField = $this->getProperty('sort');
        $sortField = ($sortField == 'name' and $this->classKey == 'modTemplate') ? 'templatename' : 'name';

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '', array($sortField));
        if (empty($sortKey)) $sortKey = $sortField;
        //$c->sortby($sortKey,$this->getProperty('dir'));

        $c->sortby('static', 'ASC');

        if ($limit > 0) {
            $c->limit($limit, $start);
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
    public function checkElementIfIsChanged($results) {

        foreach ($results as $result) {
            $content = sha1($result->get('content'));

            $file = $result->get('static_file');


            if (!file_exists($file)) {
                $contentNew = "File not found";
            } else {
                $contentNew = sha1_file($file);
            }

            $actionDeleteElement = json_decode('{"className":"times js_actionLink js_deleteElement","text":"Delete file and remove element"}');
            $actionRemoveElement = json_decode('{"className":"trash js_actionLink js_removeElement","text":"Remove"}');
            $actionUpdateElement = json_decode('{"className":"refresh js_actionLink js_updateElement","text":"Update"}');

            $actionDeleteElementDisabled = json_decode('{"className":"times disabled","text":"Delete file and remove element"}');
            $actionRemoveElementDisabled = json_decode('{"className":"trash disabled","text":"Remove"}');
            $actionUpdateElementDisabled = json_decode('{"className":"refresh disabled","text":"Update"}');

            $statusUnchanged = json_decode('{"className":"check-circle","text":"File unchanged"}');
            $statusChanged = json_decode('{"className":"question-circle","text":"File changed"}');
            $statusDeleted = json_decode('{"className":"exclamation-circle","text":"File removed"}');

            $this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [contentNew] ' . $contentNew);

            if ($contentNew == "File not found") {
                $result->set('status', $statusDeleted);
                $result->set('actions', array($actionDeleteElementDisabled, $actionRemoveElement, $actionUpdateElementDisabled));

            } else {
                if ($content != $contentNew) {
                    $result->set('status', $statusChanged);
                    $result->set('actions', array($actionDeleteElement, $actionRemoveElement, $actionUpdateElement));
                } else {
                    $result->set('status', $statusUnchanged);
                    $result->set('actions', array($actionDeleteElement, $actionRemoveElement, $actionUpdateElementDisabled));
                }
            }


            // TODO: Optimize Status
            //$fileName = array_reverse ($file)[0];
            //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [getData] : ' . $fileName);
        }
        return $results;
    }
}

return 'modSEManagerGetListOfElementsProcessor';
