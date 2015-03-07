<?php


if (!isset($modx->semanager) || !is_object($modx->semanager)) {
    $semanager = $modx->getService('semanager','SEManager',$modx->getOption('semanager.core_path',null,$modx->getOption('core_path').'components/semanager/').'model/semanager/', $scriptProperties);
    if (!($semanager instanceof SEManager)) return '---';
}

$id =   $scriptProperties["id"];
$type = $scriptProperties["type"];
$file = $scriptProperties["file"];
$del =  $scriptProperties["del"];

$result = true;

if ($type) {
    $modClass = $modx->semanager->getModClass($type);
} else {
    $result = false;
}


if ($del == "element") {
    $result = $modx->semanager->deleteElement($modClass, $id);

} else if ($del == "file") {
    $result = $modx->semanager->deleteFile($file);

} else if ($del == "both") {
    $result = $modx->semanager->deleteElement($modClass, $id);

    if ($result == true) {
        $result = $modx->semanager->deleteFile($file);
    }
}

//$modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] delete step3: ' . $del . "    result: " . $result);

if ($result == true) {
    return $modx->error->success("");
} else {
    return $modx->error->failure("");
}
