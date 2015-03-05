<?php

$id =   $scriptProperties['id'];
$path = $scriptProperties['path'];

$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] delete: ' . $id);
$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] delete: ' . $path);

if ($id && $path) {
    $chunk = $modx->getObject('modChunk', $id);
    if (is_object($chunk)) {
        $result = $modx->removeObject('modChunk', $id);
        if($result) {
            unlink($path);
            return $modx->error->success('', $item);
        }
    }
} else if ($id) {
    $chunk = $modx->getObject('modChunk', $id);
    if (is_object($chunk)) {
        $result = $modx->removeObject('modChunk', $id);
        if($result) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] delete: ' . $result);
            return $modx->error->success('', $item);
        }
    }
}