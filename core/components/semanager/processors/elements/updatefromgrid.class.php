<?php
class modSEManagerUpdateElementsFromGridProcessor extends modProcessor {

    public $semanager = null;

    public function checkPermissions() {
        return true;
    }

    public function getLanguageTopics() {
        return array('semanager');
    }

    /**
     * {@inheritDoc}
     *
     * @return mixed
     */
    public function process() {

        $data = $this->getProperty('data');
        if (empty($data)) return $this->modx->lexicon('semanager.error.ufg_no_data');

        $this->modx->loadClass("semanager.SEManager");
        $this->semanager = new SEManager($this->modx);

        $record = $this->modx->fromJSON($data);

        $e = $this->modx->getObject('mod'.ucfirst($record['type']), array(
            'id' => $record['id']
        ));

        if($record['static'])

            $this->semanager->makeStaticElement($e); // make static
        else
            $this->semanager->unmakeStaticElement($e); // unmake static

        return $this->success('');
    }


    /**
     * Run the OnContextUpdate event
     * @return void
     */
    public function runOnUpdateEvent() {
        $this->modx->invokeEvent('OnContextUpdate',array(
            'context' => &$this->context,
            'properties' => $this->getProperties(),
        ));
    }

}
return 'modSEManagerUpdateElementsFromGridProcessor';
