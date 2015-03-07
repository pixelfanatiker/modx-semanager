<?php


$id =   $scriptProperties['id'];
$type = $scriptProperties['type'];
$path = $scriptProperties['path'];

$modElementClass = 'modChunk';

$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] delete: ' . $id);
$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] delete: ' . $path);


// TODO: create seperate classes
if ($id && $path) {
    $element = $modx->getObject($modElementClass, $id);
    if (is_object($element)) {
        $result = $modx->removeObject($modElementClass, $id);
        if ($result) {
            unlink($path);
            return $modx->error->success('', $item);
        }
    }
} else if ($id) {
    $element = $modx->getObject($modElementClass, $id);
    if (is_object($element)) {
        $result = $modx->removeObject($modElementClass, $id);
        if($result) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] delete: ' . $result);
            return $modx->error->success('', $item);
        }
    }
}

