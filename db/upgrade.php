<?php

function xmldb_plagiarism_origai_upgrade($oldversion)
{
    global $CFG, $DB;

    $result = TRUE;
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

    return $result;
}
