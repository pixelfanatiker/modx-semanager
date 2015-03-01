<?php
if ($scriptProperties['chunk']) {
    $this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] remove: ' . $scriptProperties['chunk']);
    return $modx->error->success('', $item);
}