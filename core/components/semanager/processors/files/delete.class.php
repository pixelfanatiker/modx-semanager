<?php


if (!isset($modx->semanager) || !is_object($modx->semanager)) {
    $semanager = $modx->getService('semanager','SEManager',$modx->getOption('semanager.core_path',null,$modx->getOption('core_path').'components/semanager/').'model/semanager/', $scriptProperties);
    if (!($semanager instanceof SEManager)) return '---';
}

$file = $scriptProperties["file"];

$result = true;

if ($file) {
    $result = $modx->semanager->deleteFile($file);
} else {
    $result = false;
}


if ($result == true) {
    return $modx->error->success("");
} else {
    return $modx->error->failure("");
}