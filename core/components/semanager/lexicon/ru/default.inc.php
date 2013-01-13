<?php
/**
 *
 * SE Manager
 *
 * Copyright 2012 by Ivan Klimchuk <ivan@klimchuk.com>
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
 **/
$_lang['semanager.title'] = 'SE Manager';
$_lang['semanager.description'] = 'Модуль для расширенного контроля статических элементов';

$_lang['semanager.tabs.actions'] = "Действия";
$_lang['semanager.tabs.actions.desc'] = "Описание действия";
$_lang['semanager.tabs.settings'] = "Настройки";
$_lang['semanager.tabs.settings.desc'] = "Описание настройки";
//-- chunks, plugins, shippets, templates - default in core lexicon

$_lang['semanager.common.actions.alltofiles'] = "Все в файлы";
$_lang['semanager.common.actions.alltodb'] = "Все в базу данных";
$_lang['semanager.common.actions.fromfiles'] = "Загрузить из файлов";

$_lang['semanager.common.fs'] = "Настройки файлов";

$_lang['semanager.common.fs.elements_dir'] = "Каталог с элементами";
$_lang['semanager.common.fs.elements_dir_desc'] = "Папка, куда компонент складывает все элементы";

$_lang['semanager.common.fs.filename_tpl_chunk'] = "Имя файла чанка";
$_lang['semanager.common.fs.filename_tpl_chunk_desc'] = "Шаблон для имен файл чанков, где {name} заменяется на имя элемента.";

$_lang['semanager.common.fs.filename_tpl_plugin'] = "Имя файла плагина";
$_lang['semanager.common.fs.filename_tpl_plugin_desc'] = "Шаблон для имен файл плагинов, где {name} заменяется на имя элемента.";

$_lang['semanager.common.fs.filename_tpl_snippet'] = "Имя файла сниппета";
$_lang['semanager.common.fs.filename_tpl_snippet_desc'] = "Шаблон для имен файл сниппетов, где {name} заменяется на имя элемента.";

$_lang['semanager.common.fs.filename_tpl_template'] = "Имя файла шаблона";
$_lang['semanager.common.fs.filename_tpl_template_desc'] = "Шаблон для имен файл шаблонов, где {name} заменяется на имя элемента.";

$_lang['semanager.common.os'] = "Другие настройки";

$_lang['semanager.common.os.use_categories'] = 'Использовать категории';
$_lang['semanager.common.os.use_categories_desc'] = 'Если да, то при сохранении элементов будут создаваться папки с именами категорий, внутри которых будут сохраняться элементы.';

$_lang['semanager.common.os.type_separation'] = 'Разделение по типам';
$_lang['semanager.common.os.type_separation_desc'] = 'Если да, то элементы будут сохранятся в паки согласно типам: чанки в chunks, сниппеты в snippets и т.д.';

$_lang['semanager.excluded'] = "Исключения";

$_lang['semanager.elements.filter_by_name'] = "Фильтр по имени";
$_lang['semanager.elements.filter_by_category'] = "Фильтр по категории";

$_lang['semanager.elements.static'] = "Статичный";
$_lang['semanager.elements.file'] = "Файл элемента";
$_lang['semanager.elements.make_static_file'] = "Сделать статичным";
$_lang['semanager.elements.remove_static_file'] = "Удалить статичный файл";
$_lang['semanager.elements.exclude_element'] = "Исключить элемент";


$_lang['semanager.no_permission'] = 'Нет доступа';

/* settings */

$_lang['setting_semanager.elements_dir'] = 'Папка с элементами';
$_lang['setting_semanager.elements_dir_desc'] = 'Путь к папке с элементами. Можно указывать плейсхолдеры вида {assets}';

$_lang['setting_semanager.filename_tpl_chunk'] = 'Шаблон имени файла для чанка';
$_lang['setting_semanager.filename_tpl_chunk_desc'] = 'При сохранении на диск имя файла формируется по заданному шаблону';

$_lang['setting_semanager.filename_tpl_plugin'] = 'Шаблон имени файла для плагина';
$_lang['setting_semanager.filename_tpl_plugin_desc'] = 'При сохранении на диск имя файла формируется по заданному шаблону';

$_lang['setting_semanager.filename_tpl_snippet'] = 'Шаблон имени файла для сниппета';
$_lang['setting_semanager.filename_tpl_snippet_desc'] = 'При сохранении на диск имя файла формируется по заданному шаблону';

$_lang['setting_semanager.filename_tpl_template'] = 'Шаблон имени файла для шаблона';
$_lang['setting_semanager.filename_tpl_template_desc'] = 'При сохранении на диск имя файла формируется по заданному шаблону';

$_lang['setting_semanager.type_separation'] = 'Разделение по типам';
$_lang['setting_semanager.type_separation_desc'] = 'Если да, то элементы будут сохранятся в паки согласно типам: чанки в chunks, сниппеты в snippets и т.д.';

$_lang['setting_semanager.use_categories'] = 'Использовать категории';
$_lang['setting_semanager.use_categories_desc'] = 'Если да, то при сохранении элементов будут создаваться папки с именами категорий, внутри которых будут сохраняться элементы.';
