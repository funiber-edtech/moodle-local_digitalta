<?php

/**
 * external_delete_meeting
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/dta/classes/googlemeet/helper.php');

use local_dta\helper;

class external_delete_meeting extends external_api
{

    public static function delete_meeting_parameters()
    {
        return new external_function_parameters(
            [
                'chatid' => new external_value(PARAM_INT, 'Chat id', VALUE_REQUIRED)
            ]
        );
    }

    public static function delete_meeting($chatid)
    {
        try {
            $deleted_meeting = helper::delete_googlemeet_record($chatid);
            return $deleted_meeting;
        } catch (\Exception $e) {
            throw new moodle_exception('error_deleting_meeting', 'local_dta', null, $e->getMessage());
        }
    }

    public static function delete_meeting_returns()
    {
        return new external_value(PARAM_BOOL, 'Record found and deleted');
    }
}