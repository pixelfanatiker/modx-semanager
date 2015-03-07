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
 * @subpackage lexicon
 *
 */

$_lang['semanager.title'] = 'SE Manager';
$_lang['semanager.description'] = 'Manager for extra control of Static Elements';
$_lang['semanager.sync.description'] = "Synchronizes all elements and scans directory for new files";



// Tab Files
$_lang['semanager.tabs.actions'] = "Activity";
$_lang['semanager.common.actions.files.quickupdate'] = "Quick update file";
$_lang['semanager.common.actions.files.generate.all'] = "Create elements";
$_lang['semanager.common.actions.create.processing'] = "Creating elements ...";

$_lang['semanager.common.actions.allsync'] = "Synchronize all";
$_lang['semanager.common.actions.synchronizing'] = "Synchronizing...";

$_lang['semanager.common.actions.create.element'] = "Create new element";
$_lang['semanager.common.actions.create.element.confirm'] = "Create from the selected file a new element?";

$_lang['semanager.common.actions.files.generate'] = "Create element";
$_lang['semanager.common.actions.files.quickupdate'] = "uick update file";
$_lang['semanager.common.actions.files.delete.element'] = "Delete file and element";
$_lang['semanager.common.actions.files.delete.file'] = "Delete file";

$_lang['semanager.common.actions.files.deletefile.confirm.title'] = " Delete file";
$_lang['semanager.common.actions.files.deletefile.confirm.text'] = "Finally delete the selected file?";

$_lang['semanager.common.actions.files.generate.all'] = "Create elements from files";
$_lang['semanager.common.actions.files.generate'] = "Create element from file";
$_lang['semanager.common.actions.deletefile'] = "Delete file";




// Element tabs chunks, plugins, snippets, templates
$_lang['semanager.common.actions.elements.tostatic.all'] = "All as static";
$_lang['semanager.common.actions.elements.tostatic.all.confirm.title'] = "Convert non-static elements";
$_lang['semanager.common.actions.elements.tostatic.all.confirm.text'] = "All non-static elements will be converted to each own files on the server.";

$_lang['semanager.common.actions.element.status.unchanged'] = "No changes";
$_lang['semanager.common.actions.element.status.changed'] = "File has changed";
$_lang['semanager.common.actions.element.status.deleted'] = "File has been deleted";

$_lang['semanager.common.actions.element.quickupdate'] = "Quick update element";

$_lang['semanager.common.actions.element.static'] = "Save as static element";
$_lang['semanager.common.actions.element.static.restore'] = "Restore file from database";

$_lang['semanager.common.actions.elements.sync.tofile'] = "Sync file with content from element";
$_lang['semanager.common.actions.elements.sync.fromfile'] = "Sync element with content from file";

$_lang['semanager.common.actions.elements.restore.tofile'] = "Restore file with content from element";
$_lang['semanager.common.actions.elements.restore.tofile.confirm.title'] = "Restore file";
$_lang['semanager.common.actions.elements.restore.tofile.confirm.text'] = "The missing file will be restored with the content from the element";

$_lang['semanager.common.actions.elements.sync.tofile.confirm.title'] = "Element zu File Synchronisation";
$_lang['semanager.common.actions.elements.sync.tofile.confirm.text'] = "The file on the server will be synced with the content from the element.";

$_lang['semanager.common.actions.elements.sync.fromfile.confirm.title'] = "File to element synchronization";
$_lang['semanager.common.actions.elements.sync.fromfile.confirm.text'] = "The element will be updated from the file.";

$_lang['semanager.common.actions.element.static'] = "As static element";
$_lang['semanager.common.actions.element.static.confirm.title'] = "Convert to static element";
$_lang['semanager.common.actions.element.static.confirm.text'] = "The selected element will be saved as static element and a file will be created.";

$_lang['semanager.common.actions.element.delete'] = "Delete element";
$_lang['semanager.common.actions.element.delete.confirm.title'] = "Delete element";
$_lang['semanager.common.actions.element.delete.confirm.text'] = "The selected element will be deleted.";

$_lang['semanager.common.actions.element.deletefile_element'] = "Delete file and element";
$_lang['semanager.common.actions.element.deletefile_element.confirm.title'] = "Delete file and element";
$_lang['semanager.common.actions.element.deletefile_element.confirm.text'] = "Finally delete file and element?";


// Filter
$_lang['semanager.elements.filter_by_name'] = "Filter by name";
$_lang['semanager.elements.filter_by_category'] = "Filter by category";
$_lang['semanager.elements.filter_by_type'] = "Filter by type";



$_lang['semanager.elements.static'] = "Static";
$_lang['semanager.elements.file'] = "File of the element";
$_lang['semanager.elements.make_static_file'] = "Make static";
$_lang['semanager.elements.remove_static_file'] = "Remove static file";
$_lang['semanager.elements.exclude_element'] = "Exclude an element";


// Messages
$_lang['semanager.no_permission'] = 'Not permission';


// Systemsettings
$_lang['semanager.elements_dir'] = 'Base elements directory';
$_lang['semanager.elements_mediasource'] = 'Mediasource for elements';
$_lang['semanager.filename_tpl_chunk'] = 'Suffix for Chunks';
$_lang['semanager.filename_tpl_plugin'] = 'Suffix for Plugins';
$_lang['semanager.filename_tpl_snippet'] = 'Suffix for Snippets';
$_lang['semanager.filename_tpl_template'] = 'Suffix for Templates';
$_lang['semanager.use_mediasources'] = 'Use mediasource';
$_lang['semanager.use_suffix_only'] = 'Use simple file suffix only';
$_lang['semanager.auto_create_elements'] = 'Auto create elements';
$_lang['semanager.type_separation'] = 'Seperate element-types throug folders';
$_lang['semanager.use_categories'] = 'Use folder as categories';
