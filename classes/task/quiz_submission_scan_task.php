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

namespace plagiarism_origai\task;

defined('MOODLE_INTERNAL') || die();

use plagiarism_origai\enums\plagiarism_origai_scan_type_enums;
use plagiarism_origai\enums\plagiarism_origai_status_enums;
use plagiarism_origai\helpers\plagiarism_origai_plugin_config;
use plagiarism_origai\helpers\plagiarism_origai_action;
use plagiarism_origai\helpers\plagiarism_origai_text_extractor;
use mod_quiz\quiz_attempt;


class quiz_submission_scan_task extends \core\task\adhoc_task {

    public function execute() {
        try {
            global $DB;

            $data = (object) $this->get_custom_data();

            // Load the event data from the task.
            $eventdata = (array) json_decode(json_encode($data->eventdata), true);

            $quizattempt = $DB->get_record('quiz_attempts', ['id' => $eventdata['objectid']]);
            if (!$quizattempt) {
                mtrace("Attempt not found.");
                return;
            }

            // Get course module.
            $coursemodule = get_coursemodule_from_instance('quiz', $eventdata['other']['quizid']);
            $course = $DB->get_record('course', ['id' => $coursemodule->course], 'shortname');

            // Stop event if the course module is not fonund.
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

            $quizattempt = $DB->get_record('quiz_attempts', ['id' => $eventdata['objectid']]);

            $quiz = $DB->get_record('quiz', [
                'id' => $quizattempt->quiz,
            ], 'name');

            $attempt = quiz_attempt::create($eventdata['objectid']);

            foreach ($attempt->get_slots() as $key => $slot) {
                $questionattempt = $attempt->get_question_attempt($slot);
                $qtype = $questionattempt->get_question()->qtype;
                if ($qtype instanceof \qtype_essay) {
                    $attachments = $questionattempt->get_last_qt_files('attachments', $eventdata['contextid']);
                    $content = $questionattempt->get_response_summary();

                    try {
                        $userid = $eventdata['userid'];
                        $scantypes = [
                            plagiarism_origai_scan_type_enums::PLAGIARISM,
                            plagiarism_origai_scan_type_enums::AI,
                        ];

                        $submissiondate = plagiarism_origai_action::format_submission_timestamp(
                            $attempt->get_submitted_date() != 0 ? $attempt->get_submitted_date() :
                            $attempt->get_attempt()->timemodified
                        );

                        if (!empty($content)) {
                            $scanmeta = plagiarism_origai_action::construct_scan_meta(
                                $coursemodule->name,
                                get_string('quiz', 'plagiarism_origai'),
                                $userid,
                                $course->shortname,
                                $submissiondate,
                                \core\uuid::generate()
                            );
                            $title = plagiarism_origai_action::generate_scan_title(
                                $course->shortname,
                                $content,
                                $quiz->name
                            );
                            foreach ($scantypes as $scantype) {
                                plagiarism_origai_action::queue_new_submission([
                                    'scan_type' => $scantype,
                                    'cmid' => $coursemodule->id,
                                    'userid' => $userid,
                                    'itemid' => $eventdata['objectid'],
                                    'title' => $title,
                                    'content' => $content,
                                    'contenthash' => plagiarism_origai_action::generate_content_hash($content),
                                    'meta' => $scanmeta,
                                ]);
                            }
                        }
                        foreach ($attachments as $pathnamehash) {
                            $scanmeta = plagiarism_origai_action::construct_scan_meta(
                                $coursemodule->name,
                                get_string('quiz', 'plagiarism_origai'),
                                $userid,
                                $course->shortname,
                                $submissiondate,
                                \core\uuid::generate()
                            );
                            $textextractor = plagiarism_origai_text_extractor::make(null, $pathnamehash);
                            $content = $textextractor->extract();
                            $title = plagiarism_origai_action::generate_scan_title(
                                $course->shortname,
                                $content,
                                $quiz->name
                            );
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
                                        'title' => $title,
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
                                    'title' => $title,
                                    'content' => $content,
                                    'contenthash' => plagiarism_origai_action::generate_content_hash($content),
                                    'meta' => $scanmeta,
                                ]);
                            }
                        }
                    } catch (\Throwable $th) {
                        mtrace("Error queuing submission, exception message: " . $th->getMessage());
                    }
                }
            }

        } catch (\Throwable $th) {
            mtrace("Error queuing submission, exception message: " . $th->getMessage());
        }
    }
}
