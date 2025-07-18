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
 * Resumit failed scans
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . '/lib.php');

use plagiarism_origai\helpers\plagiarism_origai_plugin_config;
use plagiarism_origai\helpers\plagiarism_origai_action;
use plagiarism_origai\enums\plagiarism_origai_status_enums;
use plagiarism_origai\enums\plagiarism_origai_retry_mode_enums;

require_login();
require_sesskey();

$scanid = null;
$mode = required_param('mode', PARAM_TEXT);
if ($mode == plagiarism_origai_retry_mode_enums::SINGLE) {
    $scanid = required_param('scanid', PARAM_INT);
}

$cmid = required_param('cmid', PARAM_INT);
$modulename = required_param('coursemodule', PARAM_TEXT);
$returnurl = required_param("returnurl", PARAM_LOCALURL);

global $DB, $PAGE;

$coursemodule = get_coursemodule_from_id($modulename, $cmid);
$context = context_course::instance($coursemodule->course);

if (!$context) {
    redirect($returnurl, get_string('scanfailed', 'plagiarism_origai'), null, \core\output\notification::NOTIFY_ERROR);
}

require_capability('mod/assign:grade', $context);

$enabled = plagiarism_origai_plugin_config::is_module_enabled($modulename, $cmid);
if (!$enabled) {
    redirect($returnurl, get_string('pluginname', 'plagiarism_origai') . "not enabled/configured", null, \core\output\notification::NOTIFY_ERROR);
}
switch ($mode) {
    case plagiarism_origai_retry_mode_enums::SINGLE:
        resubmit_single_failed_scan($scanid, $returnurl);
        break;
    default:
        throw new \core\exception\invalid_parameter_exception("param(mode) is invalid");
}


function resubmit_single_failed_scan($scanid, $returnurl)
{
    $scan = plagiarism_origai_action::get_scan_record_by_id($scanid);
    if (
        !$scan ||
        ($scan && $scan->status != plagiarism_origai_status_enums::FAILED)
    ) {
        redirect($returnurl, get_string('scanfailed', 'plagiarism_origai'), null, \core\output\notification::NOTIFY_ERROR);
    }
    $scan->status = plagiarism_origai_status_enums::SCHEDULED;
    $scan->error = null;
    $scan->success = null;
    plagiarism_origai_action::update_scan_record($scan);
    redirect($returnurl, get_string('scanqueuednotification', 'plagiarism_origai'), null, \core\output\notification::NOTIFY_INFO);
}
