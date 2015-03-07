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
$_lang['semanager.description'] = 'Verwaltung für statische Elemente';
$_lang['semanager.sync.description'] = "Synchronisiert statische Elemente und überwacht Ordner auf geänderte Dateien";




// Tab Files
$_lang['semanager.tabs.actions'] = "Neue Dateien";
$_lang['semanager.common.actions.files.quickupdate'] = "Datei schnell bearbeiten";
$_lang['semanager.common.actions.files.generate.all'] = "Elemente erstellen";
$_lang['semanager.common.actions.create.processing'] = "Erstelle Elemente von Dateien ...";

$_lang['semanager.common.actions.create.element'] = "Neues Element erstellen";
$_lang['semanager.common.actions.create.element.confirm'] = "Aus der ausgewählten Datei ein Element erstellen?";
$_lang['semanager.common.actions.create.processing'] = 'Erstelle Elemente...';

$_lang['semanager.common.actions.files.generate'] = "Element erstellen";
$_lang['semanager.common.actions.files.quickupdate'] = "Datei bearbeiten";
$_lang['semanager.common.actions.files.delete.element'] = "Datei und Element löschen";
$_lang['semanager.common.actions.files.delete.file'] = "Datei löschen";

$_lang['semanager.common.actions.files.deletefile.confirm.title'] = " Datei löschen";
$_lang['semanager.common.actions.files.deletefile.confirm.text'] = "Die ausgewählte Dateie wird endgültig löschen?";



// Element tabs chunks, plugins, snippets, templates
$_lang['semanager.common.actions.elements.tostatic.all'] = "All as static";
$_lang['semanager.common.actions.elements.tostatic.all.confirm.title'] = "Nicht-statischen Elemente konvertieren";
$_lang['semanager.common.actions.elements.tostatic.all.confirm.text'] = "Alle nicht-statischen Elemente werden konvertiert und auf dem Server jeweils als Datei erstellt.";

$_lang['semanager.common.actions.element.status.unchanged'] = "Keine Änderungen";
$_lang['semanager.common.actions.element.status.changed'] = "Datei wurde geändert";
$_lang['semanager.common.actions.element.status.deleted'] = "Datei wurde gelöscht";

$_lang['semanager.common.actions.element.quickupdate'] = "Element bearbeiten";

$_lang['semanager.common.actions.element.static'] = "Als statisches Element speichern";
$_lang['semanager.common.actions.element.static.restore'] = "Datei aus Datenbank wiederherstellen";

$_lang['semanager.common.actions.elements.sync.tofile'] = "Datei mit Inhalt von Element synchronisieren";
$_lang['semanager.common.actions.elements.sync.fromfile'] = "Element mit Inhalt von Datei synchronisieren";

$_lang['semanager.common.actions.elements.restore.tofile'] = "Datei mit Inhalt von Element wiederherstellen";
$_lang['semanager.common.actions.elements.restore.tofile.confirm.title'] = "Datei wieder herstellen";
$_lang['semanager.common.actions.elements.restore.tofile.confirm.text'] = "Die Fehlende Datei wird mit dem Inhalt des aktuellen Element wiederhergestellt.";

$_lang['semanager.common.actions.elements.sync.tofile.confirm.title'] = "Element zu File Synchronisation";
$_lang['semanager.common.actions.elements.sync.tofile.confirm.text'] = "Das File auf dem Server wird mit dem Inhalt des Element aktualisiert.";

$_lang['semanager.common.actions.elements.sync.fromfile.confirm.title'] = "File zu Element Synchronisation";
$_lang['semanager.common.actions.elements.sync.fromfile.confirm.text'] = "Das Element wird mit dem Inhalt des Files vom Server aktualisiert.";

$_lang['semanager.common.actions.element.static.confirm.title'] = "Als statisches Element festlegen";
$_lang['semanager.common.actions.element.static.confirm.text'] = "Das ausgewählte Element als statisches Element festlegen?";

$_lang['semanager.common.actions.element.delete'] = "Element Löschen";
$_lang['semanager.common.actions.element.delete.confirm.title'] = "Element löschen";
$_lang['semanager.common.actions.element.delete.confirm.text'] = "Das ausgewählte Element wirklich löschen?";

$_lang['semanager.common.actions.element.deletefile_element'] = "Datei und Element löschen";
$_lang['semanager.common.actions.element.deletefile_element.confirm.title'] = "Datei und Element löschen";
$_lang['semanager.common.actions.element.deletefile_element.confirm.text'] = "Datei und Element endgültig löschen?";


$_lang['semanager.common.actions.element.refresh'] = "Geändertes Element aktualisieren";





$_lang['semanager.common.actions.allsync'] = "Alles synchronisieren";
$_lang['semanager.common.actions.synchronizing'] = "Synchronizing...";


$_lang['semanager.elements.filter_by_name'] = "Nach Name filtern";
$_lang['semanager.elements.filter_by_category'] = "Nach Kategorie filtern";

$_lang['semanager.elements.filter_by_type'] = "Nach Typ filtern";

$_lang['semanager.elements.static'] = "Statisch";
$_lang['semanager.elements.file'] = "Dateipfad des Elements";
$_lang['semanager.elements.make_static_file'] = "Als statisches Element";
$_lang['semanager.elements.remove_static_file'] = "Von statischen Dateien entfernen";
$_lang['semanager.elements.exclude_element'] = "Element auslassen";

$_lang['semanager.elements.exclude_element'] = "Element auslassen";


$_lang['semanager.no_permission'] = 'Keine Berechtigung';


// Systemsettings
$_lang['semanager.elements_dir'] = 'Elements directory';
$_lang['semanager.elements_mediasource'] = 'Mediasource folder';
$_lang['semanager.filename_tpl_chunk'] = 'Suffix for Chunks';
$_lang['semanager.filename_tpl_plugin'] = 'Suffix for Plugins';
$_lang['semanager.filename_tpl_snippet'] = 'Suffix for Snippets';
$_lang['semanager.filename_tpl_template'] = 'Suffix for Templates';
$_lang['semanager.use_mediasources'] = 'Use mediasource';
$_lang['semanager.use_suffix_only'] = 'Use simple file suffix only';
$_lang['semanager.auto_create_elements'] = 'Auto create elements';
$_lang['semanager.type_separation'] = 'Typ-Seperation nach Ordner';
$_lang['semanager.use_categories'] = 'Order als Kategorien';