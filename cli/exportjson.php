<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Toggle read_only status on and off from the command line
 *
 * @package tool_skin
 * @copyright  2023 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

$params = cli_get_params([], []);
global $DB, $CFG;
$records = $DB->get_records('tool_skin');

$recordcount = count($records);
$text = '['.PHP_EOL;
$json = [];
foreach ($records as $key => $record) {
    $json['skinname'] = $record->skinname;
    $json['tag'] = $record->tag;
    $json['javascript'] = $record->javascript;
    $json['css'] = $record->css;
    $json['html'] = $record->html;

    $json['pagetype'] = $DB->get_records_menu('tool_skin_pagetype', ['skin' => $record->id], null, 'id,pagetype');
    $text .= json_encode($json, JSON_PRETTY_PRINT);
    if ($key < $recordcount) {
        $text .= ',';
    }
}
$text .= PHP_EOL.']';
echo $text;

