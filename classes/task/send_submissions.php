<?php
// This file is part of the plagiarism_origai plugin for Moodle - http://moodle.org/
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
 * Task schedule configuration for the plagiarism_origai plugin.
 *  DOC -  https://moodledev.io/docs/5.1/apis/subsystems/task
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\task;

defined('MOODLE_INTERNAL') || die();

use plagiarism_origai\helpers\plagiarism_origai_action;
use plagiarism_origai\helpers\plagiarism_origai_plugin_config;
use plagiarism_origai\helpers\plagiarism_origai_api;
use plagiarism_origai\enums\plagiarism_origai_scan_type_enums;
use plagiarism_origai\enums\plagiarism_origai_status_enums;

/**
 * Class send_submissions
 * @package plagiarism_origai\task
 */
class send_submissions extends \core\task\scheduled_task {


    /** @var string */
    const CRON_MAX_LOOP = 2;

    /** @var string */
    const CHUNK_SIZE = 10;

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('sendqueuedsubmissionstaskname', 'plagiarism_origai');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        $this->send_submissions();
    }

    /**
     * Sends submissions to originality.ai
     * @throws \coding_exception
     * @return void
     */
    private function send_submissions() {
        $loopcount = 0;
        $originalityapi = new plagiarism_origai_api();
        if (!$originalityapi->test_connection()) {
            return;
        }
        while ($loopcount < static::CRON_MAX_LOOP) {
            $queuedsubmissions = plagiarism_origai_action::get_queued_submissions(static::CHUNK_SIZE);
            if (empty($queuedsubmissions)) {
                break;
            }
            $batch = $this->prepare_batch($queuedsubmissions);

            $response = $originalityapi->batch_scan($batch);
            if (
                $response === false || 
                (isset($response->success) && !$response->success) ||
                !isset($response->success)
            ) {

                $errormessage = isset($response->message) ?
                    $response->message : get_string('defaultscanerror', 'plagiarism_origai');

                foreach ($queuedsubmissions as $queuedsubmission) {
                    $queuedsubmission->success = 0;
                    $queuedsubmission->status = plagiarism_origai_status_enums::FAILED;
                    $queuedsubmission->error = $errormessage;
                    plagiarism_origai_action::update_scan_record($queuedsubmission);
                }
                continue;
            }

            $queuedsubmissionindex = array_values($queuedsubmissions);
            $responsedata = array_map(function ($item, $index) use ($queuedsubmissionindex) {
                $item->id = $queuedsubmissionindex[$index]->id;
                return $item;
            }, $response->data, array_keys($response->data));

            plagiarism_origai_action::handle_batch_results($responsedata);

            $loopcount++;
        }
    }

    /**
     * Prepares the batch for the api call
     * @param $records
     * @return array
     */
    private function prepare_batch($records) {
        $batch = [];
        $cmids = [];
        $scanrecordids = [];
        foreach ($records as $record) {
            $cmids[] = $record->cmid;
            $scanrecordids[] = $record->id;
        }
        $cmsettings = plagiarism_origai_plugin_config::get_cms_config($cmids, ['plagiarism_origai_ai_model']);

        if (!plagiarism_origai_action::mark_scan_as_processing($scanrecordids)) {
            return $batch;
        }
        foreach ($records as $record) {
            $scanmeta = isset($record->meta)? json_decode($record->meta, true) : [];
            $payload = [
                'title' => $record->title,
                'ai_model' => $cmsettings[$record->cmid]['plagiarism_origai_ai_model'] ?? plagiarism_origai_plugin_config::get_default_model(),
                'content' => $record->content,
                'scan_ai' => $record->scan_type == plagiarism_origai_scan_type_enums::AI,
                'scan_plag' => $record->scan_type == plagiarism_origai_scan_type_enums::PLAGIARISM,
                'meta' => [
                    'activity_name' => $scanmeta['activity_name'] ?? null,
                    "activity_type" => $scanmeta['activity_type'] ?? null,
                    'author_id' => isset($scanmeta['author_id']) ? (string)$scanmeta['author_id'] : null,
                    "course_module" => $scanmeta['course_module'] ?? null,
                    "submission_date" => $scanmeta['submission_date'] ?? null,
                    "submission_ref" => $scanmeta['submission_ref'] ?? null,
                    "submission_title" => $record->title
                ]
            ];
            $batch[] = $payload;
        }
        return $batch;
    }
}
