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
 * Utilities for admin/tool/skin plugin for tweaking the UI.
 *
 * @package     tool_skin
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Page callback to get any paagetype skins for this module instance.
 * @package     tool_skin
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function tool_skin_before_footer() {
    global $PAGE, $USER, $DB;
    $cmid = optional_param('cmid', null, PARAM_INT);
    $id = optional_param('id', null , PARAM_INT);

    $cmid = $cmid ?? $id;

    if (get_config('tool_skin', 'showpagetype')) {
        if (is_siteadmin($USER->id)) {
            $msg = 'page-type mavg:'.$PAGE->pagetype;
            \core\notification::add($msg, \core\notification::WARNING);
        }
    }
    $cache = cache::make('tool_skin', 'skindata');
    if (($pagetypes = $cache->get('pagetypes')) === false) {
        $pagetypes = get_distinct_pagetypes();
        $cache->set('pagetypes', $pagetypes);
    }

    $parts = explode('-', $PAGE->pagetype);
    $plugintype = $parts[0].'-'.$parts[1];

    // Bail out if there are no skins with pagetype, or plugins with pagetype.
    $pagetypeskins = in_array($PAGE->pagetype, $pagetypes);
    $plugintypeskins = in_array($plugintype, $pagetypes);

    if (!$pagetypeskins && !$plugintypeskins) {
        return '';
    }

    $skins = get_skins($PAGE->pagetype);
    $skins = array_merge($skins, get_skins($plugintype));

    foreach ($skins as $skin) {
        $skintags[] = $skin->tag;
    }

    $content = '';
    list($insql, $inparams) = $DB->get_in_or_equal($skintags);
    $sql = "SELECT name as tagname
              FROM {tag_instance} ti
              JOIN {tag} tag
                ON ti.tagid=tag.id
             WHERE tag.name $insql
               AND ti.itemtype='course_modules'
               AND ti.itemid = ?";
    $inparams[] = $cmid;
    $plugintags = $DB->get_records_sql($sql, $inparams);
    if (empty($plugintags)) {
        return false;
    }

    if ($skins) {
        foreach ($skins as $skin) {
            foreach ($plugintags as $tag) {
                if ($skin->tag == $tag->tagname) {
                    $content .= $skin->html. PHP_EOL;
                     $content .= '<script>'.$skin->javascript. '</script>'.PHP_EOL;
                     $content .= '<style>'.$skin->css. '</style>'.PHP_EOL;
                }
            }
        }
    }

    $content = php_get_string($content);
    return $content;
}
/**
 * Give javascript some of the Moodle core
 * get_string capability
 *
 * @param string $content
 * @return string
 */
function php_get_string(string $content) {
    preg_match_all('/get_string\\(.*?\)/', $content, $matches);
    foreach ($matches[0] as $functioncall) {
        $toreplace = $functioncall;
        // Remove spaces and single quotes.
        $functioncall = str_replace([" ", "'"], "", $functioncall);
        // Get content between parentheseis.
        preg_match('/\((.*?)\)/', $functioncall, $matches);
        $params = explode(',', $matches[1]);
        if (count($params) == 1) {
            $string = get_string($params[0]);
        } else {
            $string = get_string($params[0], $params[1]);
        }
        $string = '"'.$string.'"';
        $content = str_replace($toreplace, $string, $content);
    }
    return trim($content, '"');
}

/**
 * Get skins avilable for this pagetype
 *
 * @param string $pagetype
 * @return void
 *
 */
function get_skins(string $pagetype) :array {
    global $DB;
    $sql = 'SELECT skin.id, tag, javascript, css, html FROM {tool_skin} skin
            JOIN {tool_skin_pagetype} pagetype
            ON skin.id = pagetype.skin
            WHERE pagetype.pagetype  IN (
            SELECT pagetype FROM {tool_skin_pagetype} pagetype WHERE
                pagetype.pagetype like  :pagetype
            )';

    $skins = $DB->get_records_sql($sql, ['pagetype' => $pagetype]);
    return $skins;
}

/**
 * Take in a json string, convert to an object
 * and write to the tables
 *
 * @param string $json
 * @return void
 */
function import_json(string $json) : bool {
    $jsonobject = json_decode($json, false);
    global $DB;
    foreach ($jsonobject as $field) {
        $pagetypes = $field->pagetype;
        unset($field->pagetype);
        $skinid = $DB->insert_record('tool_skin', $field);
        foreach ($pagetypes as $pagetype) {
            $DB->insert_record('tool_skin_pagetype', ['skin' => $skinid, 'pagetype' => $pagetype]);
        }
    }
    return true;
}
/**
 * Get unique id values for all pagetypes currently stored
 * for this plugin
 *
 * @return array
 */
function get_distinct_pagetypes() {
    global $DB;
    $pagetypes = $DB->get_records_sql('SELECT DISTINCT pagetype FROM {tool_skin_pagetype}');
    return array_keys($pagetypes);
}
