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
 * Poll scan
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . '/lib.php');

use plagiarism_origai\enums\plagiarism_origai_status_enums;
use plagiarism_origai\helpers\plagiarism_origai_action;


require_login();
require_sesskey();

$scanid = required_param('scanid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
$modulename = required_param('coursemodule', PARAM_TEXT);
$returnurl = required_param("returnurl", PARAM_LOCALURL);

$scan = plagiarism_origai_action::get_scan_record_by_id($scanid);
$coursemodule = get_coursemodule_from_id($modulename, $cmid);

if (in_array(
    $scan->status,
    [plagiarism_origai_status_enums::PROCESSING, plagiarism_origai_status_enums::SCHEDULED]
)) {
    echo json_encode([
        'status' => 'processing',
        'message' => 'Scan is already in progress',
        'renderhtml' => null
    ]);
    die;
}

if ($scan->status == plagiarism_origai_status_enums::FAILED) {
    echo json_encode([
        'status' => 'completed',
        'message' => 'Scan is completed',
        'renderhtml' => plagiarism_plugin_origai::build_scan_failed_component(
            $scan,
            $modulename,
            $cmid,
            $returnurl
        )
    ]);
    die;
}

echo json_encode([
    'status' => 'completed',
    'message' => 'Scan is completed',
    'renderhtml' => plagiarism_plugin_origai::build_scan_successful_component(
        $scan,
        $cmid,
        null,
        $scan->userid,
        $coursemodule
    )
]);
die;
