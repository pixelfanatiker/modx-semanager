<?php

/*
 * SE Manager
 *
 * Copyright 2012 by LOVATA Group <info@lovata.com>
 *
 * Refactoring and further development
 * Florian Gutwald, florian@frontend-mercenary.com
 *
 * SE Manager is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * SE Manager is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * SE Manager; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package semanager
 */

class SEManager {

    public $modx = null;
    public $map = array ();
    public $config = array ();
    private $elementsDir;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct (modX &$modx, array $config = array ()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption ('semanager.core_path', null, $this->modx->getOption ('core_path') . 'components/semanager/');
        $assetsPath = $this->modx->getOption ('semanager.assets_path', null, $this->modx->getOption ('assets_path') . 'components/semanager/');
        $assetsUrl = $this->modx->getOption ('semanager.assets_url', null, $this->modx->getOption ('assets_url') . 'components/semanager/');

        $this->config = array_merge (array (
                'corePath' => $corePath,
                'modelPath' => $corePath . 'model/',
                'processorsPath' => $corePath . 'processors/',
                'controllersPath' => $corePath . 'controllers/',
                'templatesPath' => $corePath . 'templates/',

                'baseUrl' => $assetsUrl,
                'cssUrl' => $assetsUrl . 'css/',
                'jsUrl' => $assetsUrl . 'js/',
                'imgUrl' => $assetsUrl . 'img/',
                'connectorUrl' => $assetsUrl . 'connector.php',

                'elementsDir' => $this->elementsDir = $this->modx->getOption ('semanager.elements_dir', null, '/elements/'),
                'fileSuffixChunk' => $this->elementsDir = $this->modx->getOption ('semanager.filename_tpl_chunk', null, 'ch.html'),
                'fileSuffixPlugin' => $this->elementsDir = $this->modx->getOption ('semanager.filename_tpl_plugin', null, 'pl.php'),
                'fileSuffixSnippet' => $this->elementsDir = $this->modx->getOption ('semanager.filename_tpl_snippet', null, 'sn.php'),
                'fileSuffixTemplate' => $this->elementsDir = $this->modx->getOption ('semanager.filename_tpl_template', null, 'tp.html'),

                'defaultFileSuffix' => array (
                        'template' => 'tp.html',
                        'plugin' => 'pl.php',
                        'snippet' => 'sn.php',
                        'chunks' => 'ch.html'),
        ), $config);

        $this->modx->addPackage ('semanager', $this->config['modelPath']);

        //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [__contruct] $corePath: ' . $corePath);

        if ($this->modx->lexicon) {
            $this->modx->lexicon->load ('semanager:default');
        }

    }


