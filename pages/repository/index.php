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
 * Repository index page
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot . '/local/dta/classes/resources.php');
require_once($CFG->dirroot . '/local/dta/classes/themes.php');
require_once($CFG->dirroot . '/local/dta/classes/tags.php');
require_once($CFG->dirroot . '/local/dta/classes/utils/stringutils.php');
require_once($CFG->dirroot . '/local/dta/classes/utils/filterutils.php');
require_once($CFG->dirroot . '/local/dta/locallib.php');

require_login();

use local_dta\Resources;
use local_dta\Themes;
use local_dta\Tags;
use local_dta\utils\FilterUtils;

global $CFG, $PAGE, $OUTPUT;

$strings = get_strings(['repository_header', 'repository_title'], "local_dta");

// Setea el título de la página
$PAGE->set_url(new moodle_url('/local/dta/pages/repository/index.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($strings->repository_title);

// Get all types
$type_options = Resources::get_types();
$type_options = array_values(array_map(function ($type) {
    return [
        'value' => $type->id,
        'text' => local_dta_get_element_translation('resource_type', $type->name)
    ];
}, $type_options));

// Get all themes
$theme_options = Themes::get_themes();
$theme_options = array_values(array_map(function ($theme) {
    return [
        'value' => $theme->id,
        'text' => local_dta_get_element_translation('theme', $theme->name)
    ];
}, $theme_options));

// Get all tags
$tag_options = Tags::get_tags();
$tag_options = array_values(array_map(function ($tag) {
    return [
        'value' => $tag->id,
        'text' => local_dta_get_element_translation('tag', $tag->name)
    ];
}, $tag_options));

// Load the JS
$options = [
    'change' => false,
    'types' => $type_options,
    'format_id' => Resources::get_format_by_name('Link')->id,
    'themes' => $theme_options,
    'tags' => $tag_options
];
$PAGE->requires->js_call_amd('local_dta/resources/manage_resources', 'init', [$options]);

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

$resources = Resources::get_all_resources();

foreach ($resources as &$resource) {
    $typeData = getResourceType($resource->type);
    $resource = (object) array_merge((array) $resource, $typeData);
}

$template_context = [
    'resources' => $resources,
];

$template_context = FilterUtils::apply_filter_to_template_object($template_context);

echo $OUTPUT->render_from_template('local_dta/repository/dashboard', $template_context);

echo $OUTPUT->footer();
