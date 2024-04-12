<?php

/**
 * community page
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . './../../classes/experience.php');
require_once(__DIR__ . './../../classes/utils/string_utils.php');

require_login();

use local_dta\Experience;
use local_dta\utils\StringUtils;

global $CFG, $PAGE, $OUTPUT;

$strings = get_strings(['experiences_header', 'experiences_title'], "local_dta");

// Setea el título de la página
$PAGE->set_url(new moodle_url('/local/dta/pages/experiences/dashboard.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($strings->experiences_title);
$PAGE->requires->js_call_amd('local_dta/myexperience/reactions', 'init');

echo $OUTPUT->header();

$experiences = Experience::get_all_experiences(false);
$experiences = array_map(function ($experience) {
    $experience->description = StringUtils::truncateHtmlText($experience->description, 1000);
    return $experience;
}, $experiences);

$user = get_complete_user_data("id", $USER->id);
$picture = new user_picture($user);
$picture->size = 101;
$user->imageurl = $picture->get_url($PAGE)->__toString();

$template_context = [
    "user" => $user,
    "experiences" => [
        "data" => $experiences,
        "showcontrolsadmin" => is_siteadmin($USER),
        "addurl" => $CFG->wwwroot . "/local/dta/pages/experiences/manage.php",
        "viewurl" => $CFG->wwwroot . '/local/dta/pages/experiences/view.php?id='
    ],
];

echo $OUTPUT->render_from_template('local_dta/experiences/dashboard/dashboard', $template_context);

echo $OUTPUT->footer();
