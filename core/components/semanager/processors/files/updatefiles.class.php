<?php

if (!isset($modx->semanager) || !is_object($modx->semanager)) {
    $semanager = $modx->getService('semanager','SEManager',$modx->getOption('semanager.core_path',null,$modx->getOption('core_path').'components/semanager/').'model/semanager/', $scriptProperties);
    if (!($semanager instanceof SEManager)) return '---';
}

$file = $scriptProperties['file'];
$content = $scriptProperties['content'];

if($file){
    file_put_contents ($file, $content);
    return $modx->error->success('',$item);
}

