<?php

/*
 * SE Manager
 *
 * Copyright 2012 by LOVATA Group <info@lovata.com>
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

class SEManager
{

    public $modx = null;
    public $map = array();
    public $config = array();
    private $elementsDir;

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('semanager.core_path', null, $this->modx->getOption('core_path') . 'components/semanager/');
        $assetsPath = $this->modx->getOption('semanager.assets_path', null, $this->modx->getOption('assets_path') . 'components/semanager/');
        $assetsUrl = $this->modx->getOption('semanager.assets_url', null, $this->modx->getOption('assets_url') . 'components/semanager/');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',
            'controllersPath' => $corePath . 'controllers/',
            'templatesPath' => $corePath . 'templates/',
            // chunks and snippets

            'baseUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imgUrl' => $assetsUrl . 'img/',
            'connectorUrl' => $assetsUrl . 'connector.php',

            'elementsDir' => $this->elementsDir = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/'),
            'filename_tpl_chunk' => $this->elementsDir = $this->modx->getOption('semanager.filename_tpl_chunk', null, 'ch.html'),
            'filename_tpl_plugin' => $this->elementsDir = $this->modx->getOption('semanager.filename_tpl_plugin', null, 'pl.php'),
            'filename_tpl_snippet' => $this->elementsDir = $this->modx->getOption('semanager.filename_tpl_snippet', null, 'sn.php'),
            'filename_tpl_template' => $this->elementsDir = $this->modx->getOption('semanager.filename_tpl_template', null, 'tp.html'),

            'default_filenames' => array(
                'template' => 'tp.html',
                'plugin' => 'pl.php',
                'snippet' => 'sn.php',
                'chunks' => 'ch.html'),
        ), $config);

        $this->modx->addPackage('semanager', $this->config['modelPath']);

        //$this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [__contruct] $corePath: ' . $corePath);

        if ($this->modx->lexicon) {
            $this->modx->lexicon->load('semanager:default');
        }

    }

    /**
     * Initializes SE Manager into different contexts.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     */
    public function initialize($ctx = 'mgr') {
        $output = '';
        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass('semanager.request.SEManagerControllerRequest', $this->config['modelPath'], true, true)) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new SEManagerControllerRequest($this);
                $output = $this->request->handleRequest();
                break;
        }
        return $output;
    }


    /**
     * Make synchronization of all Elements
     */
    public function syncAll() {

        // TODO: перейти на переменную в config
        $this->elementsDir = $this->config['elementsDir'];

        if (!file_exists($this->elementsDir)) {
            $this->_makeDirs($this->elementsDir);
        }

        // 2. проверить настройку - использовать ли типы. если да, то создать папки нужные
        $typeSeparation = $this->modx->getOption('semanager.type_separation', null, true);

        if ($typeSeparation) {

            $dirs = array(
                'modTemplate' => $this->elementsDir . 'templates/',
                'modChunk' => $this->elementsDir . 'chunks/',
                'modSnippet' => $this->elementsDir . 'snippets/',
                'modPlugin' => $this->elementsDir . 'plugins/'
            );

            foreach ($dirs as $type => $dir) {
                $this->_makeDirs($dir);
                $this->manyElementsToStatic($type, $dir);
            }

        } else {

            $types = array(
                'modTemplate',
                'modChunk',
                'modSnippet',
                'modPlugin'
            );

            foreach ($types as $type) {
                $this->manyElementsToStatic($type);
            }

        }

    }

    public function oneElementToStatic($element, $path) {
        $useCategories = $this->modx->getOption('semanager.use_categories', null, true);
        if ($useCategories) {
            $categoriesMap = $this->getCategoriesMap($element->category);
            if ($categoriesMap != '') {
                $path = $path . $categoriesMap . '/';
                $this->_makeDirs($path);
            }
        }

        // TODO: отрефакторить. учесть все возможные БД
        $elementClass = str_replace(array('_mysql', '_sqlsrv'), '', get_class($element));
        $type = strtolower(str_replace('mod', '', $elementClass));
        $filenameTpl = $this->modx->getOption('semanager.filename_tpl_' . $type, null, $this->config['default_filenames'][$type]);

        if ($elementClass == 'modTemplate') {
            $element->set('name', $element->templatename);
        }

        $filePath = $path . $element->name . '.' . $filenameTpl;
        touch($filePath);

        $content = $element->getContent();
        $element->set('static_file', $filePath);
        $element->set('static', true);
        $element->set('source', 0);
        $element->setFileContent($content);

        if ($element->save()) {
            return true;
        } else {
            return false;
        }

    }


    public function checkNewFileForElement($file) {

        $fn = array_reverse(explode('.', array_pop(explode('/', $file))));

        if (count($fn) <= 1) return false; // if file not have extension

        $useSuffixOnly = $this->modx->getOption('semanager.use_suffix_only', null, false);
        $useSuffixOnly = $useSuffixOnly ? true : 2;

        $ext = implode('.', array_reverse(array_slice($fn, 0, $useSuffixOnly))); // extension

        $fnch = $this->modx->getOption('semanager.filename_tpl_chunk', null, 'ch.html');

        if ($ext === $fnch) {
            if (!is_object($this->modx->getObject('modChunk', array('static' => 1, 'static_file' => $file)))) {
                return true;
            }
        }

        $fnpl = $this->modx->getOption('semanager.filename_tpl_plugin', null, 'pl.php');
        if ($ext === $fnpl) {
            if (!is_object($this->modx->getObject('modPlugin', array('static' => 1, 'static_file' => $file)))) {
                return true;
            }
        }

        $fnsn = $this->modx->getOption('semanager.filename_tpl_snippet', null, 'sn.php');
        if ($ext === $fnsn) {
            if (!is_object($this->modx->getObject('modSnippet', array('static' => 1, 'static_file' => $file)))) {
                return true;
            }
        }

        $fntp = $this->modx->getOption('semanager.filename_tpl_template', null, 'tp.html');
        if ($ext === $fntp) {
            if (!is_object($this->modx->getObject('modTemplate', array('static' => 1, 'static_file' => $file)))) {
                return true;
            }
        }

        return false;

    }

    public function getNewFiles() {
        $files = array();
        foreach ($this->scanElementsFolder() as $f) {
            if ($this->checkNewFileForElement($f)) {
                $path = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/');
                $typeSeparation = $this->modx->getOption('semanager.type_separation', null, true);
                $useCategories = $this->modx->getOption('semanager.use_categories', null, true);

                $category = 0;
                $type = '0';

                $filePath = array_reverse(explode('/', str_replace($path, '', $f)));

                $fullCategory = array_reverse($filePath);
                array_shift($fullCategory);
                array_pop($fullCategory);
                $fullCategory = implode('/', $fullCategory);
                $fullCategory = $fullCategory . '/';

                if ($useCategories) {
                    $category = $fullCategory;
                    if ($category == '/') {
                        $category = 0;
                    }
                }

                $filename = array_shift($filePath);

                $this->modx->log(xPDO::LOG_LEVEL_ERROR,'[se manager] [getNewFiles] $filename' . $filename);

                // TODO: добавить дополнительно проверку, если файл не в папке вообще
                if ($typeSeparation) {
                    $type = array_pop($filePath);
                    if ($type == '') {
                        $type = 0;
                    }
                }
                $files[] = array(
                    'filename' => $filename,
                    'category' => $category,
                    'type' => $type,
                    'path' => $f,
                    'content' => file_get_contents($f, true)

                );
            }
        }
        return $files;
    }

    public function scanElementsFolder() {
        $files = array();
        $path = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/');
        $this->_scanFolder($path, $files);

        return $files;
    }

    private function _scanFolder($path, &$files) {
        $d = dir($path);

        while (false != ($e = $d->read())) {
            if ($e != '.' and $e != '..') {
                if (is_dir($d->path . $e)) {
                    $this->_scanFolder($d->path . $e . '/', $files);
                } else {
                    $files[] = $d->path . $e;
                }
            }
        }
        $d->close();
    }

    /**
     * Return type of Element (chunk, plugin, snippet or template)
     *
     * @param $element
     * @return mixed
     */
    private function _getTypeOfElement($element) {
        $config = $this->modx->getConfig();
        $dbtype = $config['dbtype'];
        return str_replace(array($dbtype, 'mod', '_'), '', strtolower(get_class($element)));
    }

    /**
     * Make and return full path to file with element's code
     *
     * @param $element
     * @return mixed|string
     */
    private function _makePath($element) {
        $path = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH . 'elements/');
        $typeSeparation = $this->modx->getOption('semanager.type_separation', null, true);
        $useCategories = $this->modx->getOption('semanager.use_categories', null, true);

        if ($typeSeparation) {   // make subdirectories with name by element's type
            $path .= $this->_getTypeOfElement($element) . 's/';
        }

        if ($useCategories) {    // make subdirectories with category name
            $categoriesMap = $this->getCategoriesMap($element->category);
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
    public function makeStaticElement($element) {

        $path = $this->_makePath($element);
        // $this->modx->log(E_ERROR,  $path);
        $type = $this->_getTypeOfElement($element);

        $filenameTpl = $this->modx->getOption('semanager.filename_tpl_' . $type, null, '');

        if ($type == 'template') {
            $filePath = $path . $element->templatename . '.' . $filenameTpl;
        } else {
            $filePath = $path . $element->name . '.' . $filenameTpl;
        }
        $this->_makeDirs(dirname($filePath));
        touch($filePath);
        $content = $element->getContent();
        $element->set('static_file', $filePath);
        $element->set('static', true);
        $element->setFileContent($content);
        if ($element->save()) {
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
    public function unmakeStaticElement($element) {
        $file_name = $element->get('static_file');
        $content = $element->getContent();
        $element->set('static_file', '');
        $element->set('static', false);
        $element->setContent($content);

        if ($element->save()) {
            unlink($file_name);
            return $element;
        } else {
            return false;
        }
    }

    public function manyElementsToStatic($class_name, $path = '') {
        if ($path == '') {
            $path = $this->elementsDir;
        }

        $elements = $this->modx->getCollection($class_name);

        foreach ($elements as $element) {
            $this->oneElementToStatic($element, $path);
        }
    }

    /**
     * Рекурсивная функция, которая получает полные пути для вложенных категорий
     *
     * @param $id
     * @param array $parents
     * @param array $category_list
     */
    private function _findAllParents($id, array $parents, array $category_list) {
        $parents[] = $category_list[$id]['name'];
        $parent = $category_list[$id]['parent'];
        if ($parent != 0) {
            $this->_findAllParents($parent, $parents, $category_list);
        } else {
            $this->map = $parents;
        }
    }

    /**
     * Get all categories as map for filesystem
     *
     * @param $idCategoryory
     * @return string
     */

    public function getCategoriesMap($idCategoryory) {
        if ($idCategoryory == 0) return '';
        // get all categories
        $categories = $this->modx->getCollection('modCategory');
        $list = array();
        foreach ($categories as $c) {
            $list[$c->id] = array(
                'parent' => $c->parent,
                'name' => $c->category
            );
        }
        $this->_findAllParents($idCategoryory, array(), $list);
        $map_to_path = join('/', array_reverse($this->map));

        return $map_to_path;
    }

    /**
     * Recursive mkdir function
     *
     * @param $strPath
     * @return bool
     */
    private function _makeDirs($strPath) {
        if (is_dir($strPath)) return true;
        $pStrPath = dirname($strPath);
        if (!$this->_makeDirs($pStrPath)) return false;
        return @mkdir($strPath);
    }

    public function parseCategory($category) {
        $idCategory = '';
        if ($category == '0') {
            return '0';
        } else {
            $category = explode('/', $category);
            array_pop($category);
            $prev_id = '0';
            for ($i = 0; $i < sizeof($category); $i++) {
                $categ = $this->modx->getObject('modCategory', array('category' => $category[$i], 'parent' => $prev_id));

                if ($categ) {
                    $idCategory = $categ->id;
                    $prev_id = $categ->id;
                } else {
                    $newCategory = $this->modx->newObject('modCategory');
                    $newCategory->set('parent', $prev_id);
                    $newCategory->set('category', $category[$i]);
                    $newCategory->save();
                    $prev_id = $newCategory->id;
                    $idCategory = $newCategory->id;
                }
            }
        }
        return $idCategory;
    }

    public function newOneElem($path_file, $categoryName) {
        if ($this->checkNewFileForElement($path_file)) {
            $typesClass = array(
                'templates' => array('modTemplate', $this->modx->getOption('semanager.filename_tpl_template', null, 'tp.html')),
                'chunks' => array('modChunk', $this->modx->getOption('semanager.filename_tpl_chunk', null, 'ch.html')),
                'snippets' => array('modSnippet', $this->modx->getOption('semanager.filename_tpl_snippet', null, 'sn.php')),
                'plugins' => array('modPlugin', $this->modx->getOption('semanager.filename_tpl_plugin', null, 'pl.php'))
            );

            $path = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH . '/elements/');
            $typeSeparation = $this->modx->getOption('semanager.type_separation', null, true);
            $useCategories = $this->modx->getOption('semanager.use_categories', null, true);

            $category = 0;
            $type = '0';

            $filePath = array_reverse(explode('/', str_replace($path, '', $path_file)));
            $filename = array_shift($filePath);
            if ($typeSeparation) {
                $type = array_pop($filePath);
                if ($type == '') {
                    $type = 0;
                }
            }
            if ($useCategories) {
                $category = array_shift($filePath);
                if ($category == '') {
                    $category = 0;
                }
            }

            $idCategory = $this->parseCategory($categoryName);
            $newObj = $this->modx->newObject($typesClass[$type][0]);

            $this->setElement($newObj, $path_file, $idCategory);

            if ($type == 'templates') {
                $title = str_replace('.' . $typesClass[$type][1], "", $filename);
                $newObj->set('templatename', $title);
            } else {
                $title = str_replace('.' . $typesClass[$type][1], "", $filename);
                $newObj->set('name', $title);
            }

            $content = file_get_contents($path_file, true);
            $status = $this->saveElement($type, $newObj, $content);

            return $status;
        } else {
            return false;
        }
    }


    public function newElement(array $files = array()) {
        if (!$files) {
            $files = $this->getNewFiles();
        }

        $typesClass = array(
            'templates' => array('modTemplate', $this->modx->getOption('semanager.filename_tpl_template', null, 'tp.html')),
            'chunks' => array('modChunk', $this->modx->getOption('semanager.filename_tpl_chunk', null, 'ch.html')),
            'snippets' => array('modSnippet', $this->modx->getOption('semanager.filename_tpl_snippet', null, 'sn.php')),
            'plugins' => array('modPlugin', $this->modx->getOption('semanager.filename_tpl_plugin', null, 'pl.php'))
        );

        foreach ($files as $filesItem) {
            $idCategory = $this->parseCategory($filesItem['category']);
            $newObj = $this->modx->newObject($typesClass[$filesItem['type']][0]);
            $this->setElement($newObj, $filesItem['path'], $idCategory);

            if ($filesItem['type'] == 'templates') {
                $title = str_replace('.' . $typesClass[$filesItem['type']][1], "", $filesItem['filename']);
                $newObj->set('templatename', $title);
            } else {
                $title = str_replace('.' . $typesClass[$filesItem['type']][1], "", $filesItem['filename']);
                $newObj->set('name', $title);
            }
            $content = file_get_contents($filesItem['path'], true);
            $this->saveElement($filesItem['type'], $newObj, $content);
        }
        return 'true';
    }


    public function setElement ($newObj, $path, $idCategory) {
        $newObj->set('static', '1');
        $newObj->set('source', 0);
        $newObj->set('static_file', $path);
        $newObj->set('category', $idCategory);
    }


    public function saveElement($elementType, $newObj, $content) {
        $typeArray = array('templates', 'snippets', 'plugins', 'chunks');
        foreach ($typeArray as $type) {
            if ($elementType == $type) {
                $newObj->set('content', $content);
            }
        }
        $newObj->save();

        return true;
    }
}
