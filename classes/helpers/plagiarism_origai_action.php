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
 * Utility class for plagiarism_origai plugin
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\helpers;

use plagiarism_origai\enums\plagiarism_origai_scan_type_enums;
use plagiarism_origai\enums\plagiarism_origai_status_enums;

/**
 * Class plagiarism_origai_action
 * @package plagiarism_origai\helpers
 */
class plagiarism_origai_action {

    /**
     * Queue new submission
     * @param array $submission
     * @param object $record
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function queue_new_submission($submission) {
        $record = static::get_scan_record($submission['cmid'], $submission['userid'], $submission['content'], $submission['scan_type']);
        if (
            $record &&
            (
                $record->status == plagiarism_origai_status_enums::FAILED ||
                $record->status == plagiarism_origai_status_enums::PENDING
            )
        ) {
            $record->status = plagiarism_origai_status_enums::SCHEDULED;
            $record->error = null;
            $record->public_link = null;
            $record->total_text_score = null;
            $record->flesch_grade_level = null;
            $record->original_score = null;
            $record->ai_score = null;
            static::update_scan_record($record);
        }

        // prevent rescheduling if scan already exists and past pending state
        if (($record && in_array($record->status, [
            plagiarism_origai_status_enums::COMPLETED,
            plagiarism_origai_status_enums::PROCESSING,
            plagiarism_origai_status_enums::SCHEDULED,
        ])) || ($record && $record->success)) {
            return $record;
        }

        $submission['status'] = $submission['status'] ?? plagiarism_origai_status_enums::SCHEDULED;
        $record = static::create_scan_record($submission);
        return $record;
    }

    private function queue_rescan_submission($submission) {
        $record = static::create_scan_record($submission);
        return $record;
    }

    /**
     * Create scan record
     * @param \stdClass $submission
     * @return \stdClass
     */
    public static function create_scan_record($submission) {
        global $DB;
        $table = "plagiarism_origai_plagscan";
        $record = new \stdClass;
        $record->scan_type = $submission['scan_type'];
        $record->cmid = $submission['cmid'];
        $record->userid = $submission['userid'];
        $record->itemid = $submission['itemid'] ?? null;
        $record->public_link = $submission['public_link'] ?? null;
        $record->title = $submission['title'] ?? null;
        $record->total_text_score = $submission['total_text_score'] ?? null;
        $record->flesch_grade_level = $submission['flesch_grade_level'] ?? null;
        $record->original_score = $submission['original_score'] ?? null;
        $record->ai_score = $submission['ai_score'] ?? null;
        $record->sources = $submission['sourcres'] ?? null;
        $record->content = $submission['content'];
        $record->error = $submission['error'] ?? null;
        $record->status = $submission['status'];
        $record->success = $submission['success'] ?? null;
        $record->update_time = null;
        $record->contenthash = $submission['contenthash'] ?? self::generate_content_hash($record->content);

        $id = $DB->insert_record($table, $record);
        $record->id = $id;
        return $record;
    }

    /**
     * Update scan record.
     * @param $record
     * @return mixed
     * @throws \dml_exception
     */
    public static function update_scan_record($record) {
        global $DB;
        $table = "plagiarism_origai_plagscan";
        $DB->update_record($table, $record);
        return $record;
    }

    /**
     * Mark scan as processing.
     * @param $scanids
     * @return bool
     */
    public static function mark_scan_as_processing($scanids) {
        global $DB;

        $table = 'plagiarism_origai_plagscan';
        $status = plagiarism_origai_status_enums::PROCESSING;

        list($idsql, $idparams) = $DB->get_in_or_equal($scanids, SQL_PARAMS_NAMED, 'id');

        $sql = "UPDATE {{$table}} SET status = :status WHERE id $idsql";

        $params = array_merge(['status' => $status], $idparams);

        return $DB->execute($sql, $params);
    }

    /**
     * Get scan record by content, cmid, userid, itemid, scantype.
     * @param int $cmid
     * @param int $userid
     * @param string $content
     * @param $itemid
     * @param $scantype
     * @return mixed
     */
    public static function get_scan_record($cmid, $userid, $content, $scantype) {
        global $DB;
        $contenthash = static::generate_content_hash($content);

        $sql = "SELECT * FROM {plagiarism_origai_plagscan} 
                    WHERE cmid = ? 
                    AND userid = ? 
                    AND scan_type = ?  
                    AND " . ($contenthash === null ? "contenthash IS NULL" : "contenthash = ?") . " 
                    ORDER BY id DESC LIMIT 1";

        $params = [$cmid, $userid, $scantype];

        // Add contenthash if not null
        if ($contenthash !== null) {
            $params[] = $contenthash;
        }

        return $DB->get_record_sql($sql, $params);
    }

    /**
     * Get scan record by id.
     * @param int $id
     * @return mixed
     */
    public static function get_scan_record_by_id($id) {
        global $DB;
        $scantable = "plagiarism_origai_plagscan";
        return $DB->get_record($scantable, ['id' => $id]);
    }

