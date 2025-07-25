<?php
// This file is part of the plagiarism_origai plugin for Moodle
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
 * Event handler for assessment related cm.
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\event\observer;

defined('MOODLE_INTERNAL') || die();

use plagiarism_origai\enums\plagiarism_origai_scan_type_enums;
use plagiarism_origai\enums\plagiarism_origai_status_enums;
use plagiarism_origai\helpers\plagiarism_origai_plugin_config;
use plagiarism_origai\helpers\plagiarism_origai_action;
use plagiarism_origai\helpers\plagiarism_origai_text_extractor;

require_once($CFG->dirroot . '/question/type/essay/questiontype.php');

/**
 * Event handler class for assessment related cm
 */
class assessment_listener {


    /**
     * Handler for assign submission.
     * @param \mod_assign\event\assessable_submitted $event
     */
    public static function assign_submitted($event) {
        global $DB;
        $eventdata = $event->get_data();

        // Get course module.
        $coursemodule = static::get_coursemodule($eventdata, 'assign');

        // Stop event if the course module is not found.
        if (!$coursemodule) {
            return;
        }

        // Check if module is enabled for this event.
        if (!plagiarism_origai_plugin_config::is_module_enabled($coursemodule->modname, $coursemodule->id)) {
            return;
        }

        if (!plagiarism_origai_plugin_config::get_cm_config(
            $coursemodule->id, 'plagiarism_origai_automated_scan', false
        )) {
            return;
        }

        $submission = $event->get_record_snapshot('assign_submission', $event->objectid);

        // Handle uploaded Files.
        $eventdata['other']['pathnamehash'] = [];
        if ($uploadedfiles = $DB->get_records(
            'files',
            [
                'component' => 'assignsubmission_file',
                'itemid' => $submission->id,
                'userid' => $eventdata['userid'],
            ]
        )) {
            foreach ($uploadedfiles as $uploadedfile) {
                $eventdata['other']['pathnamehash'][] = $uploadedfile->pathnamehash;
            }
        }

        // Handle text content.
        $eventdata['other']['content'] = '';
        if ($txtsubmissionref = $DB->get_record(
            'assignsubmission_onlinetext',
            [
                'submission' => $submission->id,
            ],
            'onlinetext'
        )) {
            $eventdata['other']['content'] = $txtsubmissionref->onlinetext;
        }
        $assignment = $DB->get_record('assign', [
            'id' => $submission->assignment,
        ], 'name');
        $title = null;
        if ($assignment) {
            $title = substr($assignment->name, 0, 255);
        }

        try {
            $userid = empty($eventdata['relateduserid']) ?
                $eventdata['userid'] : $eventdata['relateduserid'];
            $scantypes = [
                plagiarism_origai_scan_type_enums::PLAGIARISM,
                plagiarism_origai_scan_type_enums::AI,
            ];

            if (!empty($eventdata['other']['content'])) {
                foreach ($scantypes as $scantype) {
                    $content = $eventdata['other']['content'];
                    plagiarism_origai_action::queue_new_submission([
                        'scan_type' => $scantype,
                        'cmid' => $coursemodule->id,
                        'userid' => $userid,
                        'itemid' => $eventdata['objectid'],
                        'title' => $title ?? substr(html_to_text($content, 0, false), 0, 255),
                        'content' => $content,
                        'contenthash' => plagiarism_origai_action::generate_content_hash($content),
                    ]);
                }
            }
            foreach ($eventdata['other']['pathnamehash'] as $pathnamehash) {
                $textextractor = plagiarism_origai_text_extractor::make(null, $pathnamehash);
                $content = $textextractor->extract();
                if (!$content) {
                    if ($textextractor->get_stored_file()->get_filename() === '.') {
                        continue;
                    }
                    foreach ($scantypes as $scantype) {
                        plagiarism_origai_action::queue_new_submission([
                            'scan_type' => $scantype,
                            'cmid' => $coursemodule->id,
                            'userid' => $userid,
                            'itemid' => $eventdata['objectid'],
                            'title' => $title ?? substr(html_to_text($content, 0, false), 0, 255),
                            'content' => null,
                            'contenthash' => null,
                            'error' => 'Content is invalid or unsupported',
                            'status' => plagiarism_origai_status_enums::SKIPPED,
                        ]);
                    }
                    continue;
                }

                foreach ($scantypes as $scantype) {
                    plagiarism_origai_action::queue_new_submission([
                        'scan_type' => $scantype,
                        'cmid' => $coursemodule->id,
                        'userid' => $userid,
                        'itemid' => $eventdata['objectid'],
                        'title' => $title ?? substr(html_to_text($content, 0, false), 0, 255),
                        'content' => $content,
                        'contenthash' => plagiarism_origai_action::generate_content_hash($content),
                    ]);
                }
            }
        } catch (\Throwable $th) {
            debugging("Error queuing submission, exception message: " . $th->getMessage());
        }
    }

