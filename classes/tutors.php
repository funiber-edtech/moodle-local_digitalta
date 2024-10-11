<?php

/**
 * Tutors class
 *
 * @package   local_digitalta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_digitalta;

require_once($CFG->dirroot . '/local/digitalta/classes/chat.php');
require_once($CFG->dirroot . '/local/digitalta/classes/experiences.php');
require_once($CFG->dirroot . '/local/digitalta/classes/tutoring_status.php');
require_once($CFG->dirroot . '/user/profile/lib.php');

use local_digitalta\Chat;
use local_digitalta\Experiences;
use local_digitalta\TutoringStatus;
use local_digitalta\task\pending_emails;
use stdClass;

class Tutors
{
    private $id;
    private static $table = 'user';
    private static $requests_table = 'digitalta_tutoring_request';
    private static $availability_table = 'digitalta_tutor_availability';

    /**
     * Get a tutor by its ID.
     *
     * @param $id int The ID of the tutor.
     *
     * @return object The tutor object.
     */
    public static function get_tutor(int $id): object
    {
        global $DB;
        return $DB->get_record(self::$table, ['id' => $id]);
    }

    /**
     * Get a tutor by its ID.
     *
     * @param $id int The ID of the tutor.
     */
    public static function get_request(int $id): object | bool
    {
        global $DB;
        return $DB->get_record(self::$requests_table, ['id' => $id]);
    }

    /**
     * Get all tutors.
     *
     * @return array The tutors.
     */
    public static function get_all_tutors(): array
    {
        global $DB;
        $tutors = $DB->get_records_sql(
            "SELECT mu.*
            FROM {user} mu
            INNER JOIN {user_info_data} muid
                ON mu.id = muid.userid
                AND muid.data IN ('Tutor', 'Mentor')
            INNER JOIN {user_info_field} muif
                ON muid.fieldid = muif.id
                AND muif.shortname = 'digitalta_role';"
        );
        return array_values(array_map(function ($tutor) {
            profile_load_data($tutor);
            return $tutor;
        }, $tutors));
    }
    /**
     * Send a tutor request.
     *
     * @param $tutorid int The ID of the tutor.
     * @param $userid int The ID of the user.
     * @param $experienceid int The ID of the experience.
     *
     * @return bool|object The tutor request object.
     */
    public static function send_tutor_request(int $tutorid, int $experienceid, bool $experienceRequest): bool|object
    {
        global $DB, $USER, $PAGE, $CFG;

        $context = \context_system::instance();
        $PAGE->set_context($context);

        $data = new \stdClass();
        $data->tutorid = $tutorid;
        $data->experienceid = $experienceid;
        $data->status = $experienceRequest ? TutoringStatus::PENDING_EXPERIENCE_REQUEST : TutoringStatus::PENDING_TUTOR_REQUEST;
        $data->timecreated = time();
        $data->timemodefied = time();
        if (!$data->id = $DB->insert_record(self::$requests_table, $data)) {
            return false;
        }

        $tutor = $DB->get_record('user', array('id' => $tutorid), '*', MUST_EXIST);
        $experience= Experiences::get_experience($experienceid, false);


        if ($experienceRequest && $experience->userid) {
            $subject = get_string('tutoring:experiencerequestsubject', 'local_digitalta');
            $messagehtml = get_string('tutoring:experiencerequestbody', 'local_digitalta', (object)[
                'experienceid' => $experienceid,
                'experienceurl' => $CFG->wwwroot . '/local/digitalta/pages/experiences/view.php?id=' . $experienceid
            ]);

            $messagehtml .= '<br>' . get_string('tutoring:experiencerequestsender', 'local_digitalta', (object)[
                'username' =>  $tutor->firstname . ' ' . $tutor->lastname . ' ' . '(' . $tutor->username . ')'
            ]);

            $messagehtml .= '<br>' . get_string('tutoring:tutorrequesttime', 'local_digitalta', (object)[
                'requesttime' => userdate(time())
            ]);

            $task = pending_emails::instance($experience->userid, $tutorid, $subject, $messagehtml);
        } else {
            $subject = get_string('tutoring:newtutorrequestsubject', 'local_digitalta');

            $messagehtml = get_string('tutoring:tutorrequestbody', 'local_digitalta', (object)[
                'experienceid' => $experienceid,
                'experienceurl' => $CFG->wwwroot . '/local/digitalta/pages/experiences/view.php?id=' . $experienceid
            ]);
            $messagehtml .= '<br>' . get_string('tutoring:tutorrequestrsender', 'local_digitalta', (object)[
                'username' =>  $USER->firstname . ' ' . $USER->lastname . ' ' . '(' . $USER->username . ')'
            ]);

            $messagehtml .= '<br>' . get_string('tutoring:tutorrequesttime', 'local_digitalta', (object)[
                'requesttime' => userdate(time())
            ]);

            $task = pending_emails::instance($tutorid, $USER->id, $subject, $messagehtml);
        }

        \core\task\manager::queue_adhoc_task($task);

        return $data;
    }

    /**
     * Get tutor requests by tutor ID.
     *
     * @param $tutorid int The ID of the tutor.
     * @param $status int The status of the tutor request.
     *
     * @return array The tutor requests.
     */
    public static function requests_get_by_tutor(int $tutorid, $status = 0): array
    {
        global $DB;
        return array_values($DB->get_records(self::$requests_table, ['tutorid' => $tutorid, 'status' => $status]));
    }

    /**
     * Get tutor requests by experience ID.
     *
     * @param $experienceid int The ID of the experience.
     *
     * @return array The tutor requests.
     */
    public static function requests_get_by_experience(int $experienceid, int $status = 1): array
    {
        global $DB;
        return array_values($DB->get_records(self::$requests_table, ['experienceid' => $experienceid, 'status' => $status]));
    }

    public static function request_get_by_tutor_experience(int $tutorid, int $experienceid, int $status = 2): stdClass | bool
    {
        global $DB;
        return $DB->get_record(self::$requests_table, ['tutorid' => $tutorid, 'experienceid' => $experienceid, 'status' => $status]);
    }

    /**
     * Change the status of a tutor request.
     *
     * @param $id int The ID of the tutor request.
     * @param $status int The status of the tutor request.
     *
     * @return bool True if the status was changed, false otherwise.
     */
    public static function change_tutor_request_status(int $id, int $status): bool
    {
        global $DB;
        $data = new \stdClass();
        $data->id = $id;
        $data->status = $status;
        return $DB->update_record(self::$requests_table, $data);
    }

    /**
     * Accept a tutor request.
     *
     * @param $requestid int The ID of the tutor request.
     */
    public static function requests_accept(int $requestid): bool
    {
        $request = self::get_request($requestid);
        if (!$request) {
            return false;
        }

        $chat = Chat::get_chat_room_by_experience($request->experienceid);

        if (!$chat) {
            $chat = new \stdClass();
            $chat->id = Chat::create_chat_room($request->experienceid);
        }
        $experience = Experiences::get_experience($request->experienceid);

        Chat::chats_add_user_to_room($chat->id, $experience->userid);
        Chat::chats_add_user_to_room($chat->id, $request->tutorid);

        return self::change_tutor_request_status($requestid, TutoringStatus::ACCEPTED);
    }

    /**
     * Reject a tutor request.
     *
     * @param $requestid int The ID of the tutor request.
     */
    public static function reject_tutor_request(int $requestid): bool
    {
        return self::requests_remove_by_id($requestid);
    }

    /**
     * Verify if a tutor request exists.
     *
     * @param $tutorid int The ID of the tutor.
     * @param $experienceid int The ID of the experience.
     * @param $status int The status of the tutor request.
     */
    public static function is_enrolled_tutor_in_course(int $tutorid, int $experienceid, int $status = 1): bool
    {
        global $DB;
        $data = $DB->get_record(self::$requests_table, ['tutorid' => $tutorid, 'experienceid' => $experienceid, 'status' => $status]);
        return !!$data;
    }

    /**
     * Remove a tutor request.
     *
     * @param $tutorid int The ID of the tutor.
     * @param $experienceid int The ID of the experience.
     */
    public static function requests_remove(int $tutorid, int $experienceid): bool
    {
        global $DB;
        return $DB->delete_records(self::$requests_table, ['tutorid' => $tutorid, 'experienceid' => $experienceid]);
    }

    /**
     * Remove a tutor request.
     *
     * @param $requestid int The ID of the tutor request.
     */
    public static function requests_remove_by_id(int $requestid): bool
    {
        global $DB;
        return $DB->delete_records(self::$requests_table, ['id' => $requestid]);
    }

    public static function availability_create($userid, $day, $timefrom, $timeto)
    {
        global $DB;
        $tutor_availability = new \stdClass();
        $tutor_availability->userid = $userid;
        $tutor_availability->day = $day;
        $tutor_availability->timefrom = $timefrom;
        $tutor_availability->timeto = $timeto;
        $tutor_availability->timecreated = time();
        $tutor_availability->timemodified = time();

        if ($tutor_availability->timefrom > $tutor_availability->timeto) {
            throw new \Exception('The timefrom must be less than timeto');
        }

        $sql = "SELECT * FROM {digitalta_tutor_availability} WHERE userid = ? AND day = ? AND ((timefrom <= ? AND timeto >= ?) OR (timefrom <= ? AND timeto >= ?))";
        $tutor_availability_exist = $DB->get_records_sql($sql, [$userid, $day, $timefrom, $timefrom, $timeto, $timeto]);
        if ($tutor_availability_exist) {
            throw new \Exception('The tutor already has a availability in the same day and hours');
        };
        $tutor_availability->id = $DB->insert_record('digitalta_tutor_availability', $tutor_availability);
        return $tutor_availability;
    }

    public static function availability_update($id, $userid, $day, $timefrom, $timeto)
    {
        global $DB;
        $tutor_availability = new \stdClass();
        $tutor_availability->id = $id;
        $tutor_availability->userid = $userid;
        $tutor_availability->day = $day;
        $tutor_availability->timefrom = $timefrom;
        $tutor_availability->timeto = $timeto;

        if ($tutor_availability->timefrom > $tutor_availability->timeto) {
            throw new \Exception('The timefrom must be less than timeto');
        }

        $sql = "SELECT * FROM {digitalta_tutor_availability} WHERE id != ? AND iserid = ? AND day = ? AND ((timefrom <= ? AND timeto >= ?) OR (timefrom <= ? AND timeto >= ?))";
        $tutor_availability_exist = $DB->get_records_sql($sql, [$id, $userid, $day, $timefrom, $timefrom, $timeto, $timeto]);
        if ($tutor_availability_exist) {
            throw new \Exception('The tutor already has a availability in the same day and hours');
        };
        $DB->update_record('digitalta_tutor_availability', $tutor_availability);
        return $tutor_availability;
    }


    public static function availability_get_hours_by_day($tutor_id, $day = "ALL")
    {
        global $DB;
        $sql = "SELECT * FROM {digitalta_tutor_availability} WHERE tutor_id = ?";
        $tutor_availability = $DB->get_records_sql($sql, [$tutor_id]);

        $hours = [];
        foreach ($tutor_availability as $availability) {
            if ($day != "ALL" && $availability->day != $day) {
                continue;
            };
            $hours[$availability->day] = [
                "timefrom" => $availability->timefrom,
                "timeto" => $availability->timeto
            ];
        }
        return $hours;
    }

    public static function availability_delete($id)
    {
        global $DB;
        $DB->delete_records('digitalta_tutor_availability', ['id' => $id]);
        return true;
    }

    public static function is_tutor($userid)
    {
        global $DB;
        $context = \context_system::instance();
        $tutor_role_id = $DB->get_field('role', 'id', ['shortname' => 'digitaltatutor']);
        return $tutor_role_id && user_has_role_assignment($userid, $tutor_role_id, $context->id);
    }
}
