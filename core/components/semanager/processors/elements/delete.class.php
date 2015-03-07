<?php


$id =   $scriptProperties["id"];
$type = $scriptProperties["type"];
$path = $scriptProperties["path"];

if ($type) {
    if ($type == "chunk") $modClass = "modChunk";
    if ($type == "plugin") $modClass = "modPlugin";
    if ($type == "snippet") $modClass = "modSnippet";
    if ($type == "template") $modClass = "modTemplate";
} else {
    return $modx->error->failure("Element type is not defined");
}

// TODO: create seperate classes
if ($id && $path) {
    $element = $modx->getObject($modClass, $id);
    if (is_object($element)) {
        $result = $modx->removeObject($modClass, $id);
        if ($result) {
            unlink($path);
            return $modx->error->success("", $item);
        }
    }
} else if ($id) {
    $element = $modx->getObject($modClass, $id);
    if (is_object($element)) {
        $result = $modx->removeObject($modClass, $id);
        if($result) {
            return $modx->error->success("", $item);
        }
    }
}
