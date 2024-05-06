<?php

/**
 * Resource class.
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This file controls DTA resource as an instance of the repository and digital_resource table in the database
// NOT TO BE CONFUSED WITH THE FILEMANAGER HANDLER OR MOODLE FILE API

namespace local_dta;

require_once(__DIR__ . '/constants.php');

class Mentor {
    /** @var int The ID of the mentor. */
    private $id;
    private static $table = 'user'; // TODO: 

    /**
     * Get a mentor by its ID.
     * 
     * @param $id int The ID of the mentor.
     * 
     * @return object The mentor object.
     */
    public static function get_mentor(int $id) : object{
        global $DB;
        return $DB->get_record(self::$table, ['id' => $id]);
    }

    /**
     * Get all mentors.
     * 
     * @return array The mentors.
     */
    public static function get_all_mentors() : array{
        global $DB;
        return array_values($DB->get_records(self::$table));
    }
}



