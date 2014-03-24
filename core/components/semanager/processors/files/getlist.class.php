<?php


class modSEManagerGetListOfFilesProcessor extends modObjectGetListProcessor {
    public $scriptProperties;
    public $semanager = null;
    public $defaultSortField = 'name';

    public function getData() {
        $data = array();
        $datas = array();

        $sort = $this->modx->getOption('sort', $_REQUEST, 'filename');
        $dir = $this->modx->getOption('dir', $_REQUEST, 'ASC');
        $start = $this->modx->getOption('start',$_REQUEST,0);
        $limit = $this->modx->getOption('limit',$_REQUEST,20);
        $namefilter = $this->modx->getOption('namefilter',$_REQUEST,'');
        $typefilter = $this->modx->getOption('type',$_REQUEST,'');


        $this->modx->loadClass('semanager.SEManager');
        $this->semanager = new SEManager($this->modx);

        $datas['all'] = $this->semanager->getNewFiles();
        $count = 0;
        if($namefilter){
            for($i=0;$i<sizeof($datas['all']);$i++){
                if(strpos($datas['all'][$i]['filename'],$namefilter) !== false){
                    $count++;
                    $data['results_step'][] = $datas['all'][$i];
                }
            }
            for($i=$start;$i<$limit+$start;$i++){
                $data['results'][] = $data['results_step'][$i];
            }
            $data['total'] = $count;

        }elseif($typefilter){
            for($i=0;$i<sizeof($datas['all']);$i++){
                if($datas['all'][$i]['type'] == $typefilter){
                    $count++;
                    $data['results_step'][] = $datas['all'][$i];
                }
            }
            for($i=$start;$i<$limit+$start;$i++){
                    $data['results'][] = $data['results_step'][$i];
            }
            $data['total'] = $count;

        }else{
            for($i=$start;$i<$limit+$start;$i++){
                $data['results'][] = $datas['all'][$i];
            }
            $data['total'] = count($datas['all']);
        }


        return $data;

    }

    public function prepareRow($object) {
        return $object;
    }

}
return 'modSEManagerGetListOfFilesProcessor';