    /**
     * Handler for quiz submission.
     * @param \mod_quiz\event\attempt_submitted $event
     */
    public static function quiz_submitted($event) {
        $eventdata = $event->get_data();
        // Set up and queue adhoc task
        $task = new \plagiarism_origai\task\quiz_submission_scan_task();
        $task->set_component('plagiarism_origai');

        // Delay by 10s to ensure responsesummary is saved before accessing it
        $task->set_next_run_time(time() + 10);

        $task->set_custom_data([
            'eventdata' => $eventdata,
        ]);

        \core\task\manager::queue_adhoc_task($task);
    }

    /**
     * Handler for forum submission.
     * @param \mod_forum\event\assessable_uploaded $event
     */
    public static function forum_submitted($event) {
        $eventdata = $event->get_data();
        // Get course module.
        $coursemodule = static::get_coursemodule($eventdata, 'forum');

        // Stop event if the course module is not found.
        if (!$coursemodule) {
            return;
        }

        // Check if module is enabled for this event.
        if (!plagiarism_origai_plugin_config::is_module_enabled($coursemodule->modname, $coursemodule->id)) {
            return;
        }

        if (!plagiarism_origai_plugin_config::get_cm_config(
            $coursemodule->id, 'plagiarism_origai_automated_scan', false
        )) {
            return;
        }

        $forumpost = $event->get_record_snapshot('forum_posts', $eventdata['objectid']);

        $title = null;
        if ($forumpost) {
            $title = substr($forumpost->subject, 0, 255);
        }

        try {
            $userid = $eventdata['userid'];
            $scantypes = [
                plagiarism_origai_scan_type_enums::PLAGIARISM,
                plagiarism_origai_scan_type_enums::AI,
            ];

            if (!empty($eventdata['other']['content'])) {
                foreach ($scantypes as $scantype) {
                    $content = $eventdata['other']['content'];
                    plagiarism_origai_action::queue_new_submission([
                        'scan_type' => $scantype,
                        'cmid' => $coursemodule->id,
                        'userid' => $userid,
                        'itemid' => $eventdata['objectid'],
                        'title' => $title ?? substr(html_to_text($content, 0, false), 0, 255),
                        'content' => $content,
                        'contenthash' => plagiarism_origai_action::generate_content_hash($content),
                    ]);
                }
            }
            foreach ($eventdata['other']['pathnamehashes'] as $pathnamehash) {
                $textextractor = plagiarism_origai_text_extractor::make(null, $pathnamehash);
                $content = $textextractor->extract();
                if (!$content) {
                    if ($textextractor->get_stored_file()->get_filename() === '.') {
                        continue;
                    }
                    foreach ($scantypes as $scantype) {
                        plagiarism_origai_action::queue_new_submission([
                            'scan_type' => $scantype,
                            'cmid' => $coursemodule->id,
                            'userid' => $userid,
                            'itemid' => $eventdata['objectid'],
                            'title' => $title ?? substr(html_to_text($content, 0, false), 0, 255),
                            'content' => null,
                            'contenthash' => null,
                            'error' => 'Content is invalid or unsupported',
                            'status' => plagiarism_origai_status_enums::SKIPPED,
                        ]);
                    }
                    continue;
                }

                foreach ($scantypes as $scantype) {
                    plagiarism_origai_action::queue_new_submission([
                        'scan_type' => $scantype,
                        'cmid' => $coursemodule->id,
                        'userid' => $userid,
                        'itemid' => $eventdata['objectid'],
                        'title' => $title ?? substr(html_to_text($content, 0, false), 0, 255),
                        'content' => $content,
                        'contenthash' => plagiarism_origai_action::generate_content_hash($content),
                    ]);
                }
            }
        } catch (\Throwable $th) {
            debugging("Error queuing submission, exception message: " . $th->getMessage());
        }
    }


    /**
     * Get course module.
     * @param object $data
     * @return object course module (cm)
     */
    private static function get_coursemodule($data, $modulename) {
        if ($modulename == 'quiz') {
            // During quiz submission, we do have the quiz id.
            return get_coursemodule_from_instance($modulename, $data['other']['quizid']);
        } else {
            return get_coursemodule_from_id($modulename, $data['contextinstanceid']);
        }
    }
}
