<?php
/**
 * Loads system settings into build
 *
 * @package semanager
 * @subpackage build
 */

$settings = array();

$settings[0]= $modx->newObject('modSystemSetting');
$settings[0]->fromArray(array(
'key' => 'semanager.elements_dir',
'value' => '{assets_path}elements/',
'xtype' => 'textfield',
'namespace' => 'semanager',
'area' => 'Files',
),'',true,true);

$settings[1]= $modx->newObject('modSystemSetting');
$settings[1]->fromArray(array(
    'key' => 'semanager.filename_tpl_chunk',
    'value' => 'ch.html',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[2]= $modx->newObject('modSystemSetting');
$settings[2]->fromArray(array(
    'key' => 'semanager.filename_tpl_plugin',
    'value' => 'pl.php',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[3]= $modx->newObject('modSystemSetting');
$settings[3]->fromArray(array(
    'key' => 'semanager.filename_tpl_snippet',
    'value' => 'sn.php',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[4]= $modx->newObject('modSystemSetting');
$settings[4]->fromArray(array(
    'key' => 'semanager.filename_tpl_template',
    'value' => 'tp.html',
    'xtype' => 'textfield',
    'namespace' => 'semanager',
    'area' => 'Files',
),'',true,true);

$settings[5]= $modx->newObject('modSystemSetting');
$settings[5]->fromArray(array(
    'key' => 'semanager.type_separation',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'semanager',
    'area' => 'Other',
),'',true,true);

$settings[6]= $modx->newObject('modSystemSetting');
$settings[6]->fromArray(array(
    'key' => 'semanager.use_categories',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'semanager',
    'area' => 'Other',
),'',true,true);

return $settings;
