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
 *
 * Edit themes form
 *
 * This does the same as the standard xml import but easier
 * @package    tool_skin
 * @copyright  2023 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'../../lib.php');

use tool_skin\import_form;
use tool_skin\utils;

$page   = optional_param('page', 0, PARAM_INT);
$newrecord = optional_param('newrecord', '', PARAM_TEXT);
$save = optional_param('save', '', PARAM_TEXT);
$delete = optional_param('delete', '', PARAM_TEXT);

$jsonsubmit = optional_param('jsonsubmit', '', PARAM_TEXT);
$savejson = optional_param('savejson', '', PARAM_TEXT);
$import = optional_param('import', '', PARAM_TEXT);
$export = optional_param('export', '', PARAM_TEXT);
$exportall = optional_param('exportall', '', PARAM_TEXT);


$context = context_system::instance();
$PAGE->set_context($context);
admin_externalpage_setup('tool_skin_edit');

/**
 *  Edit tool_skin code
 *
 * @copyright Marcus Green 2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * Form for editing skin (css and javascript)
 */
class tool_skin_edit_form extends moodleform {
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pagetypes = [];

    /**
     * Interface elements of the editing form.
     */
    protected function definition() {
        global $PAGE;
        $mform = $this->_form;
        // Add the popup CSS hints on pressing ctrl space.
        $PAGE->requires->css('/admin/tool/skin/amd/src/codemirror/lib/codemirror.css');

        $PAGE->requires->css('/admin/tool/skin/amd/src/codemirror/addon/hint/show-hint.css');
        $PAGE->requires->js_call_amd('tool_skin/skin_edit', 'init');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $navbuttons = [];
        $navbuttons[] = $mform->createElement('submit', 'save', get_string('save'));
        $navbuttons[] = $mform->createElement('submit', 'cancel', get_string('cancel'));
        $navbuttons[] = $mform->createElement('submit', 'newrecord', get_string('new'));
        $navbuttons[] = $mform->createElement('submit', 'delete', get_string('delete'));

        $mform->addGroup($navbuttons);

        $mform->addElement('header', 'importexportheader', get_string('skinedit:importexportheader', 'tool_skin'));
        $mform->addHelpButton('importexportheader', 'skinedit:importexportheader', 'tool_skin');

        $mform->setExpanded('importexportheader', false);

        $mform->addElement('submit', 'import', get_string('skinedit:import', 'tool_skin'));
        $mform->addHelpButton('import', 'skinedit:import', 'tool_skin');

        $mform->addElement('submit', 'export', get_string('skinedit:export', 'tool_skin'));
        $mform->addHelpButton('export', 'skinedit:export', 'tool_skin');

        $mform->addElement('submit', 'exportall', get_string('skinedit:exportall', 'tool_skin'));
        $mform->addHelpButton('exportall', 'skinedit:exportall', 'tool_skin');

        $mform->addElement('header', 'editheader', get_string('skinedit:editheader', 'tool_skin'));

        $mform->addElement('text', 'skinname', get_string('name'));
        $mform->setType('skinname', PARAM_TEXT);
        $mform->addHelpButton('skinname', 'skinedit:name', 'tool_skin');
        $mform->addRule('skinname',  get_string("skinedit:name_required", 'tool_skin'), 'required', '', 'server');

        $options['multiple'] = true;
        $options['tags'] = true;

        $pagetypes = array_map('trim', explode(',', get_config('tool_skin', 'pagetypes')));
        $pagetypes = array_combine($pagetypes, $pagetypes);
        $mform->addElement('autocomplete', 'pagetypes', get_string('skinedit:pagetype', 'tool_skin') , $pagetypes, $options);
        $mform->addHelpButton('pagetypes', 'skinedit:pagetype', 'tool_skin');
        $mform->addRule('pagetypes',  get_string("skinedit:pagetype_required", 'tool_skin'), 'required', '', 'server');

        $mform->addElement('text', 'tag', get_string('tag'));
        $mform->setType('tag', PARAM_TEXT);
        $mform->addHelpButton('tag', 'skinedit:tag', 'tool_skin');

        $mform->addElement('textarea', 'css', get_string('skinedit:css', 'tool_skin'), ['rows' => 15, 'cols' => 80]);
        $mform->addHelpButton('css', 'skinedit:css', 'tool_skin');
        $mform->setType('css', PARAM_RAW);

        $mform->addElement('textarea', 'javascript', get_string('skinedit:javascript', 'tool_skin'), ['rows' => 15, 'cols' => 80]);
        $mform->addHelpButton('javascript', 'skinedit:javascript', 'tool_skin');
        $mform->setType('javascript', PARAM_RAW);

        $mform->addElement('textarea', 'html', get_string('skinedit:html', 'tool_skin'), ['rows' => 10, 'cols' => 80]);
        $mform->addHelpButton('html', 'skinedit:html', 'tool_skin');
        $mform->setType('html', PARAM_RAW);

    }
    /**
     * Sets the data before the form is displayed
     *
     * @param Object $skin
     * @return void
     */
    public function set_data($skin) {
        $this->_form->getElement('id')->setValue($skin->id);
        $this->_form->getElement('skinname')->setValue($skin->skinname ?? "");
        $this->_form->getElement('tag')->setValue($skin->tag ?? "");
        $this->_form->getElement('css')->setValue($skin->css ?? "");
        $this->_form->getElement('javascript')->setValue($skin->javascript ?? "");
        $this->_form->getElement('html')->setValue($skin->html ?? "");
        $this->_form->getElement('pagetypes')->setValue($skin->pagetypes);
    }
}
$importform = new  import_form();
if ($data = $importform->get_data()) {
    $content = $importform->get_file_content('jsonfile');
    import_json($content);
}
$recordcount = $DB->count_records('tool_skin');

