<?php
if($scriptProperties['path']){
    unlink($scriptProperties['path']);
    return $modx->error->success('',$item);
}