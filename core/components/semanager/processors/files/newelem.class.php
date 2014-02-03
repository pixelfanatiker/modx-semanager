<?php
if (!isset($modx->semanager) || !is_object($modx->semanager)) {
$semanager = $modx->getService('semanager','SEManager',$modx->getOption('semanager.core_path',null,$modx->getOption('core_path').'components/semanager/').'model/semanager/', $scriptProperties);
if (!($semanager instanceof SEManager)) return '---';
}

if (!$modx->hasPermission('view')) {return $this->failure($modx->lexicon('semanager.no_permission'));}

$kk = $modx->semanager->newElem();

return $modx->error->success($kk);
