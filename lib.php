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
defined('MOODLE_INTERNAL') || die();

function tool_skin_before_footer() {
    global $PAGE, $USER, $DB;
    $cmid = optional_param('cmid', null, PARAM_INT);
    $id = optional_param('id', null , PARAM_INT);
    // function cohort_is_member($cohortid, $userid) {

    $cmid = $cmid ?? $id;
    $cache = cache::make('tool_skin', 'skindata');
    if (($pagetypes = $cache->get('pagetypes')) === false) {
        $pagetypes = get_distinct_pagetypes();
        $cache->set('pagetypes', $pagetypes);
    }
    xdebug_break();

    $allskins = get_all_skins();
    $skins = filter_by_cohort($allskins);
    $skins = filter_by_pagetype($skins);
    $skins = filter_by_tag($skins, $cmid);



    $i=1;
    // $parts = explode('-', $PAGE->pagetype);
    // $plugintype = $parts[0].'-'.$parts[1];

    // Bail out if there are no skins with pagetype, or plugins with pagetype.
    // $pagetypeskins = in_array($PAGE->pagetype, $pagetypes);
    // $plugintypeskins = in_array($plugintype, $pagetypes);

    // if (!$pagetypeskins && !$plugintypeskins) {
    //     return '';
    // }
    // https://docs.moodle.org/dev/Cache_API

    // $usercohorts = cohort_get_user_cohorts($USER->id, true);
   // $plugintags = get_plugintags($cmid);
    //$cohortskins = get_cohort_skins($cmid);

   // $skins = get_pagetype_skins($PAGE->pagetype);
    // $skins = array_merge($skins,$cohortskins);
    // $skins = array_merge($skins, get_skins($plugintype));

    // foreach ($skins as $skin) {
    //     $skintags[] = $skin->tag;
    // }

    // $content = '';
    // return true;
    // $plugintags = get_plugintags($skintags, $cmid);

    // if (empty($plugintags)) {
    //     return false;
    // }
    $content = '';
    if ($skins) {
        foreach ($skins as $skin) {
                     $content .= $skin->html. PHP_EOL;
                     $content .= '<script>'.$skin->javascript. '</script>'.PHP_EOL;
                     $content .= '<style>'.$skin->css. '</style>'.PHP_EOL;
        }
    }

    $content = php_get_string($content);
    return $content;
}

/**
 * Show the page type to the admin user
 * Purely for debug and setup
 */
function show_pagetype() {
    global $USER, $PAGE;
    if (get_config('tool_skin', 'showpagetype')) {
        if (is_siteadmin($USER->id)) {
            $msg = 'page-type:'.$PAGE->pagetype;
            \core\notification::add($msg, \core\notification::WARNING);
        }
    }
}

function get_plugintags($cmid) {
    global $DB;
    $sql = "SELECT name as tagname
              FROM {tag_instance} ti
              JOIN {tag} tag
                ON ti.tagid=tag.id
               AND ti.itemtype='course_modules'
               AND ti.itemid = :cmid";

    $plugintags = $DB->get_records_sql($sql, ['cmid' => $cmid]);
    return $plugintags;
}

function filter_by_tag($allskins, $cmid) {
    $plugintags = get_plugintags($cmid);
    foreach ($allskins as $key => $skin) {
        if ($skin->tag) {
            $i = 0;
            if (!in_array($skin->tag, $plugintags )) {
                unset($allskins[$key]);
            }
        }
    }
    return $allskins;
}

function filter_by_pagetype($allskins) {
    global $PAGE;
    $pagetype = $PAGE->pagetype;
    $parts = explode('-', $PAGE->pagetype);
    $plugintype = $parts[0].'-'.$parts[1];
    foreach ($allskins as $key => $skin) {
        if ($skin->pagetype) {
            if (($skin->pagetype !== $pagetype) && ($skin->pagetype !== $plugintype)) {
                unset($allskins[$key]);
            }
        }
    }
    return $allskins;

}
function filter_by_cohort($allskins) {
    global $DB, $USER;
    $sql = 'SELECT id,userid from {cohort_members}
            WHERE userid = :userid';
    $usercohorts = $DB->get_records_menu('cohort_members', ['userid' => $USER->id], 'id,userid');
    foreach ($allskins as $key => $skin) {
        if ($skin->cohort > 0) {
            if (!in_array($skin->cohort, $usercohorts)) {
                unset($allskins[$key]);
            }

        }
    }
    return $allskins;
}
/**
 * Get skins containing a cohort the current user
 * is in. If the skin has a pagetype check if
 * this any skins have a page type but it does not
 * match the current pagetype remove the skin from
 * the list returned.
 *
 * @return array
 */
function _get_cohort_skins($cmid) : array {
    global $DB, $USER, $PAGE;
    $pagetype = $PAGE->pagetype;

    $sql = 'SELECT skin.id, tag, pagetype, javascript, css, html, cohort
            FROM {tool_skin} skin
            JOIN {cohort_members} cm
            ON skin.cohort=cm.id
            LEFT JOIN {tool_skin_pagetype} pagetype
            ON pagetype.skin = skin.id
            AND cm.userid = :userid';
    $cohortskins = $DB->get_records_sql($sql, ['userid' => $USER->id]);
    $plugintags = get_plugintags($cmid);
    foreach ($cohortskins as $key => $skin) {
        $parts = explode('-', $PAGE->pagetype);
        $plugintype = $parts[0].'-'.$parts[1];
        if ($skin->pagetype) {
            if (($skin->pagetype !== $pagetype) && ($skin->pagetype !== $plugintype)) {
                unset($cohortskins[$key]);
            }
        }
        if($skin->tag) {

        }
    }
    return $cohortskins;
}


/**
 * Get skins avilable for this pagetype
 *
 * @param string $pagetype
 * @return void
 *
 */
function get_pagetype_skins(string $pagetype) :array {
    $parts = explode('-', $pagetype);
    $plugintype = $parts[0].'-'.$parts[1];

    global $DB;
    $sql = 'SELECT skin.id, tag, javascript, css, html FROM {tool_skin} skin
            JOIN {tool_skin_pagetype} pagetype
            ON skin.id = pagetype.skin
            WHERE pagetype.pagetype  IN (
            SELECT pagetype FROM {tool_skin_pagetype} pagetype WHERE
                pagetype.pagetype =  :pagetype or pagetype.pagetype = :plugintype
            )';

    $skins = $DB->get_records_sql($sql, ['pagetype' => $pagetype, 'plugintype' => $plugintype]);
    return $skins;
}


function get_all_skins() {
    global $DB;
    $sql = 'SELECT skin.id, skinname, cohort,tag,pagetype FROM {tool_skin} skin
            LEFT JOIN {tool_skin_pagetype} pagetype on pagetype.skin=skin.id';
    $allskins = $DB->get_recordset_sql($sql);
    $skins = [];
    foreach ($allskins as $skin) {
        $skins[] = $skin;
    }
    return $skins;
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
 * Take in a json string, convert to an object
 * and write to the tables
 *
 * @param string $json
 * @return void
 */
function import_json(string $json) : int {
    $jsonobject = json_decode($json, false);
    global $DB;
    $recordcount = 0;
    foreach ($jsonobject as $field) {
        $recordcount++;
        $pagetypes = $field->pagetype;
        unset($field->pagetype);
        $skinid = $DB->insert_record('tool_skin', $field);
        foreach ($pagetypes as $pagetype) {
            $DB->insert_record('tool_skin_pagetype', ['skin' => $skinid, 'pagetype' => $pagetype]);
        }
    }
    return $recordcount;
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
