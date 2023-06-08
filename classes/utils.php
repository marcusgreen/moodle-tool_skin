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
namespace tool_skin;
// require(__DIR__.'/../../../config.php');

class utils {
    /**
     * Get all skin fields in json format for export/download
     * @param int $skinid
     * @return string
     */
    public static function get_skin_json(int $skinid) : string {
        global $DB;
        $record = $DB->get_record('tool_skin', ['id' => $skinid]);
        $json['skinname'] = $record->skinname;
        $json['tag'] = $record->tag;
        $json['javascript'] = $record->javascript;
        $json['css'] = $record->css;
        $json['html'] = $record->html;

        $json['pagetype'] = $DB->get_records_menu('tool_skin_pagetype', ['skin' => $record->id], null, 'id,pagetype');
        $text = '[';
        $text .= json_encode($json, JSON_PRETTY_PRINT);
        $text .= ']';
        return $text;
    }

}