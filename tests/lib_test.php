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
 * Unit tests for tool_skin lib
 *
 * @package    tool_skin
 * @copyright  2023 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_skin;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Test tool_skin functions
 */
class lib_test extends \advanced_testcase {

    /**
     * Parse javascript for get_strings like php
     * @covers ::php_get_string()
     * @return void
     */
    public function test_php_get_string() {
        $content = "get_string('changesaved')";
        $returnstring = php_get_string($content);
        $returnval = true;
        return $returnval;
    }
}
