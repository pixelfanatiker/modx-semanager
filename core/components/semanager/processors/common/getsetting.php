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
 * @subpackage processors
 */

if (!isset($modx->semanager) || !is_object($modx->semanager)) {
    $semanager = $modx->getService('semanager','SEManager',$modx->getOption('semanager.core_path',null,$modx->getOption('core_path').'components/semanager/').'model/semanager/', $scriptProperties);
    if (!($semanager instanceof SEManager)) return '---';
}

if (!$modx->hasPermission('view')) {return $this->failure($modx->lexicon('semanager.no_permission'));}

$settings_name = $modx->getOption('settings_name', $_REQUEST, '');

if($settings_name != ''){

    $full_settings_name = 'semanager.' . $settings_name;
    $default = array_key_exists($settings_name, $modx->semanager->config)? $modx->semanager->config[$settings_name] : '';

    $value = $modx->getOption($full_settings_name, null, $default);

    return $value;

}
