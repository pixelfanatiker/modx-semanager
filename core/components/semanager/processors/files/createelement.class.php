<?php


if (!isset($modx->semanager) || !is_object($modx->semanager)) {
    $semanager = $modx->getService('semanager', 'SEManager', $modx->getOption('semanager.core_path', null, $modx->getOption('core_path') . 'components/semanager/') . 'model/semanager/', $scriptProperties);
    if (!($semanager instanceof SEManager)) return '---';
}

if (!$modx->hasPermission('view')) {
    return $this->failure($modx->lexicon('semanager.no_permission'));
}


$path = $scriptProperties["path"];
$category = $scriptProperties["category"];

$modx->log(xPDO::LOG_LEVEL_DEBUG, "path:".$path);

//$newElem = $modx->semanager->createNewSingleElement($path, $category);

return $modx->error->success($newElem);
