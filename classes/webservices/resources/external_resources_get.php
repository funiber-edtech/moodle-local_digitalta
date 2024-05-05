<?php

/**
 * external_resources_save
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/dta/classes/resources.php');

use local_dta\Resources;

class external_resources_get extends external_api
{
    // TODO - Add the parameters here (filters)
    public static function resources_get_parameters()
    {
        return new external_function_parameters(
            []
        );
    }

    public static function resources_get()
    {
        $resources = Resources::get_all_resources();
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
                'error' => new external_value(PARAM_RAW, 'Error message' , VALUE_OPTIONAL),
                'resources' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'ID'),
                            'userid' => new external_value(PARAM_INT, 'User ID'),
                            'name' => new external_value(PARAM_TEXT, 'Name'),
                            'description' => new external_value(PARAM_TEXT, 'Description'),
                            'themes' => new external_multiple_structure(
                                new external_value(PARAM_TEXT, 'Themes')
                            ),
                            'tags' => new external_multiple_structure(
                                new external_value(PARAM_TEXT, 'Tags')
                            ),
                            'type' => new external_value(PARAM_INT, 'Type'),
                            'format' => new external_value(PARAM_INT, 'Format'),
                            'path' => new external_value(PARAM_TEXT, 'Path'),
                            'lang' => new external_value(PARAM_TEXT, 'Language'),
                            'timecreated' => new external_value(PARAM_TEXT, 'Time created'),
                            'timemodified' => new external_value(PARAM_TEXT, 'Time modified'),
                        ]
                    )
                ),
            ]
        );
    }
}
