<?php

class modSEManagerGetListOfElementsProcessor extends modObjectGetListProcessor {
    //public $permission = '';
    public $defaultSortField = 'name';

    /**
     * Get a collection of modChunk objects
     * @return array
     */
    public function getData() {

        $this->modx->getService('lexicon','modLexicon');
        $this->modx->lexicon->load ('semanager:default');

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
        $data['results'] = $this->addMediaSourceName($data['results']);


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
    public function addMediaSourceName ($results) {

        foreach ($results as $result) {
            $source = $result->get("source");
            $mediaSource = $this->modx->getObject('sources.modMediaSource', $source);
            if(!empty($mediaSource) && is_object($mediaSource)) {
                $mediaSourceName = $mediaSource->get("name");
            } else {
                $mediaSourceName = "None";
            }
            $result->set("mediasource", $mediaSourceName);
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, "mediasource: ".$mediaSourceName);
        }
        return $results;
    }

    /**
     * @param $results
     * @return mixed
     */
    public function checkElementIfIsChanged($results) {

        foreach ($results as $result) {
            $content = sha1($result->get('content'));

            $file = $result->get('static_file');
            $isStatic = $result->get('static');

            if (!file_exists($file)) {
                $contentNew = "File not found";
            } else {
                $contentNew = sha1_file($file);
            }

            // TODO: Refactoring for better handling
            $actionEditElement = json_decode('{"className":"edit js_actionLink js_editElement","text":"'. $this->modx->lexicon('semanager.common.actions.element.quickupdate') .'"}');
            $actionSyncToFile = json_decode('{"className":"arrow-circle-o-down js_actionLink js_syncToFile","text":"'. $this->modx->lexicon('semanager.common.actions.elements.sync.tofile') .'"}');
            $actionRestoreToFile = json_decode('{"className":"arrow-circle-o-down js_actionLink js_restoreToFile","text":"'. $this->modx->lexicon('semanager.common.actions.elements.restore.tofile') .'"}');
            $actionSyncFromFile = json_decode('{"className":"arrow-circle-o-up js_actionLink js_syncFromFile","text":"'. $this->modx->lexicon('semanager.common.actions.elements.sync.fromfile') .'"}');
            $actionExportToFile = json_decode('{"className":"save js_actionLink js_exportToFile","text":"'. $this->modx->lexicon('semanager.common.actions.element.static') .'"}');
            $actionDeleteElement = json_decode('{"className":"minus-square-o js_actionLink js_deleteElement","text":"'. $this->modx->lexicon('semanager.common.actions.element.delete') .'"}');
            $actionDeleteFileElement = json_decode('{"className":"trash js_actionLink js_deleteFileElement","text":"'. $this->modx->lexicon('semanager.common.actions.element.deletefile_element') .'"}');

            $actionDeleteFileElementDisabled = json_decode('{"className":"trash disabled","text":"Delete file and element"}');
            $actionDeleteElementDisabled = json_decode('{"className":"minus-square-o disabled","text":"Delete element"}');
            $actionEditElementDisabled = json_decode('{"className":"edit disabled","text":"Edit element"}');

            $actionSyncToFileDisabled = json_decode('{"className":"arrow-circle-o-down disabled","text":"'. $this->modx->lexicon('semanager.common.actions.elements.sync.tofile') .'"}');
            $actionSyncFromFileDisabled = json_decode('{"className":"arrow-circle-o-up disabled","text":"'. $this->modx->lexicon('semanager.common.actions.elements.sync.fromfile') .'"}');

            $actionExportToFileDisabled = json_decode('{"className":"save disabled","text":"'. $this->modx->lexicon('semanager.common.actions.element.static') .'"}');

            $statusUnchanged = json_decode('{"className":"check-circle sm-green","text":"'. $this->modx->lexicon('semanager.common.actions.element.status.unchanged') .'"}');
            $statusChanged = json_decode('{"className":"exclamation-circle sm-orange","text":"'. $this->modx->lexicon('semanager.common.actions.element.status.changed') .'"}');
            $statusDeleted = json_decode('{"className":"warning sm-red","text":"'. $this->modx->lexicon('semanager.common.actions.element.status.deleted') .'"}');

            if ($isStatic == false && $contentNew == "File not found") {
                $varActionSaveElement = $actionRestoreToFile;
                $varActionExportElement = $actionExportToFileDisabled;
            } else if ($isStatic == true && $contentNew == "File not found") {
                $varActionSaveElement = $actionRestoreToFile;
                $varActionExportElement = $actionExportToFileDisabled;
            } else if ($isStatic == true) {
                $varActionSaveElement = $actionSyncToFileDisabled;
                $varActionExportElement = $actionExportToFileDisabled;
            }

            if ($contentNew == "File not found") {
                $result->set('status', $statusDeleted);
                $result->set('actions', array(
                    $actionEditElement,
                    $varActionSaveElement,
                    $actionSyncFromFileDisabled,
                    $varActionExportElement,
                    $actionDeleteElement,
                    $actionDeleteFileElementDisabled
                ));

            } else {
                if ($content != $contentNew) {
                    $result->set('status', $statusChanged);
                    $result->set('actions', array(
                        $actionEditElement,
                        $actionSyncToFile,
                        $actionSyncFromFile,
                        $varActionExportElement,
                        $actionDeleteElement,
                        $actionDeleteFileElement
                    ));
                } else {
                    $result->set('status', $statusUnchanged);
                    $result->set('actions', array(
                        $actionEditElement,
                        $actionSyncToFileDisabled,
                        $actionSyncFromFileDisabled,
                        $varActionExportElement,
                        $actionDeleteElement,
                        $actionDeleteFileElement
                    ));
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
