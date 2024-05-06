<?php

/**
 * community page
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once (__DIR__ . '/../../../../config.php');
require_once (__DIR__ . './../../classes/resource.php');
require_once (__DIR__ . './../../classes/utils/string_utils.php');
require_once (__DIR__ . './../../classes/utils/filter_utils.php');

require_login();

use local_dta\Resource;
use local_dta\utils\filter_utils;

global $CFG, $PAGE, $OUTPUT;

$strings = get_strings(['repository_header', 'repository_title'], "local_dta");

// Setea el título de la página
$PAGE->set_url(new moodle_url('/local/dta/pages/repository/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($strings->repository_title);
$PAGE->requires->js_call_amd('local_dta/resources/manage_resources', 'init', array(false));

echo $OUTPUT->header();

function getResourceType($type)
{
    switch ($type) {
        case 'IMAGE':
            return ['isImage' => true];
        case 'VIDEO':
            return ['isVideo' => true];
        case 'URL':
            return ['isUrl' => true];
        case 'DOCUMENT':
            return ['isDocument' => true];
        default:
            return [];
    }
}

$resources = Resource::get_all_resources();

foreach ($resources as &$resource) {
    $typeData = getResourceType($resource->type);
    $resource = (object) array_merge((array) $resource, $typeData);
}

$template_context = [
    'resources' => $resources,
];

$template_context = filter_utils::apply_filter_to_template_object($template_context);

echo $OUTPUT->render_from_template('local_dta/repository/dashboard', $template_context);

echo $OUTPUT->footer();
