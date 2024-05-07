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
 * Experience managem page
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . './../../../../config.php');
require_once($CFG->dirroot . '/local/dta/classes/experiences.php');
require_once($CFG->dirroot . '/local/dta/classes/tinyeditorhandler.php');
require_once($CFG->dirroot . '/local/dta/classes/files/filemanagerhandler.php');

use local_dta\Experiences;
use local_dta\TinyEditorHandler;
use local_dta\file\FileManagerHandler;

require_login();

global $CFG, $PAGE, $OUTPUT , $USER;

$experience_title = optional_param('experiencetitle', "", PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

// Seting the page url and context
$PAGE->set_url(new moodle_url('/local/dta/pages/experiences/manage.php', ['id' => $id]));
$PAGE->set_context(context_system::instance());
$PAGE->requires->js_call_amd('local_dta/myexperience/manage/form', 'init');

echo $OUTPUT->header();

// Set tiny configs in DOM
(new TinyEditorHandler)->get_config_editor(['maxfiles' => 1]);
// Set filemanager in M variable
(new FileManagerHandler($id ?? null))->init("experience_picture");

if (!$id) {
    $template_context = [
        "experience" => [
            "title" => $experience_title
        ]
    ];
} else {
    $experience = Experiences::get_experience($id);

    if (!$experience) {
        throw new moodle_exception('Experience not found');
    }
    $template_context = [
        "experience" => $experience
    ];

}

echo $OUTPUT->render_from_template('local_dta/experiences/manage/form', $template_context);

echo $OUTPUT->footer();