    /*
     * Initializes SE Manager into different contexts.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     */
    public function initialize ($ctx = 'mgr') {
        $output = '';
        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass ('semanager.request.SEManagerControllerRequest', $this->config['modelPath'], true, true)) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new SEManagerControllerRequest($this);
                $output = $this->request->handleRequest ();
                break;
        }
        return $output;
    }


    /**
     * Make synchronization of all Elements
     */
    public function syncAll () {
        $this->elementsDir = $this->config['elementsDir'];

        if (!file_exists ($this->elementsDir)) {
            $this->_createDirectories ($this->elementsDir);
        }

        $typeSeparation = $this->modx->getOption ('semanager.type_separation', null, true);
        if ($typeSeparation) {
            $dirs = array (
                    'modTemplate' => $this->elementsDir . $this->modx->getOption ('semanager.template_directory', null, 'templates/'),
                    'modChunk' => $this->elementsDir . $this->modx->getOption ('semanager.template_directory', null, 'chunks/'),
                    'modSnippet' => $this->elementsDir . $this->modx->getOption ('semanager.template_directory', null, 'snippets/'),
                    'modPlugin' => $this->elementsDir . $this->modx->getOption ('semanager.template_directory', null, 'plugins/')
            );

            foreach ($dirs as $type => $dir) {
                $this->_createDirectories ($dir);
                $this->manyElementsToStatic ($type, $dir);
            }

        } else {
            $types = array (
                    'modTemplate',
                    'modChunk',
                    'modSnippet',
                    'modPlugin'
            );

            foreach ($types as $type) {
                $this->manyElementsToStatic ($type);
            }
        }
    }

    /**
     * @param $element
     * @param $path
     * @return bool
     */
    public function oneElementToStatic ($element, $path) {
        $useCategories = $this->modx->getOption ('semanager.use_categories', null, true);
        if ($useCategories) {
            $categoriesMap = $this->getCategoriesMap ($element->category);
            if ($categoriesMap != '') {
                $path = $path . $categoriesMap . '/';
                $this->_createDirectories ($path);
            }
        }

        // TODO: отрефакторить. учесть все возможные БД
        $elementClass = str_replace (array ('_mysql', '_sqlsrv'), '', get_class ($element));
        $type = strtolower (str_replace ('mod', '', $elementClass));
        $filenameTpl = $this->modx->getOption ('semanager.filename_tpl_' . $type, null, $this->config['defaultFileSuffix'][$type]);

        if ($elementClass == 'modTemplate') {
            $element->set ('name', $element->templatename);
        }

        $filePath = $path . $element->name . '.' . $filenameTpl;
        touch ($filePath);

        $content = $element->getContent ();
        $element->set ('static_file', $filePath);
        $element->set ('static', true);
        $element->set ('source', 0);
        $element->setFileContent ($content);

        if ($element->save ()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $fullFilePath
     * @return bool
     */
    public function checkNewFileForElement ($fullFilePath) {
        $useMediaSources   = $this->modx->getOption('semanager.use_mediasources', 0);
        $path = $this->getElementsDirectory();
        if ($useMediaSources >= 1) {
            $mediaSourceId   = $this->getMediaSource();
            $file = str_replace ($path, "", $fullFilePath);
        } else {
            //$path = $this->modx->getOption ('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/');
            $mediaSourceId = 0;
            $file = $fullFilePath;
        }

        //$this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM:checkNewFileForElement] file: ' . $fullFilePath);
        //$this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM:checkNewFileForElement] path: ' . $path);


        $fileName = array_reverse (explode ('.', array_pop (explode ('/', $fullFilePath))));
        $filePath = array_reverse (explode ('/', str_replace ($path, '', $fullFilePath)));


        if (count ($fileName) <= 1) return false; // if file not have extension

        $useSuffixOnly = $this->modx->getOption ('semanager.use_suffix_only', null, false);
        if ($useSuffixOnly == true) {
            $position = 1;
        } else {
            $position = 2;
        }

        $fileExtension = implode ('.', array_reverse (array_slice ($fileName, 0, $position)));
        $fileType = $this->getFileType($filePath);


        $useCategories  = $this->modx->getOption ('semanager.use_categories', null, true);
        if ($useCategories) {
            $modElementClasses = array (
                 "chunks" => "modChunk"
                ,"plugins" => "modPlugin"
                ,"snippets" => "modSnippet"
                ,"templates" => "modTemplate"
            );

            foreach ($modElementClasses as $type => $modClass) {
                //$this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM] '.$file.'  class: '.$modClass."  mediaSourceId: ".$mediaSourceId);
                if ($fileType == $type) {
                    //$elementMediaSourceId = $this->getElementsMediaSource($file, $modClass);
                    if (!is_object ($this->searchStaticElement($file, $modClass, $mediaSourceId))) {
                        //$this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM] ' .$file." is an existing " .$fileType);
                        return true;
                    }
                }
            }

        } else {
            $fileSuffixes = array (
                 "modChunk" => $this->modx->getOption ('semanager.fileSuffixChunk', null, 'ch.html')
                ,"modPlugin" => $this->modx->getOption ('semanager.fileSuffixPlugin', null, 'pl.php')
                ,"modSnippet" => $this->modx->getOption ('semanager.fileSuffixSnippet', null, 'sn.php')
                ,"modTemplate" => $this->modx->getOption ('semanager.fileSuffixTemplate', null, 'tp.html')
            );

            foreach ($fileSuffixes as $modClass => $fileSuffix) {
                if ($fileExtension == $fileSuffix) {
                    if (!is_object($this->modx->getObject($modClass, array('static' => 1, 'static_file' => $file)))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getNewFiles () {

        // TODO: refactor
        $actionCreateElement = json_decode('{"className":"check-square-o js_actionLink js_createElement","text":"Create element"}');
        $actionEditFile      = json_decode('{"className":"edit js_actionLink js_editFile","text":"Edit file"}');
        $actionDeleteFile    = json_decode('{"className":"trash js_actionLink js_deleteFile","text":"Delete file"}');

        $actions = array($actionCreateElement, $actionEditFile, $actionDeleteFile);

        $files = array ();
        $filesystem = $this->scanElementsFolder ();

        foreach ($filesystem as $file) {

            if ($this->checkNewFileForElement ($file)) {

                $path = $this->getElementsDirectory();

                $useCategories  = $this->modx->getOption ('semanager.use_categories', null, true);
                $category = 0;

                $filePath = array_reverse (explode ('/', str_replace ($path, '', $file)));
                $fullCategory = array_reverse ($filePath);

                array_shift ($fullCategory);
                array_pop ($fullCategory);
                $fullCategory = implode ('/', $fullCategory);
                $fullCategory = $fullCategory . '/';

                if ($useCategories) {
                    $category = $fullCategory;
                    if ($category == '/') {
                        $category = 0;
                    }
                }

                $filename = array_shift ($filePath);
                $fileType = $this->getFileType($filePath);
                $mediaSourceId = $this->getMediaSource();

                $files[] = array (
                        'filename' => $filename,
                        'category' => $category,
                        'type' => $fileType,
                        'path' => $file,
                        'content' => file_get_contents ($file, true),
                        'mediasource' => $mediaSourceId,
                        'actions' => $actions
                );
            }
        }
        return $files;
    }

    /**
     * @param $filenameWithSuffix
     * @return mixed
     */
    public function removeFileTypeSuffix ($filenameWithSuffix) {
        $filenameArr = explode(".", $filenameWithSuffix);
        return $filenameArr[0];
    }

    /**
     * @return array
     */
    public function scanElementsFolder () {
        $files = array ();
        $path = $this->modx->getOption ('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/');
        $this->_scanFolder ($path, $files);

        return $files;
    }

    /**
     * @param $path
     * @param $files
     */
    private function _scanFolder ($path, &$files) {
        $d = dir ($path);

        while (false != ($e = $d->read ())) {
            if ($e != '.' and $e != '..') {
                if (is_dir ($d->path . $e)) {
                    $this->_scanFolder ($d->path . $e . '/', $files);
                } else {
                    $files[] = $d->path . $e;
                }
            }
        }
        $d->close ();
    }

    /**
     * Return type of Element (chunk, plugin, snippet or template)
     *
     * @param $element
     * @return mixed
     */
    private function _getTypeOfElement ($element) {
        $config = $this->modx->getConfig ();
        $dbtype = $config['dbtype'];
        return str_replace (array ($dbtype, 'mod', '_'), '', strtolower (get_class ($element)));
    }

    /**
     * Make and return full path to file with element's code
     *
     * @param $element
     * @return mixed|string
     */
    // TODO: Merge with getFilePath function
    private function _makePath ($element) {
        $path = $this->modx->getOption ('semanager.elements_dir', null, MODX_ASSETS_PATH . 'elements/');
        $typeSeparation = $this->modx->getOption ('semanager.type_separation', null, true);
        $useCategories = $this->modx->getOption ('semanager.use_categories', null, true);

        // make subdirectories with name by element's type
        if ($typeSeparation) {
            $path .= $this->_getTypeOfElement ($element) . 's/';
        }

        // make subdirectories with category name
        if ($useCategories) {
            $categoriesMap = $this->getCategoriesMap ($element->category);
            if ($categoriesMap != '') {
                $path .= $categoriesMap . '/';
            }
        }
        return $path;
    }

    /**
     * Make static element. Create static file.
     *
     * @param $element
     * @return bool
     */
    public function setAsStaticElement ($element) {
        // $this->modx->log(E_ERROR,  $path);
        $path = $this->_makePath ($element);
        $type = $this->_getTypeOfElement ($element);

        $filenameTpl = $this->modx->getOption ('semanager.filename_tpl_' . $type, null, '');

        if ($type == 'template') {
            $filePath = $path . $element->templatename . '.' . $filenameTpl;
        } else {
            $filePath = $path . $element->name . '.' . $filenameTpl;
        }

        $this->_createDirectories (dirname ($filePath));
        touch ($filePath);
        $content = $element->getContent ();

        $element->set ('static_file', $filePath);
        $element->set ('static', true);
        $element->setFileContent ($content);
        if ($element->save ()) {
            return $element;
        } else {
            return false;
        }
    }

    /**
     * Unmake static element. Make dynamic element. Remove static file
     *
     * @param $element
     * @return bool
     */
    public function unsetAsStaticElement ($element) {
        $fileName = $element->get ('static_file');
        $content = $element->getContent ();

        $element->set ('static_file', '');
        $element->set ('static', false);
        $element->setContent ($content);

        if ($element->save ()) {
            unlink ($fileName);
            return $element;
        } else {
            return false;
        }
    }

    /**
     * @param $class_name
     * @param string $path
     */
    public function manyElementsToStatic ($class_name, $path = '') {
        if ($path == '') {
            $path = $this->elementsDir;
        }

        $elements = $this->modx->getCollection ($class_name);

        foreach ($elements as $element) {
            $this->oneElementToStatic ($element, $path);
        }
    }

    /**
     * Рекурсивная функция, которая получает полные пути для вложенных категорий
     *
     * @param $id
     * @param array $parents
     * @param array $category_list
     */
    private function _findAllParents ($id, array $parents, array $category_list) {
        $parents[] = $category_list[$id]['name'];
        $parent = $category_list[$id]['parent'];
        if ($parent != 0) {
            $this->_findAllParents ($parent, $parents, $category_list);
        } else {
            $this->map = $parents;
        }
    }

    /**
     * @param $idCategory
     * @return string
     */
    public function getCategoriesMap ($idCategory) {
        if ($idCategory == 0) return '';
        // get all categories
        $categories = $this->modx->getCollection ('modCategory');
        $list = array ();
        foreach ($categories as $c) {
            $list[$c->id] = array (
                    'parent' => $c->parent,
                    'name' => $c->category
            );
        }
        $this->_findAllParents ($idCategory, array (), $list);
        $map_to_path = join ('/', array_reverse ($this->map));

        return $map_to_path;
    }

    /**
     * Recursive mkdir function
     *
     * @param $strPath
     * @return bool
     */
    private function _createDirectories ($strPath) {
        if (is_dir ($strPath)) return true;
        $pStrPath = dirname ($strPath);
        if (!$this->_createDirectories ($pStrPath)) return false;
        return @mkdir ($strPath);
    }

    /**
     * @param $category
     * @return string
     */
    public function parseCategory ($category) {
        $idCategory = '';
        if ($category == '0') {
            return '0';
        } else {
            $category = explode ('/', $category);
            array_pop ($category);
            $prev_id = '0';
            for ($i = 0; $i < sizeof ($category); $i++) {
                $categ = $this->modx->getObject ('modCategory', array ('category' => $category[$i], 'parent' => $prev_id));

                if ($categ) {
                    $idCategory = $categ->id;
                    $prev_id = $categ->id;
                } else {
                    $newCategory = $this->modx->newObject ('modCategory');
                    $newCategory->set ('parent', $prev_id);
                    $newCategory->set ('category', $category[$i]);
                    $newCategory->save ();
                    $prev_id = $newCategory->id;
                    $idCategory = $newCategory->id;
                }
            }
        }
        return $idCategory;
    }

    /**
     * @param $fullFilePath
     * @param $categoryName
     * @return bool
     */
    public function createNewSingleElement ($fullFilePath, $categoryName) {
        $this->modx->log(xPDO::LOG_LEVEL_DEBUG, "[createNewSingleElement] fullFilePath:".$fullFilePath);
        if ($this->checkNewFileForElement ($fullFilePath)) {
            $typesClass = array (
                    'templates' => array ('modTemplate', $this->modx->getOption ('semanager.fileSuffixTemplate', null, 'tp.html')),
                    'chunks' => array ('modChunk', $this->modx->getOption ('semanager.fileSuffixChunk', null, 'ch.html')),
                    'snippets' => array ('modSnippet', $this->modx->getOption ('semanager.fileSuffixSnippet', null, 'sn.php')),
                    'plugins' => array ('modPlugin', $this->modx->getOption ('semanager.fileSuffixPlugin', null, 'pl.php'))
            );

            $typeSeparation = $this->modx->getOption ('semanager.type_separation', null, true);
            $useCategories = $this->modx->getOption ('semanager.use_categories', null, true);
            $useMediaSources   = $this->modx->getOption('semanager.use_mediasources', 0);
            $path = $this->modx->getOption ('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/');

            $category = 0;
            $type = '0';

            $filePath = array_reverse (explode ('/', str_replace ($path, '', $fullFilePath)));
            $filename = array_shift ($filePath);

            if ($typeSeparation) {
                $type = array_pop ($filePath);
                if ($type == '') {
                    $type = 0;
                }
            }
            if ($useCategories) {
                $category = array_shift ($filePath);
                if ($category == '') {
                    $category = 0;
                }
            }
            if ($useMediaSources >= 1) {
                $mediaSourcePath = $this->getElementsDirectory();
                $mediaSourceId   = $this->getMediaSource();
                $elementPath = str_replace ($mediaSourcePath, "", $fullFilePath);
            } else {
                $mediaSourceId = 0;
                $elementPath = $fullFilePath;
            }


            $categoryId = $this->parseCategory ($categoryName);
            $currentObject = $this->modx->newObject ($typesClass[$type][0]);
            $elementName = $this->removeFileTypeSuffix(str_replace ('.' . $typesClass[$type][1], "", $filename));
            $fieldName = $this->getElementFieldName($type);

            $this->modx->log(xPDO::LOG_LEVEL_DEBUG, "[createNewSingleElement] elementPath: ".$elementPath);

            $this->setElement ($currentObject, $elementPath, $categoryId, $fieldName, $elementName, $mediaSourceId);
            $this->saveElement ($type, $currentObject, $fullFilePath);

            $status = true;
        } else {
            $status = false;
        }

        return $status;
    }

    /**
     * @param array $files
     * @return bool
     */
    public function createNewElements (array $files = array ()) {
        if (!$files) {
            $files = $this->getNewFiles ();
        }


        $typesClass = array (
                'templates' => array ('modTemplate', $this->modx->getOption ('semanager.fileSuffixTemplate', null, 'tp.html')),
                'chunks' => array ('modChunk', $this->modx->getOption ('semanager.fileSuffixChunk', null, 'ch.html')),
                'snippets' => array ('modSnippet', $this->modx->getOption ('semanager.fileSuffixSnippet', null, 'sn.php')),
                'plugins' => array ('modPlugin', $this->modx->getOption ('semanager.fileSuffixPlugin', null, 'pl.php'))
        );



        foreach ($files as $filesItem) {
            $type          = $filesItem['type'];
            $filePath      = $filesItem['path'];
            $mediaSourceId = $filesItem['mediasource'];
            $fileName      = str_replace ('.' . $typesClass[$filesItem['type']][1], "", $filesItem['filename']);
            $currentObject = $this->modx->newObject($typesClass[$filesItem['type']][0]);
            $categoryId    = $this->parseCategory ($filesItem['category']);
            $elementName   = $this->removeFileTypeSuffix($fileName);
            $fieldName     = $this->getElementFieldName($type);
            $staticFile    = $this->getStaticElementFilePath($filePath, $mediaSourceId);

            $this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM:createNewElements] staticFile: ' .$staticFile);

            $this->setElement ($currentObject, $staticFile, $categoryId, $mediaSourceId, $fieldName, $elementName);
            $this->saveElement ($currentObject, $filePath, $type);
        }
        return true;
    }

    /**
     * @param $currentObject
     * @param $staticFile
     * @param $categoryId
     * @param $mediaSourceId
     * @param $fieldName
     * @param $elementName
     */
    public function setElement ($currentObject, $staticFile, $categoryId, $mediaSourceId, $fieldName, $elementName) {
        $currentObject->set ($fieldName, $elementName);
        $currentObject->set ('static', '1');
        $currentObject->set ('source', $mediaSourceId);
        $currentObject->set ('static_file', $staticFile);
        $currentObject->set ('category', $categoryId);
    }

    /**
     * @param $currentObject
     * @param $filePath
     * @param $elementType
     * @return mixed
     */
    public function saveElement ($currentObject, $filePath, $elementType) {
        $typeArray = array ('templates', 'snippets', 'plugins', 'chunks');
        foreach ($typeArray as $type) {
            if ($elementType == $type) {
                $content = file_get_contents ($filePath, true);
                $currentObject->set ('content', $content);
            }
        }
        $status = $currentObject->save ();

        return $status;
    }

    /**
     * @return mixed
     */
    public function getElementsDirectory () {
        return $this->modx->getOption ('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/');
    }

    /**
     * @param $file
     * @return mixed|string
     */
    public function getStaticElementFilePath ($file, $mediaSourceId) {
        $this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM:getStaticElementFilePath] sourceid: '.$mediaSourceId);

        $useMediaSources = $this->modx->getOption('semanager.use_mediasources', 0);
        if ($useMediaSources == 0 || $mediaSourceId == 0) {
            $staticElementsDirectory = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/');
        } else {
            $staticElementsDirectory = $this->modx->getOption('semanager.elements_dir', null, '/template/elements/');
        }

        $filePath = array_reverse (explode ('/', $file));
        //$fileName = array_shift ($filePath);

        $mediaSource = $this->modx->getObject('sources.modMediaSource', $mediaSourceId);
        if(!empty($mediaSource) && is_object($mediaSource)) {
            $path = $mediaSource->prepareOutputUrl($staticElementsDirectory);
            $this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM:getStaticElementFilePath] path: '.$path);
        }

        $staticFile = str_replace (MODX_BASE_PATH, "", $file);

        $this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM:getStaticElementFilePath] stat: '.$staticFile);
        //$this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM:getStaticElementFilePath] name: '.$fileName);
        $this->modx->log (xPDO::LOG_LEVEL_ERROR, '[SEM:getStaticElementFilePath] path: '.$path);

        return $staticFile;
    }


    /**
     * @return int
     */
    public function getMediaSource () {
        $mediaSourceId = 0;
        $useMediaSources   = $this->modx->getOption('semanager.use_mediasources', 0);
        if ($useMediaSources >= 1) {
            $mediaSourceId = intval($this->modx->getOption('semanager.elements_mediasource', 1));
        }
        return $mediaSourceId;
    }


    /**
     * @param $source
     * @return mixed
     */
    public  function getMediaSourceName ($source) {
        $mediaSource = $this->modx->getObject('sources.modMediaSource', $source);
        if(!empty($mediaSource) && is_object($mediaSource)) {
            return $mediaSource->get("name");
        }
    }

    /**
     * @param $file
     * @param $modClass
     * @return int
     */
    public function getElementsMediaSource ($file, $modClass) {
        $parameter = array (
             'static' => 1
            ,'static_file' => $file
        );
        $element = $this->modx->getObject ($modClass, $parameter);
        $mediaSourceId = intval($element->get("source"));
        return $mediaSourceId;

    }

    /**
     * @param $filePath
     * @return int|mixed|string
     */
    public function getFileType ($filePath) {
        // TODO: добавить дополнительно проверку, если файл не в папке вообще
        // TODO: check, if the file is existing in the folder
        $type = '0';
        $typeSeparation = $this->modx->getOption ('semanager.type_separation', null, true);
        if ($typeSeparation) {
            $type = array_pop ($filePath);
            if ($type == '') {
                $type = 0;
            }
        }
        return $type;
    }

    /**
     * @param $file
     * @param $modClass
     * @return null|object
     */
    public function getStaticFileField ($file, $modClass, $mediaSourceId) {
        $parameter = array (
            'static' => 1
            ,'static_file' => $file
            ,"source" => $mediaSourceId
        );
        $element = $this->modx->getObject ($modClass, $parameter);
        return $element;
    }


    public function searchStaticElement ($file, $modClass) {
        $parameter = array (
             'static' => 1
            ,'static_file:LIKE' => "%".$file."%",
        );
        $element = $this->modx->getObject ($modClass, $parameter);
        return $element;

    }

    /**
     * @param $modClass
     * @param $id
     * @return bool
     */
    public function deleteElement($modClass, $id) {
        $element = $this->modx->getObject($modClass, $id);

        if (is_object($element)) {
            return $this->modx->removeObject($modClass, $id);
        } else {
            return false;
        }
    }

    /**
     * @param $file
     * @return bool
     */
    public function deleteFile ($file) {
        if ($file) {
            unlink($file);
            return true;
        } else {
            return false;
        }

    }


    /**
     * @param $type
     * @return string
     */
    public function getModClass($type) {
        if ($type == "chunk") $modClass = "modChunk";
        else if ($type == "plugin") $modClass = "modPlugin";
        else if ($type == "snippet") $modClass = "modSnippet";
        else if ($type == "template") $modClass = "modTemplate";

        return $modClass;
    }



    /**
     * @param $type
     * @return string
     */
    public function getElementFieldName($type) {
        //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[SEM] getElementFieldName: ' . $type);
        if ($type == "template") {
            $elementFieldName = "templatename";
        } else {
            $elementFieldName = "name";
        }

        return $elementFieldName;
    }


    /**
     * @param $file
     * @param $modClass
     * @param $parameter
     * @return bool
     */
    public function writeToFile($file, $modClass, $parameter) {
        $element = $this->modx->getObject($modClass, $parameter);
        if (is_object($element)) {

            $content = $element->get("content");
            $openFile = fopen($file, "w");

            fwrite($openFile, $content);
            fclose($openFile);

            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }


    /**
     * @param $file
     * @param $modClass
     * @param $parameter
     * @return bool
     */
    public function updateChunkFromStaticFile($file, $modClass, $parameter) {

        $fileContent = file_get_contents($file);
        $element = $this->modx->getObject($modClass, $parameter);

        if (is_object($element)) {
            $element->set("content", $fileContent);
            $element->save();

            if ($element->save() == true) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }
}
