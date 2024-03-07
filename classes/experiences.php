<?php

/**
 * Experience class
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dta;

use stdClass;

class Experience
{
    private static $table = 'digital_experiences';
    private $db;
    private $title;
    private $description;
    private $date;
    private $lang;

    /** @var string The picture draft id of the experience */
    private $picture;

    /** @var string The picture url of the experience */
    private $pictureurl;

    private $visible;


    /**
     * Constructor.
     * 
     * @param $data array The data to populate the experience with.
     */
    public function __construct($experience = null)
    {
        foreach ($experience as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = ($key === 'description' && is_array($value))
                    ? $value['text']
                    : $value;
            }
        }

    }

    /**
     * Get an experience by this id
     * 
     * @param bool $includePrivates 
     * @return array|null
     */
    public static function getAllExperiences($includePrivates = true)
    {
        global $DB;
        $experiences = array_values($DB->get_records(self::$table, $includePrivates ? null : ['visible' => 1]));
        return $experiences;
    }

    /**
     * Add a new experience to the database and return the new experience
     *
     * @param string $title
     * @param string $description
     * @param string $date
     * @param string $lang
     * @param bool $visible
     * @return Experience|bool
     */
    public static function addExperience($title, $description, $date, $lang, $user, $visible = 1)
    {
        global $DB;
        if (empty($title) || empty($description) || empty($date) || empty($lang)) {
            return false;
        }

        $record = new stdClass();
        $record->title = $title;
        $record->description = $description;
        $record->date = $date;
        $record->lang = $lang;
        $record->visible = $visible;
        $record->user = $user;

        if (!$id = $DB->insert_record(self::$table, $record)) {
            throw new Exception('Error adding experience');
        }

        $record->id = $id;

        return new Experience($record);
    }

    /**
     * Update an existing experience
     *
     * @param object $experience
     * @return bool
     */
    public static function updateExperience($experience)
    {
        if (empty($experience->id)) {
            throw new Exception('Error id not found');
        }

        if (empty($experience->title) || empty($experience->description) || empty($experience->date) || empty($experience->lang) || !isset($experience->visible)) {
            throw new Exception('Error experience properties not found or incomplete');
        }

        global $DB;

        $record = new stdClass();
        $record->id = $experience->id;
        $record->title = $experience->title;
        $record->description = $experience->description;
        $record->date = $experience->date;
        $record->lang = $experience->lang;
        $record->visible = $experience->visible;

        return $DB->update_record(self::$table, $record);
    }

    /**
     * Get my experiences
     */

    public static function getMyExperiences($user)
    {
        global $DB;
        $experiences = array_values($DB->get_records(self::$table, ['user' => $user]));
        return $experiences;
    }


    /**
     * Delete an experience
     *
     * @param int $id
     * @return bool
     */
    public static function deleteExperience($id)
    {
        global $DB, $USER;
        if (!self::checkExperience($id)) {
            throw new Exception('Error experience not found');
        }
        // Check permissions
        if (!local_dta_check_permissions($id, $USER)) {
            throw new Exception('Error permissions');
        }
        return $DB->delete_records(self::$table, ['id' => $id]);
    }

    /**
     * check if experience exists
     * 
     * @param int $id
     * @return bool
     */
    public static function checkExperience($id)
    {
        global $DB;
        return $DB->record_exists(self::$table, ['id' => $id]);
    }

    /**
     * Get an experience by this id
     * 
     * @param int $id
     * @return stdClass|null
     */
    public static function getExperience($id)
    {
        global $DB;
        return $DB->get_record(self::$table, ['id' => $id]);
    }
}
