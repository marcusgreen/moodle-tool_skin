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
 * Plugin internal classes, functions and constants are defined here.
 * @package     admin_skin
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function tool_skin_before_footer() {
    global $PAGE, $DB;
    $pagetypes = explode(',', get_config('tool_skin', 'pagetypes'));
    if (!in_array($PAGE->pagetype, $pagetypes)) {
       // return '';
    }
    $skins = $DB->get_records('tool_skin', ['pagetype' => $PAGE->pagetype]);
    if (!$skins) {
        return;
    }
    foreach ($skins as $skin) {
        $skintags[] = $skin->tag;
    }

    $content = '';
    $cmid = $PAGE->url->params()['cmid'];
    list($insql, $inparams) = $DB->get_in_or_equal($skintags);
    $sql = "SELECT name as tagname
              FROM {tag_instance} ti
              JOIN {tag} tag
                ON ti.tagid=tag.id
             WHERE tag.name $insql
               AND ti.itemid = ?";
    $inparams[] = $cmid;
    $pagetags = $DB->get_records_sql($sql, $inparams);
    if ($skins) {
        foreach ($skins as $skin) {
            foreach ($pagetags as $tag) {
                if ($skin->tag == $tag->tagname) {
                     $content .= $skin->code;
                }
            }
        }
    }
    $content = php_get_string($content);
    return $content;
}
function php_get_string(string $content) {
    preg_match_all('/get_string\\(.*?\)/', $content, $matches);
    foreach ($matches[0] as $functioncall) {
        $toreplace = $functioncall;
        // Remove spaces and single quotes.
        $functioncall = str_replace([" ", "'"], "", $functioncall);
        // Get content between parentheseis.
        preg_match('/\((.*?)\)/', $functioncall, $matches);
        $params = explode(',', $matches[1]);
        $string = get_string($params[0], $params[1]);
        $string = '"'.$string.'"';
        $content = str_replace($toreplace, $string, $content);
    }
    return $content;

}
