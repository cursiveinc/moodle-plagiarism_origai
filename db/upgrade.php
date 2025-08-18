<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Upgrade for plagiarism_origai
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade code for the plagiarism_origai module.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool
 */
function xmldb_plagiarism_origai_upgrade($oldversion) {
    global $CFG, $DB;

    $result = true;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2024052400) {

        $table = new xmldb_table('plagiarism_origai_plagscan');
        $field = new xmldb_field('scan_type', XMLDB_TYPE_CHAR, '10', null, null, null, "plagiarism", 'id');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field original_score to be added to plagiarism_origai_plagscan.
        $table = new xmldb_table('plagiarism_origai_plagscan');
        $field = new xmldb_field('original_score', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'flesch_grade_level');

        // Conditionally launch add field original_score.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field ai_score to be added to plagiarism_origai_plagscan.
        $table = new xmldb_table('plagiarism_origai_plagscan');
        $field = new xmldb_field('ai_score', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'original_score');

        // Conditionally launch add field ai_score.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

         // Define field fakescore to be added to plagiarism_origai_match.
         $table = new xmldb_table('plagiarism_origai_match');
         $field = new xmldb_field('fakescore', XMLDB_TYPE_NUMBER, '12, 11', null, null, null, null, 'score');

         // Conditionally launch add field fakescore.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

          // Define field realscore to be added to plagiarism_origai_match.
        $table = new xmldb_table('plagiarism_origai_match');
        $field = new xmldb_field('realscore', XMLDB_TYPE_NUMBER, '12, 11', null, null, null, null, 'fakescore');

        // Conditionally launch add field realscore.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Origai savepoint reached.
        upgrade_plugin_savepoint(true, 2024052400, 'plagiarism', 'origai');
    }

    if ($oldversion < 2025071800) {
        // Define field status to be added to plagiarism_origai_plagscan.
        $table = new xmldb_table('plagiarism_origai_plagscan');
        $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '191', null, null, null, null, 'update_time');

        // Conditionally launch add field status.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field contenthash to be added to plagiarism_origai_plagscan.
        $table = new xmldb_table('plagiarism_origai_plagscan');
        $field = new xmldb_field('contenthash', XMLDB_TYPE_CHAR, '191', null, null, null, null, 'status');

        // Conditionally launch add field contenthash.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index status_index (not unique) to be added to plagiarism_origai_plagscan.
        $table = new xmldb_table('plagiarism_origai_plagscan');
        $index = new xmldb_index('status_index', XMLDB_INDEX_NOTUNIQUE, ['status']);

        // Conditionally launch add index status_index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define index contenthash_index (not unique) to be added to plagiarism_origai_plagscan.
        $table = new xmldb_table('plagiarism_origai_plagscan');
        $index = new xmldb_index('contenthash_index', XMLDB_INDEX_NOTUNIQUE, ['contenthash']);

        // Conditionally launch add index contenthash_index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        \core\task\manager::queue_adhoc_task(new \plagiarism_origai\task\populate_contenthash());

        // Define index cm_index (not unique) to be added to plagiarism_origai_config.
        $table = new xmldb_table('plagiarism_origai_config');
        $index = new xmldb_index('cm_index', XMLDB_INDEX_NOTUNIQUE, ['cm']);

        // Conditionally launch add index cm_index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Origai savepoint reached.
        upgrade_plugin_savepoint(true, 2025071800, 'plagiarism', 'origai');
    }

    return $result;
}