if ($recordcount == 0 || $newrecord) {
    $id = $DB->insert_record('tool_skin', (object) ['skinname' => '', 'css' => '']);
    $record = $DB->get_record('tool_skin', ['id' => $id]);
    $page = $DB->count_records('tool_skin');
    $page --;
}

$recordcount = $DB->count_records('tool_skin');

$record = get_page_record($page);

if ($delete ) {
    $DB->delete_records('tool_skin', ['id' => $record->id]);
    $DB->delete_records('tool_skin_pagetype', ['skin' => $record->id]);
    $page--;
    $record = get_page_record($page);
    $recordcount = $DB->count_records('tool_skin');
    if ($recordcount == 0) {
        $id = $DB->insert_record('tool_skin', (object) ['skinname' => '', 'css' => '']);
        $record = $DB->get_record('tool_skin', ['id' => $id]);
        $recordcount = 1;
    }
}
$baseurl = new moodle_url('/admin/tool/skin/db/skin_edit.php', ['page' => $page]);

$record->page = $page;

$mform = new tool_skin_edit_form($baseurl);

if ($data = $mform->get_data()) {
    if (isset($data->save)) {
            $params = [
                'id' => $data->id,
                'skinname' => $data->skinname,
                'tag' => $data->tag,
                'css' => $data->css,
                'javascript' => $data->javascript,
                'html' => $data->html
            ];
            $DB->update_record('tool_skin', $params);
            update_pagetypes($data);
            $record = $DB->get_record('tool_skin', ['id' => $data->id]);
    }
    if (isset($data->upload)) {
        $upload = true;
    }
    if (isset($data->export)) {
        do_download($data->id);
    }
    if (isset($data->exportall)) {
        do_download();
    }
}


$record = get_pagetypes($record);

$mform->set_data($record);

echo $OUTPUT->header();
if ($import) {
    $importform = new  import_form();
    $importform->display();
} else {
    echo $OUTPUT->paging_bar($recordcount, $page, 1, $baseurl);
    $mform->display();
}
if ($export) {
    do_download($data);
}
if (!$export) {
    echo $OUTPUT->footer();
}


/**
 * Get the database record to match the current page
 *
 * @param int $page
 * @return \stdClass
 */
function get_page_record(int $page) : \stdClass {
    global $DB;
    $record = (object) [];
    $recordset = $DB->get_recordset('tool_skin');
    $count = 0;
    foreach ($recordset as $key => $value) {
        if ($count == $page) {
            $record = $value;
            break;
        }
        $count++;
    }
    return $record;
}
/**
 * If no page types given, delete any existing ones
 * Otherwise insert the ones given
 * @param mixed $data
 * @return void
 */
function  update_pagetypes($data) {
    global $DB;
    if (!$data->pagetypes) {
        $DB->delete_records('tool_skin_pagetype', ['skin' => $data->id]);
        return;
    }
    $DB->delete_records('tool_skin_pagetype', ['skin' => $data->id]);
    foreach ($data->pagetypes as $pagetype) {
         $DB->insert_record('tool_skin_pagetype', ['skin' => $data->id, 'pagetype' => $pagetype]);
    }
}

/**
 * Get the pagetypes (the skin will act on) for a given
 * skin record
 *
 * @param stdClass $record
 * @return stdClass
 */
function get_pagetypes(stdClass $record) {
    global $DB;
    $pagetypes = $DB->get_records_menu('tool_skin_pagetype', ['skin' => $record->id], '', 'id, pagetype');
    $pagetypes = array_combine($pagetypes, $pagetypes);
    if (count($pagetypes) > 0 ) {
        $record->pagetypes = $pagetypes;
    } else {
        $record->pagetypes = [];
    }
    return $record;
}
/**
 * Down load the skin related to the given id
 * in json format so it can be re-used
 * @param int|null $id
 * @return never
 */
function do_download(int $id = null) {
    global $DB;
    $filename = '';
    if ($id) {
        $data = $DB->get_records('tool_skin', ['id' => $id]);
        $filename = reset($data)->skinname;
    } else {
         $data = $DB->get_records('tool_skin');
         $filename = 'skins';
    }
    $json = '[';
    $recordcount = count($data);
    foreach ($data as $key => $record) {
        $text['skinname'] = $record->skinname;
        $text['tag'] = $record->tag;
        $text['javascript'] = $record->javascript;
        $text['css'] = $record->css;
        $text['html'] = $record->html;
        $text['pagetype'] = $DB->get_records_menu('tool_skin_pagetype', ['skin' => $record->id], null, 'id,pagetype');
        $json .= json_encode($text, JSON_PRETTY_PRINT);
        if ($key < $recordcount) {
            $json .= ',';
        }
    }
    $json .= ']';

    $filename .= '.json';
    header('Content-disposition: attachment; filename=' . $filename);
    header('Content-Type: application/json; charset: utf-8');
    echo $json;
    exit;
}
