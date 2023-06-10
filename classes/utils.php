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
    public static function get_skin_json(array $data) : string {
        $json = '[';
        foreach ($data as $record) {
            $text['skinname'] = $record->skinname;
            $text['tag'] = $record->tag;
            $text['javascript'] = $record->javascript;
            $text['css'] = $record->css;
            $text['html'] = $record->html;
            foreach ($record->pagetypes as $pagetype) {
                $text['pagetype'][] = $pagetype;
            }
        }
        $json .= json_encode($text, JSON_PRETTY_PRINT);
        $json .= ']';
        return $json;
    }
    public static function import_skin_file(string $filename) {
        global $DB, $CFG;
        $filecontent = file_get_contents($CFG->wwwroot.'/'.$filename);
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

}