<?php
if (!$modx->getOption('semanager.auto_create_elements', null, false)) return '';
if (!isset($modx->semanager) || !is_object($modx->semanager)) {
    $semanager = $modx->getService('semanager','SEManager',$modx->getOption('semanager.core_path',null,$modx->getOption('core_path').'components/semanager/').'model/semanager/', array());
    if (!($semanager instanceof SEManager)) return '---';
}

if ($files = $modx->semanager->getNewFiles()) {
    $modx->semanager->newElem($files);
    $modx->cacheManager->refresh();
}
return '';