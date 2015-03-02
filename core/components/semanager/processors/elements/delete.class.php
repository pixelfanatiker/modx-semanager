<?php

$parameter = $scriptProperties['path'];

if ($parameter) {
    //unlink($scriptProperties['path']);

    $this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] remove: ' . $parameter);

    return $modx->error->success('', $item);
}