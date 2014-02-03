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
        return $data;

    }

    public function prepareRow(xPDOObject $object) {
        return $object->toArray();
    }

}
return 'modSEManagerGetListOfElementsProcessor';
