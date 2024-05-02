<?php

/**
 * external_resources_save
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . './../../resource.php');


use local_dta\Resource;

class external_resources_get extends external_api
{
    // TODO - Add the parameters here (filters)
    public static function resources_get_parameters()
    {
        return new external_function_parameters(
            array(
            )
        );
    }

    public static function resources_get()
    {
        global $USER;

        $resources = Resource::get_all_resources($USER->id);

        print_object($resources);

        return [
            'result' => true,
            'resources' => $resources,
        ];
    }

    public static function resources_get_returns()
    {
        return new external_single_structure(
            [
                'result' => new external_value(PARAM_BOOL, 'Result'),
                'resources' => new external_value(PARAM_INT, 'Resources' , VALUE_OPTIONAL),
                'error' => new external_value(PARAM_RAW, 'Error message' , VALUE_OPTIONAL),
            ]
        );
    }
}
