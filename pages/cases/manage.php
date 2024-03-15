<?php

/**
 * ourcases manage page
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . './../../classes/experience.php');
require_once(__DIR__ . './../../classes/ourcases.php');

use local_dta\Experience;
use local_dta\OurCases;

require_login();

global $CFG, $PAGE, $OUTPUT, $USER;

$experienceid = optional_param('id', 0, PARAM_INT);
$case = optional_param('caseid', 0, PARAM_INT);

$strings = get_strings(['experiences_header', 'experiences_title'], "local_dta");

$PAGE->set_url(new moodle_url('/local/dta/pages/cases/manage.php', ['id' => $experienceid]));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($strings->experiences_title);
$PAGE->requires->js_call_amd('local_dta/ourcases/manage', 'init');

echo $OUTPUT->header();




if ($experienceid) {

    if(!$experience = Experience::get_experience($experienceid)) {
        throw new moodle_exception('invalidcases', 'local_dta');
    }
    
    if (!$ourcase = OurCases::get_case_by_experience($experienceid)) {
        $ourcase = OurCases::add_with_experience($experienceid, date("Y-m-d H:i:s"), $USER->id);
    }

    $sections = array_values(OurCases::get_sections_text($ourcase->id));

    if (!$section_header = OurCases::get_section_header($ourcase->id)) {
        throw new moodle_exception('invalidcasessection', 'local_dta');
    }

    $templateContext = [
        'experience' => $experience,
        'sectionheader' => $section_header,
        'sections' => $sections,
        'ourcase' => $ourcase,
        'url_tiny' => $CFG->wwwroot . '/local/dta/vendor/tinymce/tinymce/tinymce.min.js'
    ];

    echo $OUTPUT->render_from_template('local_dta/cases/manage-with-experience', $templateContext);
}elseif($case){

    if(!$ourcase = OurCases::get_case($case)){
        throw new moodle_exception('invalidcases', 'local_dta');
    };

    $sections = array_values(OurCases::get_sections_text($ourcase->id));
    $section_header = OurCases::get_section_header($ourcase->id);
    $templateContext = [
        'sectionheader' => $section_header,
        'sections' => $sections,
        'ourcase' => $ourcase,
        'url_tiny' => $CFG->wwwroot . '/local/dta/vendor/tinymce/tinymce/tinymce.min.js'
    ];

    echo $OUTPUT->render_from_template('local_dta/cases/manage-without-experience', $templateContext);
}else{

    $ourcase = OurCases::add_without_experience(date("Y-m-d H:i:s"), $USER->id);
    
    if (!$section_header = OurCases::get_section_header($ourcase->id)) {
        throw new moodle_exception('invalidcasessection', 'local_dta');
    }

    $sections = array_values(OurCases::get_sections_text($ourcase->id));
    
    $templateContext = [
        'sectionheader' => $section_header,
        'sections' => $sections,
        'ourcase' => $ourcase,
        'url_tiny' => $CFG->wwwroot . '/local/dta/vendor/tinymce/tinymce/tinymce.min.js'
    ];

    echo $OUTPUT->render_from_template('local_dta/cases/manage-without-experience', $templateContext);

}



echo $OUTPUT->footer();
