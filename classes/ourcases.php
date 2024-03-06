<?php 

/**
 * OurCases class
 *
 * @package   local_dta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dta;

use stdClass;

class OurCases 
{
    private static $table = 'digital_ourcases';
    private static $table_section_text = 'digital_oc_sec_tex';
    private $db;
    private $id;
    private $experience;
    private $date;
    private $status;


    /**
     * OurCases constructor
     */
    public function __construct($ourcase = null)
    {
        global $DB;
        $this->db = $DB;
        if ($ourcase && is_object($ourcase)) {
            $this->id = $ourcase->id;
            $this->experience = $ourcase->experience;
            $this->date = $ourcase->date;
            $this->status = $ourcase->status;
        }

    }

    /**
     * Get all cases
     *
     * @return array Returns an array of records
     */
    public static function getCases()
    {
        global $DB;
        return $DB->get_records(self::$table);
    }

    /**
     * Get a specific case
     *
     * @param int $id ID of the case
     * @return object Returns a record object
     */
    public static function getCase($id)
    {
        global $DB;
        return $DB->get_record(self::$table, ['id' => $id]);
    }

    /**
     * Get a specific case by experience
     *
     * @param int $id ID of the case
     * @return object Returns a record object
     */
    public static function getCaseByExperience($experience)
    {
        global $DB;
        return $DB->get_record(self::$table, ['experience' => $experience]);
    }


    
    /**
     * Add a case
     *
     * @param int $experienceid ID of the experience
     * @param string $date Date of the case
     * @param bool $status Status of the case
     * @return bool|int Returns ID of the inserted record if successful, false otherwise
     */
    public static function addCase($experienceid, $date, $user , $status = 0)
    {
        global $DB;
        if (empty($experienceid) || empty($date) || empty($user)) {
            return false;
        }

        // verify if the experience exists
        if(!Experience::getExperience($experienceid)){
            return false;
        }

        $record = new stdClass();
        $record->experience = $experienceid;
        $record->user = $user;
        $record->date = $date;
        $record->status = $status;

        if(!$id = $DB->insert_record(self::$table,  $record)){
            throw new Exception('Error adding experience');
        }

        $record->id = $id;
                
        return new OurCases($record);

    }   

    /**
     * Update a case
     *
     * @param int $id ID of the case
     * @param string $title Title of the case
     * @param string $description Description of the case
     * @param string $date Date of the case
     * @param string $lang Language of the case
     * @param bool $visible Visibility of the case
     * @return bool Returns true if successful, false otherwise
     */
    public static function updateCase($experienceid, $date, $lang, $visible)
    {
        global $DB;
        if (empty($experienceid) ||empty($date) || empty($lang) || empty($visible) ) {
            return false;
        }

        $record = new stdClass();
        $record->id = $id;
        $record->title = $title;
        $record->description = $description;
        $record->date = $date;
        $record->lang = $lang;
        $record->visible = $visible;

        return $DB->update_record(self::$table, $record);
    }

    /**
     * Delete a case
     *
     * @param int $id ID of the case
     * @return bool Returns true if successful, false otherwise
     */
    public static function deleteCase($id)
    {
        global $DB;
        return $DB->delete_records(self::$table, ['id' => $id]);
    }

    /**
     * Get the text of a section
     *
     * @param int $id ID of the section
     * @return object Returns a record object
     */
    public static function getSectionText($id)
    {
        global $DB;
        return $DB->get_record(self::$table_section_text, ['id' => $id]);
    }
    
}