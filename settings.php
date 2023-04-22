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
 * Set default values for a new install
 *
 * @package     tool_skin
 * @category    admin
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (is_siteadmin()) {
    $ADMIN->add('tools', new admin_category('skincategory', get_string('pluginname', 'tool_skin')));
    $settingspage = new admin_settingpage('skinsettings' , get_string('settings:skinconfigsettings', 'tool_skin'));
    $ADMIN->add('skincategory', $settingspage);

    $settingspage->add(new admin_setting_configtextarea('tool_skin/pagetypes',
        get_string('settings:pagetypes', 'tool_skin'),
        get_string('settings:pagetypes_text', 'tool_skin'),
        "mod-quiz-attempt, mod-quiz-review",
         PARAM_RAW, 20, 3));

    $ADMIN->add('skincategory',
        new admin_externalpage(
                'tool_skin_edit',
                    get_string('skinedit', 'tool_skin'),
                    new moodle_url('/admin/tool/skin/db/skin_edit.php'),
                'moodle/site:config'
        ));
}
