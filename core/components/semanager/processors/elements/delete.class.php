<?php

$id =   $scriptProperties['id'];
$path = $scriptProperties['path'];

if ($id && $path) {
    unlink($path);

    $this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] delete: ' . $path);

    return $modx->error->success('', $item);
}