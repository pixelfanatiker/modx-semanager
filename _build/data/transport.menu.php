<?php
/**
 * Adds modActions and modMenus into package
 *
 * @package semanager
 * @subpackage build
 */

/* -- for modx 2.3 dev -- */
/*
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'action' => 'index',
    'namespace' => 'semanager',
    'text' => 'semanager.title',
    'description' => 'semanager.description',
    'parent' => 'components',
    'icon' => '',
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
),'',true,true);
return $menu;*/

$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'semanager',
    'parent' => 0,
    'controller' => 'home',
    'haslayout' => 1,
    'lang_topics' => 'semanager:default',
    'assets' => '',
),'',true,true);

/* load action into menu */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'semanager.title',
    'parent' => 'components',
    'description' => 'semanager.description',
    'icon' => '',
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
),'',true,true);
$menu->addOne($action);
unset($action);

return $menu;