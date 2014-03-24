<?php
if($scriptProperties['file']){
    file_put_contents ($scriptProperties['file'], $scriptProperties['content']);
    return $modx->error->success('',$item);
}

