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
 * WebService to handler the tutor request acceptance.
 *
 * @package   local_digitalta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/digitalta/classes/tutors.php');

use local_digitalta\Tutors;

/**
 * This class is used to create tags
 *
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_tutoring_requests_accept extends external_api
{

    public static function requests_accept_parameters()
    {
        return new external_function_parameters(
            [
                'requestid' => new external_value(PARAM_INT, 'Tutor id'),
                'acceptance' => new external_value(PARAM_BOOL, 'Acceptance')
            ]
        );
    }

    public static function requests_accept($requestid, $acceptance)
    {
        $result = $acceptance ? Tutors::requests_accept($requestid) : Tutors::reject_tutor_request($requestid);
        return [
            'result' => $result,
            'success' => true
        ];
    }

    public static function requests_accept_returns()
    {
        return
            new external_single_structure(
                [
                    'result' => new external_value(PARAM_BOOL, 'Result'),
                    'success' => new external_value(PARAM_BOOL, 'Success')
                ]
            );
    }
}
