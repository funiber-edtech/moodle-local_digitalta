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
 *  Upgrade steps for the local_dta plugin.
 *
 * @package    local_dta
 * @copyright  2024 ADSDR-FUNIBER Scepter Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/dta/locallib.php');

/**
 * Upgrade the local_dta plugin.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool
 */
function xmldb_local_dta_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024050700) {
        throw new Exception('The version is too old. Continuing the upgrade process is not possible. Please, reinstall the plugin. Keep in mind that you will lose all the data.');
    }

    if ($oldversion < 2024050701) {

        // Define field description to be added to digital_cases.
        $table = new xmldb_table('digital_cases');
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'title');

        // Conditionally launch add field description.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dta savepoint reached.
        upgrade_plugin_savepoint(true, 2024050701, 'local', 'dta');
    }

    // Try all insertions regardless of the version
    // Insert the components
    $table = new xmldb_table('digital_components');
    if ($dbman->table_exists($table)) {
        foreach (LOCAL_DTA_COMPONENTS as $value) {
            if ($DB->record_exists('digital_components', ['name' => $value])) {
                continue;
            }
            $component = new stdClass();
            $component->name = $value;
            $component->timecreated = time();
            $component->timemodified = time();
            $DB->insert_record('digital_components', $component);
        }
    }
    $local_dta_components = $DB->get_records('digital_components');
    $local_dta_components = array_column($local_dta_components, 'id', 'name');

    // Insert the modifiers
    $table = new xmldb_table('digital_modifiers');
    if ($dbman->table_exists($table)) {
        foreach (LOCAL_DTA_MODIFIERS as $value) {
            if ($DB->record_exists('digital_modifiers', ['name' => $value])) {
                continue;
            }
            $modifier = new stdClass();
            $modifier->name = $value;
            $modifier->timecreated = time();
            $modifier->timemodified = time();
            $DB->insert_record('digital_modifiers', $modifier);
        }
    }

    // Insert the themes
    $table = new xmldb_table('digital_themes');
    if ($dbman->table_exists($table)) {
        foreach (LOCAL_DTA_THEMES as $value) {
            if ($DB->record_exists('digital_themes', ['name' => $value])) {
                continue;
            }
            $theme = new stdClass();
            $theme->name = $value;
            $theme->timecreated = time();
            $theme->timemodified = time();
            $DB->insert_record('digital_themes', $theme);
        }
    }

    // Insert the resource types
    $table = new xmldb_table('digital_resources_types');
    if ($dbman->table_exists($table)) {
        foreach (LOCAL_DTA_RESOURCE_TYPES as $value) {
            if ($DB->record_exists('digital_resources_types', ['name' => $value])) {
                continue;
            }
            $resource_type = new stdClass();
            $resource_type->name = $value;
            $resource_type->timecreated = time();
            $resource_type->timemodified = time();
            $DB->insert_record('digital_resources_types', $resource_type);
        }
    }

    // Insert the resource formats
    $table = new xmldb_table('digital_resources_formats');
    if ($dbman->table_exists($table)) {
        foreach (LOCAL_DTA_RESOURCE_FORMATS as $value) {
            if ($DB->record_exists('digital_resources_formats', ['name' => $value])) {
                continue;
            }
            $resource_format = new stdClass();
            $resource_format->name = $value;
            $resource_format->timecreated = time();
            $resource_format->timemodified = time();
            $DB->insert_record('digital_resources_formats', $resource_format);
        }
    }

    if ($oldversion < 2024050702) {

        // Define table digital_chat to be created.
        $table = new xmldb_table('digital_chat');

        // Adding fields to table digital_chat.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('experienceid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table digital_chat.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('experienceid', XMLDB_KEY_FOREIGN, ['experienceid'], 'digital_experiences', ['id']);

        // Create the table if it doesn't exist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table digital_chat_messages to be created.
        $table = new xmldb_table('digital_chat_messages');

        // Adding fields to table digital_chat_messages.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('chatid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table digital_chat_messages.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('chatid', XMLDB_KEY_FOREIGN, ['chatid'], 'digital_chat', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Create the table if it doesn't exist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table digital_chat_users to be created.
        $table = new xmldb_table('digital_chat_users');

        // Adding fields to table digital_chat_users.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('chatid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table digital_chat_users.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('chatid', XMLDB_KEY_FOREIGN, ['chatid'], 'digital_chat', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Create the table if it doesn't exist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Set the new plugin version.
        upgrade_plugin_savepoint(true, 2024050702, 'local', 'dta');
    }

    return true;
}
