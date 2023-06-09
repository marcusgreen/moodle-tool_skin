<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     tool_skin
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Page skin';

$string['skinedit:import'] = 'Import';
$string['skinedit:import_help'] = 'Import help';

$string['skinedit:export'] = 'Export';
$string['skinedit:export_help'] = 'Export help';

$string['skinedit:exportall'] = 'Export all';
$string['skinedit:exportall_help'] = 'Export all help';


$string['skinedit'] = 'Skin edit';
$string['skin'] = 'skin';
$string['attachment'] = 'Attachment';
$string['settings:showpagetype'] = 'Show pagetypes';
$string['settings:showpagetype_text'] = 'The pagetype will be output in the page end if set and the user is an admin. For debug/development';

$string['settings:pagetypes'] = 'Pagetypes';
$string['settings:skinsettings'] = 'Skin config settings';
$string['settings:pagetypes_text'] = 'Comma separated list of pagetypes that can be used';

$string['skinedit:name'] = 'Name';
$string['skinedit:importexportheader'] = 'Import/Export';
$string['skinedit:importexportheader_help'] = 'Import  and export in json format. Export the skin currently being edited or all skins in the plugin';

$string['skinedit:editheader'] = 'Edit';

$string['skinedit:name_help'] = 'Name help';
$string['skinedit:name_required'] = 'Skin name cannot be blank';
$string['skinedit:pagetype_required'] = 'Page type cannot be blank';
$string['skinedit:upload'] = 'Upload';
$string['skinedit:tag_required'] = 'Tag cannot be blank';
$string['skinedit:tag'] = 'Tag';
$string['skinedit:tag_help'] = 'Tag help';
$string['skinedit:pagetype'] = 'Pagetype';
$string['skinedit:pagetype_help'] = 'Pagetype name used internally by Moodle. If the skin setting showpagetype is set, the pagetype will be shown at the end of each page to admin users.';
$string['skinedit:css'] = 'CSS, Ctrl-space for hints';
$string['skinedit:css_help'] = 'CSS help goes here';
$string['skinedit:javascript'] = 'Javascript';
$string['skinedit:javascript_help'] = 'Javascript withoutout opening and closing script tags';
$string['skinedit:html'] = 'HTML';
$string['skinedit:html_help'] = 'HTML put here is output before any javascript in the skin. Put links to Content delivery systems (CDN\'s) here';
$string['cachedef_skin'] = 'Description of the skin data cache';
