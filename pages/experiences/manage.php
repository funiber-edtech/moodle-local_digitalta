<?php

/**
 * Reflection page
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . './../../../../config.php');
require_once(__DIR__ . './../../classes/tiny_editor_handler.php');
require_once(__DIR__ . './../../classes/files/filemanager_handler.php');
require_once($CFG->dirroot . '/local/dta/classes/experiences.php');


use local_dta\tiny_editor_handler;
use local_dta\file\filemanager_handler;
use local_dta\Experiences;

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
(new tiny_editor_handler)->get_config_editor(['maxfiles' => 1]);
// Set filemanager in M variable
(new filemanager_handler($id ?? null))->init("experience_picture");

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
