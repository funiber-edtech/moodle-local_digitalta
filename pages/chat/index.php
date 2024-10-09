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
 * Chat index page
 *
 * @package   local_digitalta
 * @copyright 2024 ADSDR-FUNIBER Scepter Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');

require_login();

$chatid = optional_param('chatid', null, PARAM_INT);

global $PAGE, $OUTPUT;


$PAGE->set_url(new moodle_url('/local/digitalta/pages/chat/index.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('tutoring:chat_title', 'local_digitalta'));
$PAGE->requires->js_call_amd('local_digitalta/tutoring/google-meet', 'init');
$PAGE->requires->js_call_amd('local_digitalta/commun/main', 'init');

echo $OUTPUT->header();


echo $OUTPUT->render_from_template('local_digitalta/chat/index', ['chatid'=>$chatid]);

echo $OUTPUT->footer();