    public static function delete_scans_by_cmid($cmid) {
        global $DB;
        $scantable = "plagiarism_origai_plagscan";
        $DB->delete_records($scantable, ['cmid' => $cmid]);
    }

    /**
     * Generate content hash.
     * @param $content
     * @return ?string
     */
    public static function generate_content_hash($content) {
        $content = static::normalize_text_for_content_hash($content);
        return !empty($content) ? sha1($content) : null;
    }

    public static function normalize_text_for_content_hash($content) {
        // Normalize line endings
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Remove BOM if present
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        // Replace multiple spaces/newlines/tabs with a single space
        $content = preg_replace('/\s+/', ' ', $content);
        return trim($content);
    }

    /**
     * Get queued submissions.
     * @param $limit
     * @return array
     */
    public static function get_queued_submissions($limit) {
        global $DB;
        $table = "plagiarism_origai_plagscan";
        return $DB->get_records(
            $table,
            ['status' => plagiarism_origai_status_enums::SCHEDULED],
            'id',
            '*',
            0,
            $limit
        );
    }

    /**
     * Handle batch scan peristence.
     * @param $scandata
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function handle_batch_results($scandata) {
        global $DB;
        $scantable = "plagiarism_origai_plagscan";

        foreach ($scandata as $itemdata) {
            try {
                $record = $DB->get_record($scantable, ['id' => $itemdata->id]);
                if ($record) {
                    $scantype = $record->scan_type;
                    /** @var \Closure $issuccessful */
                    $issuccessful = function ($scantype, $itemdata) {
                        if ($scantype == plagiarism_origai_scan_type_enums::PLAGIARISM) {
                            return !isset($itemdata->error) && isset($itemdata->plag->score);
                        }
                        if ($scantype == plagiarism_origai_scan_type_enums::AI) {
                            return !isset($itemdata->error) && isset($itemdata->ai->confidence);
                        }
                    };

                    /** @var \Closure $geterrormessage */
                    $geterrormessage = function ($scantype, $itemdata) {
                        if ($scantype == plagiarism_origai_scan_type_enums::PLAGIARISM) {
                            return isset($itemdata->error) && $itemdata->error ?
                                get_string("scanfailed", "plagiarism_origai") : null;
                        }
                        if ($scantype == plagiarism_origai_scan_type_enums::AI) {
                            return isset($itemdata->error) && $itemdata->error ? get_string("scanfailed", "plagiarism_origai") : null;
                        }
                    };
                    /** @var \Closure $countsources */
                    $countsources = function ($scantype, $itemdata) {
                        if (
                            $scantype != plagiarism_origai_scan_type_enums::PLAGIARISM ||
                            !isset($itemdata->plag->results)
                        ) {
                            return 0;
                        }
                        $sources = [];
                        foreach ($itemdata->plag->results as $result) {
                            $resultsources = array_map(function ($result) {
                                return $result->link;
                            }, $result->results);
                            $sources = array_merge($sources, $resultsources);
                        }
                        return count(array_unique($sources));
                    };

                    $record->success = $issuccessful($scantype, $itemdata);
                    $record->public_link = $itemdata->scanID;
                    $record->total_text_score = isset($itemdata->plag->score) ?
                        $itemdata->plag->score : null;
                    $record->original_score = isset($itemdata->ai->confidence->Original) ?
                        round($itemdata->ai->confidence->Original, 4) : null;
                    $record->ai_score = isset($itemdata->ai->confidence->AI) ?
                        round($itemdata->ai->confidence->AI, 4) : null;
                    $record->sources = $countsources($scantype, $itemdata);
                    $record->status = $record->success ? plagiarism_origai_status_enums::COMPLETED : plagiarism_origai_status_enums::FAILED;
                    $record->error = $geterrormessage($scantype, $itemdata);
                    $record->update_time = date('Y-m-d H:i:s');
                    static::update_scan_record($record);
                }
            } catch (\Throwable $th) {
                debugging("Error update scan record. Exception: " . $th->getMessage());
            }
        }
    }

    /**
     * Get plagiarism threshold color.
     * @param $score
     * @return string
     */
    public static function get_plag_threshold_color($score) {
        switch(true) {
            case $score == 0:
                return "green";
            break;
            case $score > 0 && $score < 15:
                return "orange";
            case $score > 15:
                return "red";
            break;
        }
    }

    /**
     * Get AI threshold color.
     * @param string $aiclassification
     * @param int $confidencescore
     *
     * @return string
     */
    public static function get_ai_threshold_color($aiclassification, $confidencescore) {
        switch(true) {
            case $aiclassification == "AI" && $confidencescore > 50:
                return "red";
            break;
            case $aiclassification == "Human":
                return "green";
            break;
        }
    }

    /**
     * Get AI classification.
     * @param float $aiscore
     * @param float $humanscore
     * @return array
     */
    public static function get_ai_classification($aiscore, $humanscore) {
        $confidencescore = $aiscore > 0.5 ? $aiscore : $humanscore;
        $aiclassification = $aiscore > 0.5 ? "AI" : "Human";
        $confidencescore = intval(round($confidencescore, 2) * 100);
        return [$aiclassification, $confidencescore];
    }
}
