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
 * Tool skin inport skin
 *
 * @package    tool_skin
 * @copyright  2023 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Install some example skins
 */
function xmldb_tool_skin_install() {
    global $DB, $CFG;
    $filecontent = file_get_contents($CFG->dirroot."/admin/tool/skin/db/skins.json");
    $rows = json_decode($filecontent);
    foreach ($rows as $row) {
        if (!$row->skinname) {
            continue;
        }
        $pagetypes = $row->pagetype;
        unset($row->pagetype);
        $skinid = $DB->insert_record('tool_skin', $row);
        foreach ($pagetypes as $pagetype) {
            $DB->insert_record('tool_skin_pagetype', ['skin' => $skinid, 'pagetype' => $pagetype]);
        }
    }

}

