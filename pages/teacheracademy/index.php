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
require_once(__DIR__ . './../../classes/tags.php');
require_once(__DIR__ . './../../classes/ourcases.php');
require_once(__DIR__ . './../../classes/reactions.php');
require_once(__DIR__ . './../../classes/utils/string_utils.php');

require_login();

use local_dta\Experience;
use local_dta\Tags;
use local_dta\OurCases;
use local_dta\Reaction;
use local_dta\utils\StringUtils;

global $CFG, $PAGE, $OUTPUT;

$strings = get_strings(['teacheracademy_header', 'teacheracademy_title'], "local_dta");

// Setea el título de la página
$PAGE->set_url(new moodle_url('/local/dta/pages/teacheracademy/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($strings->teacheracademy_title);
$PAGE->requires->js_call_amd('local_dta/myexperience/manageReactions', 'init');

echo $OUTPUT->header();

$experiences = Experience::get_all_experiences(false);
$experiences = array_map(function ($experience) {
    $experience->description = StringUtils::truncateHtmlText($experience->description, 600);
    return $experience;
}, $experiences);

// Latest experiences
$latestExperiences = Experience::get_latest_experiences(false);
$latestExperiences = array_map(function ($experience) {
    $experience->description = StringUtils::truncateHtmlText($experience->description);
    return $experience;
}, $latestExperiences);
// Featured Experiences
$featuredExperiences = Experience::get_extra_fields(Reaction::get_most_liked_experience(5));
$featuredExperiences = array_map(function ($experience) {
    $experience->description = StringUtils::truncateHtmlText($experience->description, 350);
    return $experience;
}, $featuredExperiences);

$tags = Tags::getAllTags();
$allCases = array_values(OurCases::get_active_cases());

$cases = array();

for ($i = 0; $i < count($allCases); $i++) {
    $caseText = OurCases::get_sections_text($allCases[$i]->id, true);

    $newCase = [
        "id" => $allCases[$i]->id,
        "experienceid" => $allCases[$i]->experienceid,
        "userid" => $allCases[$i]->userid,
        "date" => $allCases[$i]->timecreated,
        "status" => $allCases[$i]->status,
        "casetext" => array_values($caseText)[0],
    ];

    array_push($cases, $newCase);
}

$cases = array_map(function ($case) {
    $case['casetext']->description = str_replace("<br>", " ", StringUtils::truncateHtmlText($case['casetext']->description, 100));
    return $case;
}, $cases);

$user = get_complete_user_data("id", $USER->id);
$picture = new user_picture($user);
$picture->size = 101;
$user->imageurl = $picture->get_url($PAGE)->__toString();


$templateContext = [
    "user" => $user,
    "themepixurl" => $CFG->wwwroot . "/theme/dta/pix/",
    "experiences" => [
        "data" => $experiences,
        "latest" => $latestExperiences,
        "featured" => $featuredExperiences,
        "showimageprofile" => true,
        "showcontrols" => false,
        "showcontrolsadmin" => is_siteadmin($USER),
        "addurl" => $CFG->wwwroot . "/local/dta/pages/experiences/manage.php",
        "viewurl" => $CFG->wwwroot . '/local/dta/pages/experiences/view.php?id=',
        "allurl" => $CFG->wwwroot . "/local/dta/pages/experiences/dashboard.php",
    ],
    "tags" => $tags,
    "ourcases" => [
        "cases" => array_slice($cases, 0, 4),
        "viewurl" => $CFG->wwwroot . "/local/dta/pages/cases/view.php?id=",
        "allurl" => $CFG->wwwroot . "/local/dta/pages/cases/repository.php"
    ]
];

echo $OUTPUT->render_from_template('local_dta/teacheracademy/dashboard', $templateContext);

echo $OUTPUT->footer();
