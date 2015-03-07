<?php

$id =   $scriptProperties['id'];
$type = $scriptProperties['type'];
$content = $scriptProperties['content'];

$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] tostatic: ' . $id);
$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] tostatic: ' . $content);

if ($id && $content) {

    $chunk = $modx->getObject($type, $id);

    if (is_object($chunk)) {
        $result = $modx->removeObject($type, $id);
        if($result) {
            unlink($content);
            return $modx->error->success('', $item);
        }
    }
}