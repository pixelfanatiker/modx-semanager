<?php


$id =   $scriptProperties["id"];
$type = $scriptProperties["type"];
$file = $scriptProperties["file"];
$sync = $scriptProperties["sync"];

$parameter = array(
    "id" => $id,
    "static" => 1,
    "static_file" => $file
);

/*$this->modx->log(xPDO::LOG_LEVEL_ERROR,"[SEM] sync: " . $sync . "  id  : " . $id);
$this->modx->log(xPDO::LOG_LEVEL_ERROR,"[SEM] sync: " . $sync . "  type: " . $type);
$this->modx->log(xPDO::LOG_LEVEL_ERROR,"[SEM] sync: " . $sync . "  file: " . $file);*/

if ($type) {

    if ($type == "template") {
        $modClass = "modTemplate";
        $fieldName = "templatename";
        
    } else {        
        $fieldName = "name";
        if ($type == "chunk") {
            $modClass = "modChunk";
        }
        if ($type == "plugin") {
            $modClass = "modPlugin";
        }
        if ($type == "snippet") {
            $modClass = "modSnippet";
        }
    }

} else {
    return $modx->error->failure("Element type is not defined");
}

if ($id && $file) {

    if ($sync == "tofile") {

        $element = $this->modx->getObject($modClass, $parameter);

        if (is_object($element)) {

            $content = $element->get("content");
            $openFile = fopen($file, "w");

            fwrite($openFile, $content);
            fclose($openFile);

            return $modx->error->success("", $item);
        } else {
            return $modx->error->failure("", $item);
        }


    } else if ($sync == "fromfile") {

        $fileContent = file_get_contents($file);

        $element = $this->modx->getObject($modClass, $parameter);

        if (is_object($element)) {

            $element->set("content", $fileContent);
            $element->save();

            if($element->save() == true) {
                return $modx->error->success("", $item);
            }
        } else {
            return $modx->error->failure("", $item);
        }
    }
}

