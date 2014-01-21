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
 
class SEManager {

    public $modx = null;

    public $config = array();

    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config=array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('semanager.core_path', null, $this->modx->getOption('core_path').'components/semanager/');
        $assetsPath = $this->modx->getOption('semanager.assets_path', null, $this->modx->getOption('assets_path').'components/semanager/');
        $assetsUrl = $this->modx->getOption('semanager.assets_url', null, $this->modx->getOption('assets_url').'components/semanager/');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'processorsPath' => $corePath.'processors/',
            'controllersPath' => $corePath.'controllers/',
            'templatesPath' => $corePath.'templates/',
            // chunks and snippets

            'baseUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'imgUrl' => $assetsUrl.'img/',
            'connectorUrl' => $assetsUrl.'connector.php',

            'elements_dir' => $this->elements_dir = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH.'/elements/'),
            'filename_tpl_chunk' => $this->elements_dir = $this->modx->getOption('semanager.filename_tpl_chunk', null, 'ch.html'),
            'filename_tpl_plugin' => $this->elements_dir = $this->modx->getOption('semanager.filename_tpl_plugin', null, 'pl.php'),
            'filename_tpl_snippet' => $this->elements_dir = $this->modx->getOption('semanager.filename_tpl_snippet', null, 'sn.php'),
            'filename_tpl_template' => $this->elements_dir = $this->modx->getOption('semanager.filename_tpl_template', null, 'tp.html'),

            'default_filenames' => array(
                'template'  => 'tp.html',
                'plugin'    => 'pl.php',
                'snippet'   => 'sn.php',
                'chunks'    => 'ch.html'),
        ),$config);

        $this->modx->addPackage('semanager', $this->config['modelPath']);

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
    public function initialize($ctx='mgr'){
        $output = '';
        switch($ctx){
            case 'mgr':
                if (!$this->modx->loadClass('semanager.request.SEManagerControllerRequest',$this->config['modelPath'],true,true)) {
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
    public function syncAll(){

        // TODO: перейти на переменную в config
        $this->elements_dir = $this->config['elements_dir'];

        if (!file_exists($this->elements_dir)){
            $this->_makeDirs($this->elements_dir);
        }

        // 2. проверить настройку - использовать ли типы. если да, то создать папки нужные
        $type_separation = $this->modx->getOption('semanager.type_separation', null, true);

        if($type_separation){

            $dirs = array(
                'modTemplate' => $this->elements_dir . 'templates/',
                'modChunk'    => $this->elements_dir . 'chunks/',
                'modSnippet'  => $this->elements_dir . 'snippets/',
                'modPlugin'   => $this->elements_dir . 'plugins/'
            );

            foreach($dirs as $type => $dir){
                $this->_makeDirs($dir);
                $this->manyElementsToStatic($type, $dir);
            }

        }else{

            $types = array(
                'modTemplate',
                'modChunk',
                'modSnippet',
                'modPlugin'
            );

            foreach($types as $type){
                $this->manyElementsToStatic($type);
            }

        }

    }

    public function oneElementToStatic($element, $path){

        $use_categories = $this->modx->getOption('semanager.use_categories', null, true);

        if($use_categories){

            $categories_map = $this->getCategoriesMap($element->category);

            if($categories_map != ''){

                $path = $path . $categories_map . '/';
                $this->_makeDirs($path);

            }

        }

        // TODO: отрефакторить. учесть все возможные БД
        $element_class = str_replace(array('_mysql','_sqlsrv'), '', get_class($element));

        $type = strtolower(str_replace('mod', '', $element_class));

        $filename_tpl = $this->modx->getOption('semanager.filename_tpl_' . $type, null, $this->config['default_filenames'][$type]);

        if($element_class == 'modTemplate'){
            $element->set('name', $element->templatename);

            //$this->modx->log(E_ERROR, 'E: '.serialize($element->toArray()));
        }

        $file_path = $path .  $element->name.'.'. $filename_tpl;
        //$this->modx->log(E_ERROR, $filename_tpl);
        touch($file_path);

        $content = $element->getContent();
        $element->set('static_file', $file_path);
        $element->set('static', true);
        $element->setFileContent($content);

        if($element->save()){
            return true;
        }else{
            return false;
        }

    }


    public function checkNewFileForElement($file){

        $fn = array_reverse(explode('.', array_pop(explode('/',$file))));

        if(count($fn) <= 1) return false; // if file not have extension

        $ext = implode('.', array_reverse(array_slice($fn, 0, 2))); // extension

        $fnch = $this->modx->getOption('semanager.filename_tpl_chunk', null, 'ch.html');
        if($ext === $fnch){
            if(!is_object($this->modx->getObject('modChunk', array('static' => 1,'static_file' => $file)))){
                return true;
            }
        }

        $fnpl = $this->modx->getOption('semanager.filename_tpl_plugin', null, 'pl.php');
        if($ext === $fnpl){
            if(!is_object($this->modx->getObject('modPlugin', array('static' => 1,'static_file' => $file)))){
                return true;
            }
        }

        $fnsn = $this->modx->getOption('semanager.filename_tpl_snippet', null, 'sn.php');
        if($ext === $fnsn){
            if(!is_object($this->modx->getObject('modSnippet', array('static' => 1,'static_file' => $file)))){
                return true;
            }
        }

        $fntp = $this->modx->getOption('semanager.filename_tpl_template', null, 'tp.html');
        if($ext === $fntp){
            if(!is_object($this->modx->getObject('modTemplate', array('static' => 1,'static_file' => $file)))){
                return true;
            }
        }

        return false;

    }

    public function getNewFiles(){

        $files = array();

        foreach($this->scanElementsFolder() as $f){

            if($this->checkNewFileForElement($f)){

                $path = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH.'/elements/');
                $type_separation = $this->modx->getOption('semanager.type_separation', null, true);
                $use_categories = $this->modx->getOption('semanager.use_categories', null, true);

                $category = 0;
                $type = '0';

                $file_path = array_reverse(explode('/',str_replace($path, '', $f)));

                $filename = array_shift($file_path);

                //$this->modx->log(E_ERROR, $filename);

                // TODO: добавить дополнительно проверку, если файл не в папке вообще
                if($type_separation){
                    $type = array_pop($file_path);
                    if($type == ''){
                        $type = 0;
                    }
                }
                //$type = ($type_separation)? array_pop($file_path) : 'None';

                if($use_categories){
                    $category = array_shift($file_path);
                    if($category == ''){
                        $category = 0;
                    }
                }



                $files[] = array(
                    'filename' => $filename,
                    'category' => $category,
                    'type' => $type,
                    'path' => $f

                );
            }
        }
        //$this->modx->log(E_ERROR, count($files));
        return $files;

    }

    public function scanElementsFolder(){

        $files = array();
        $path = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH.'/elements/');

        $this->_scanFolder($path, $files);

        return $files;

    }

    private function _scanFolder($path, &$files) {
        $d = dir($path);


        while(false != ($e = $d->read())) {

            if ($e != '.' and $e != '..'){

                if(is_dir($d->path.$e)){
                    $this->_scanFolder($d->path.$e.'/', &$files);
                }else{

                    $files[] = $d->path.$e;
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
    private function _getTypeOfElement($element){
        $config = $this->modx->getConfig();
        $dbtype = $config['dbtype'];
        return str_replace(array($dbtype,'mod','_'), '', strtolower(get_class($element)));
    }

    /**
     * Make and return full path to file with element's code
     *
     * @param $element
     * @return mixed|string
     */
    private function _makePath($element){

        $path = $this->modx->getOption('semanager.elements_dir', null, MODX_ASSETS_PATH.'/elements/');
        $type_separation = $this->modx->getOption('semanager.type_separation', null, true);
        $use_categories = $this->modx->getOption('semanager.use_categories', null, true);

        if($type_separation){   // make subdirectories with name by element's type
            $path .= $this->_getTypeOfElement($element).'s/';
        }

        if($use_categories){    // make subdirectories with category name
            $categories_map = $this->getCategoriesMap($element->category);
            if($categories_map != ''){
                $path .= $categories_map . '/';
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
    public function makeStaticElement($element){

        $path = $this->_makePath($element);
        $type = $this->_getTypeOfElement($element);

        $filename_tpl = $this->modx->getOption('semanager.filename_tpl_' . $type, null, '');

        if($type == 'template'){
            $file_path = $path . $element->templatename.'.'. $filename_tpl;
        }else{
            $file_path = $path . $element->name .'.'.$filename_tpl;
        }

        $this->_makeDirs(dirname($file_path));

        touch($file_path);

        $content = $element->getContent();
        $element->set('static_file', $file_path);
        $element->set('static', true);

	    $element->setFileContent($content);

    	//$this->modx->log(E_ERROR, 'E: '.serialize($element->toArray()));

        if($element->save()){
            return $element;
        }else{
            return false;
        }

    }

    /**
     * Unmake static element. Make dynamic element. Remove static file
     *
     * @param $element
     * @return bool
     */
    public function unmakeStaticElement($element){

        $file_name = $element->get('static_file');

        $content = $element->getContent();
        $element->set('static_file', '');
        $element->set('static', false);
        $element->setContent($content);

        if($element->save()){
            unlink($file_name);
            return $element;
        }else{
            return false;
        }

    }

    public function manyElementsToStatic($class_name, $path = ''){

        if($path == ''){
            $path = $this->elements_dir;
        }

        $elements = $this->modx->getCollection($class_name);

        foreach($elements as $element){

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
    private function _findAllParents($id, array $parents, array $category_list){
        $parents[] = $category_list[$id]['name'];
        $parent = $category_list[$id]['parent'];
        if($parent != 0){
            $this->_findAllParents($parent, &$parents, $category_list);
        }
    }

    /**
     * Get all categories as map for filesystem
     *
     * @param $id_category
     * @return string
     */
    public function getCategoriesMap($id_category){

        if($id_category == 0) return '';

        // get all categories
        $categories = $this->modx->getCollection('modCategory');
        $list = array();
        foreach($categories as $c){
            $list[$c->id] = array(
                'parent'    => $c->parent,
                'name'      => $c->category
            );
        }

        $map = array();
        $this->_findAllParents($id_category, &$map, $list);

        $map_to_path = join('/',array_reverse($map));

        return $map_to_path;
    }

    /**
     * Recursive mkdir function
     *
     * @param $strPath
     * @return bool
     */
    private function _makeDirs($strPath){
        if (is_dir($strPath)) return true;
        $pStrPath = dirname($strPath);
        if (!$this->_makeDirs($pStrPath)) return false;
        return @mkdir($strPath);
    }

    public function newElem(){
        $files = $this->getNewFiles();
        $typesClass = array(
           'templates'=>array('modTemplate','.tp.html'),
           'chunks' =>array('modChunk','.ch.html'),
           'snippets' =>array('modSnippet','.sn.php'),
           'plugins' =>array('modPlugin','.pl.php')
        );

        foreach($files as $files_item){

            $newObj = $this->modx->newObject($typesClass[$files_item['type']][0]);
           // $this->modx->log(E_ERROR,$typesClass[$files_item['type']][0]);
            $newObj->set('static','1');
            $newObj->set('static_file',$files_item['path']);
            if($files_item['type'] == 'templates'){
                $title = str_replace($typesClass[$files_item['type']][1], "", $files_item['filename']);
                $newObj->set('templatename',$title);
            }else{
                $title = str_replace($typesClass[$files_item['type']][1], "", $files_item['filename']);
                $newObj->set('name',$title);
            }
            $content = file_get_contents($files_item['path'], true);
            if($files_item['type'] == 'templates'){
                $newObj->set('content',$content);
            }
            if($files_item['type'] == 'snippets'){
                $newObj->set('snippet',$content);
            }
            if($files_item['type'] == 'plugins'){
                $newObj->set('plugincode',$content);
            }
            if($files_item['type'] == 'chunks'){
                $newObj->set('snippet',$content);
            }
            $newObj->save();
        }
        //$this->modx->log(E_ERROR,'E: '.serialize($files));
        return 'true';
    }

}
