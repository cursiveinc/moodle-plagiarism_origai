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
  * Adhoc scan
  * @package   plagiarism_origai
  * @category  plagiarism
  * @copyright Originality.ai, https://originality.ai
  * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  */
require_once ('../../config.php');
require_once ($CFG->libdir . '/filelib.php');
require_once (__DIR__ .'/lib.php');

use plagiarism_origai\helpers\plagiarism_origai_plugin_config;
use plagiarism_origai\helpers\plagiarism_origai_action;
use plagiarism_origai\enums\plagiarism_origai_status_enums;

require_login();
require_sesskey();

$cmid = required_param('cmid', PARAM_INT);
$scanid = required_param('scanid', PARAM_INT);
$modulename = required_param('coursemodule', PARAM_TEXT);
$isasync = optional_param('isasync', 0, PARAM_INT);
$returnurl = required_param("returnurl", PARAM_LOCALURL);

global $DB, $PAGE;
if(is_null($isasync)){
    $isasync = 0;
}

$coursemodule = get_coursemodule_from_id($modulename, $cmid);
$context = context_course::instance($coursemodule->course);

$scan = plagiarism_origai_action::get_scan_record_by_id($scanid);

if(
    !$context ||
    !$scan ||
    ($scan && $scan->status != plagiarism_origai_status_enums::PENDING)
){
    if($isasync){
        echo json_encode([
            'status' => 'error',
            'message' => get_string('scanfailed', 'plagiarism_origai'),
            'renderhtml' => plagiarism_plugin_origai::build_scan_failed_component($scan, $modulename, $cmid, $returnurl, get_string('scanfailed', 'plagiarism_origai'))
        ]);
        exit;
    }
    redirect($returnurl, get_string('scanfailed', 'plagiarism_origai'), null, \core\output\notification::NOTIFY_ERROR);
}

require_capability('mod/assign:grade', $context);

$enabled = plagiarism_origai_plugin_config::is_module_enabled($modulename, $cmid);
if (!$enabled) {
    if($isasync){
        echo json_encode([
            'status' => 'error',
            'message' => get_string('pluginname', 'plagiarism_origai') . "not enabled/configured",
            'renderhtml' => plagiarism_plugin_origai::build_scan_failed_component($scan, $modulename, $cmid, $returnurl, get_string('pluginname', 'plagiarism_origai') . "not enabled/configured")
        ]);
        exit;
    }
    redirect($returnurl, get_string('pluginname', 'plagiarism_origai') . "not enabled/configured", null, \core\output\notification::NOTIFY_ERROR);
}

$scan->status = plagiarism_origai_status_enums::SCHEDULED;
plagiarism_origai_action::update_scan_record($scan);

if($isasync){
    echo json_encode([
        'status' => 'success',
        'message' => get_string('scanqueuednotification', 'plagiarism_origai'),
        'renderhtml' => plagiarism_plugin_origai::build_scan_processing_component($scan, $modulename)
    ]);
    exit;
}
redirect($returnurl, get_string('scanqueuednotification', 'plagiarism_origai'), null, \core\output\notification::NOTIFY_INFO);
