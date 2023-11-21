<?php

namespace plagiarism_origai\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\writer;

class provider implements
    \core_privacy\local\metadata\provider,
    \core_plagiarism\privacy\plagiarism_provider {
    use \core_privacy\local\legacy_polyfill;
    use \core_plagiarism\privacy\legacy_polyfill;

    public static function _get_metadata(collection $collection) {

        $collection->add_database_table(
            'plagiarism_origai_plagscan',
            [
                'userid' => 'privacy:metadata:plagiarism_origai_plagscan:userid',
                'total_text_score' => 'privacy:metadata:plagiarism_origai_plagscan:totaltextscore',
                'flesch_grade_level' => 'privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel',
                'updated_time' => 'privacy:metadata:plagiarism_origai_plagscan:updatedtime',
            ],
            'privacy:metadata:plagiarism_origai_plagscan'
        );

        $collection->add_external_location_link('plagiarism_originalityai_client', [
            'content' => 'privacy:metadata:plagiarism_origai_client:submission_content'
        ], 'privacy:metadata:plagiarism_origai_client');

        return $collection;
    }

    public static function _get_contexts_for_userid($userid) {

        $params = [
            'modulename' => 'assign',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid
        ];

        $sql = "SELECT ctx.id " .
            "FROM {course_modules} cm " .
            "JOIN {modules} m ON cm.module = m.id AND m.name = :modulename " .
            "JOIN {assign} a ON cm.instance = a.id " .
            "JOIN {context} ctx ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel " .
            "JOIN {plagiarism_origai_plagscan} ps ON ps.cm = cm.id " .
            "WHERE ps.userid = :userid";

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    public static function _export_plagiarism_user_data($userid, \context $context, array $subcontext, array $linkarray) {
        global $DB;

        if (empty($userid)) {
            return;
        }

        $user = $DB->get_record('user', array('id' => $userid));

        $params = ['userid' => $user->id];

        $sql = "SELECT id, userid, cmid, total_text_score,flesch_grade_level update_time " .
            "FROM {plagiarism_origai_plagscan} " .
            "WHERE userid = :userid";
        $submissions = $DB->get_records_sql($sql, $params);

        foreach ($submissions as $submission) {
            $context = \context_module::instance($submission->cm);

            $contextdata = helper::get_context_data($context, $user);

            $contextdata = (object)array_merge((array)$contextdata, $submission);
            writer::with_context($context)->export_data([], $contextdata);

            helper::get_context_data($context, $user);
        }
    }

    public static function _delete_plagiarism_for_context(\context $context) {
        global $DB;

        if (empty($context)) {
            return;
        }

        if (!$context instanceof \context_module) {
            return;
        }

        $DB->delete_records('plagiarism_origai_plagscan', ['cmid' => $context->instanceid]);
    }

    public static function _delete_plagiarism_for_user($userid, \context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        $DB->delete_records('plagiarism_origai_plagscan', ['userid' => $userid, 'cmid' => $context->instanceid]);
    }
}
