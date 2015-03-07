<?php


if (!isset($modx->semanager) || !is_object($modx->semanager)) {
    $semanager = $modx->getService('semanager','SEManager',$modx->getOption('semanager.core_path',null,$modx->getOption('core_path').'components/semanager/').'model/semanager/', $scriptProperties);
    if (!($semanager instanceof SEManager)) return '---';
}


$id =   $scriptProperties["id"];
$type = $scriptProperties["type"];
$file = $scriptProperties["file"];
$sync = $scriptProperties["sync"];

$result = true;

if ($type) {
    $modClass = $modx->semanager->getModClass($type);
    $fieldName = $modx->semanager->getElementFieldName($type);

    $parameter = array("id" => $id , "static_file" => $file);

    if ($sync == "tofile") {
        $result = $modx->semanager->writeToFile($file, $modClass, $parameter);

    } else if ($sync == "fromfile") {
        $result = $modx->semanager->updateChunkFromStaticFile($file, $modClass, $parameter);
    }
}


$result = true;


if ($result) {
    return $modx->error->success("");
} else {
    return $modx->error->failure("");
}