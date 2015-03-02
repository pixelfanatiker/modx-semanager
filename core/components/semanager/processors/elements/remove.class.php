<?php

$parameter = $scriptProperties['path'];

$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] remove: ' . $parameter);

if ($parameter) {

    return $modx->error->success('', $item);
}