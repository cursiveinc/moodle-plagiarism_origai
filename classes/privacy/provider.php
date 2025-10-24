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

namespace plagiarism_origai\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;


if (interface_exists('\core_privacy\local\request\core_userlist_provider')) {
    interface core_userlist_provider extends \core_privacy\local\request\core_userlist_provider {
    }
} else {
    interface core_userlist_provider {
    };
}

class provider implements
    \core_privacy\local\metadata\provider,
    \core_plagiarism\privacy\plagiarism_provider,
    core_userlist_provider {
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
            'content' => 'privacy:metadata:plagiarism_origai_client:submission_content',
            'coursemodule' => 'privacy:metadata:plagiarism_origai_client:coursemodule',
            'submissiondate' => 'privacy:metadata:plagiarism_origai_client:submissiondate',
            'submissionref' => 'privacy:metadata:plagiarism_origai_client:submissionref',
            'moodleuserid' => 'privacy:metadata:plagiarism_origai_client:moodleuserid',
        ], 'privacy:metadata:plagiarism_origai_client');

        return $collection;
    }

    public static function _get_contexts_for_userid($userid) {

        $params = [
            'modulename' => 'assign',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];

        $sql = "SELECT ctx.id " .
            "FROM {course_modules} cm " .
            "JOIN {modules} m ON cm.module = m.id AND m.name = :modulename " .
            "JOIN {assign} a ON cm.instance = a.id " .
            "JOIN {context} ctx ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel " .
            "JOIN {plagiarism_origai_plagscan} ps ON ps.cmid = cm.id " .
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

        $user = $DB->get_record('user', ['id' => $userid]);
        if (!$user) {
            return;
        }

        $params = ['userid' => $user->id, 'cmid' => $context->instanceid];

        $sql = "SELECT id, userid, cmid, total_text_score, original_score, ai_score, content, update_time " .
            "FROM {plagiarism_origai_plagscan} " .
            "WHERE userid = :userid AND cmid = :cmid";
        $submissions = $DB->get_records_sql($sql, $params);

        $exportdata = ['scans' => []];
        foreach ($submissions as $submission) {
            $exportdata['scans'][] = [
                'content' => $submission->content,
                'total_text_score' => $submission->total_text_score,
                'original_score' => $submission->original_score,
                'ai_score' => $submission->ai_score,
                'update_time' => $submission->update_time ? \core_privacy\local\request\transform::datetime(strtotime($submission->update_time)) : null,
            ];
        }
        $contextdata = helper::get_context_data($context, $user);
        $contextdata = (object) array_merge((array)$contextdata, $exportdata);
        writer::with_context($context)->export_data($subcontext, $contextdata);
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

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $sql = "SELECT ps.userid
                  FROM {plagiarism_origai_plagscan} ps
                 WHERE ps.cmid = :cmid";

        $params = [
            'cmid' => $context->instanceid,
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $userids = $userlist->get_userids();

        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'userid');

        $sql = "SELECT ps.id
                FROM {plagiarism_origai_plagscan} ps
                JOIN {course_modules} c
                    ON ps.cmid = c.id
                WHERE ps.userid $insql
                    AND c.id = :cmid";

        $params = [
            'cmid' => $context->instanceid,
        ];

        $params = array_merge($params, $inparams);

        $scanids = $DB->get_fieldset_sql($sql, $params);

        $DB->delete_records_list('plagiarism_origai_plagscan', 'id', array_values($scanids));
    }
}
