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
  * Scan report page
  * @package   plagiarism_origai
  * @category  plagiarism
  * @copyright Originality.ai, https://originality.ai
  * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  */
require(dirname(dirname(__FILE__)) . '/../config.php');
require_once('../origai/lib.php');

use core\output\html_writer;
use plagiarism_origai\helpers\plagiarism_origai_plugin_config;

// Get url params.
$scanid = required_param('scanid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$modulename = required_param('modulename', PARAM_TEXT);
$scantype = required_param('scantype', PARAM_TEXT);

global $USER;
// Get instance modules.
$cm = get_coursemodule_from_id($modulename, $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

// Request login.
require_login($course, true, $cm);

// Setup page meta data.
$context = context_course::instance($cm->course);
$PAGE->set_course($course);
$PAGE->set_cm($cm);
$PAGE->set_pagelayout('incourse');

$PAGE->set_url(
    '/moodle/plagiarism/origai/plagiarism_origai_report.php',
    [
        'scanid' => $scanid,
        'cmid' => $cmid,
        'userid' => $userid,
        'modulename' => $modulename,
    ]
);

// Setup page title and header.
$user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
$pagetitle = get_string('reportpagetitle', 'plagiarism_origai') . ' - ' . fullname($user);

$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();

$modulesettings = $DB->get_records_menu('plagiarism_origai_config', ['cm' => $cmid], '', 'name,value');

$instructor = has_capability('mod/assign:grade', $context);

$moduleenabled = plagiarism_origai_is_plugin_configured('mod_' . $cm->modname);

$studentcanviewreport = plagiarism_origai_plugin_config::get_cm_config(
    $cmid,
    'plagiarism_origai_allow_student_report_access',
    false
);

if (!$moduleenabled) {
    echo html_writer::div(get_string('disabledformodule', 'plagiarism_origai'), 'alert alert-warning');
} else {
    $owners = [$userid];
    if ($instructor || ($studentcanviewreport && in_array($USER->id, $owners))) {
        $moduledata = $DB->get_record($cm->modname, ['id' => $cm->instance]);
        $scanresult = $DB->get_record(
            'plagiarism_origai_plagscan',
            ['id' => $scanid, 'cmid' => $cmid, 'userid' => $userid, 'scan_type' => $scantype]
        );
        if ($scanresult) {
            $uniqueid = basename($scanresult->public_link);
            $api = new \plagiarism_origai\helpers\plagiarism_origai_api();
            $response = $api->get_report($uniqueid);

            if (
                $response === false || (isset($response->success) && !$response->success)
            ) {
                $error = isset($response->message) ?
                    $response->message : get_string('scanreportfailed', 'plagiarism_origai');
                echo html_writer::div($error, 'alert alert-danger');
            } else {
                // Container
                $report = html_writer::start_div(
                    'origai-container',
                    ['id' => 'origai-container', 'style' => 'position: relative;']
                );

                $viewinfullscreen = html_writer::link(
                    '#',
                    get_string('fullscreenview', 'plagiarism_origai') .
                        html_writer::tag('i', '', [
                            'class' => 'fa-solid fa-up-right-and-down-left-from-center ml-2',
                            'title' => get_string('fullscreenview', 'plagiarism_origai'),
                        ]),
                    [
                        'id' => 'origai-fullscreen-btn',
                        'class' => 'origai-action-link',
                        'style' => 'cursor: pointer; text-decoration: none;',
                    ]
                );

                echo html_writer::div($viewinfullscreen, 'my-3');


                // Responsive iframe wrapper.
                $report .= html_writer::start_div('', [
                    'id' => 'origai-iframe-wrapper',
                    'style' => 'width: 100%; height: 100vh; overflow: hidden; position: relative;',
                ]);

                $iframeurl = $response->data->report_link;


                $report .= html_writer::tag('iframe', '', [
                    'src' => $iframeurl,
                    'style' => 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;',
                    'allowfullscreen' => 'true',
                    'loading' => 'lazy',
                    'title' => get_string('reportpagetitle', 'plagiarism_origai'),
                    'sandbox' => 'allow-scripts allow-same-origin allow-downloads',
                ]);

                $report .= html_writer::end_div(); // iframe-wrapper
                $report .= html_writer::end_div(); // container

                echo $report;

                $PAGE->requires->js_init_code("
                    const button = document.getElementById('origai-fullscreen-btn');
                    const iframe = document.querySelector('#origai-iframe-wrapper iframe');

                    if (button && iframe) {
                        button.addEventListener('click', function () {
                            if (
                                document.fullscreenEnabled ||
                                document.webkitFullscreenEnabled ||
                                document.mozFullScreenEnabled ||
                                document.msFullscreenEnabled
                            ) {
                                if (iframe.requestFullscreen) {
                                    iframe.requestFullscreen();
                                } else if (iframe.webkitRequestFullscreen) {
                                    iframe.webkitRequestFullscreen();
                                } else if (iframe.mozRequestFullScreen) {
                                    iframe.mozRequestFullScreen();
                                } else if (iframe.msRequestFullscreen) {
                                    iframe.msRequestFullscreen();
                                }
                            } else {
                                alert('Fullscreen not supported in your browser');
                            }
                        });

                        function fullscreenChange() {
                            if (document.fullscreenElement ||
                                document.webkitIsFullScreen ||
                                document.mozFullScreen ||
                                document.msFullscreenElement) {
                                console.log('Entered fullscreen');
                            } else {
                                console.log('Exited fullscreen');
                            }
                            iframe.src = iframe.src;
                        }

                        document.addEventListener('fullscreenchange', fullscreenChange);
                        document.addEventListener('webkitfullscreenchange', fullscreenChange);
                        document.addEventListener('mozfullscreenchange', fullscreenChange);
                        document.addEventListener('MSFullscreenChange', fullscreenChange);
                    }
                ");
            }
        }
        else {
            echo html_writer::div(get_string('scanreportfailed', 'plagiarism_origai'), 'alert alert-warning');
        }
    }
    else{
        echo html_writer::div(get_string('nopageaccess', 'plagiarism_origai'), 'alert alert-danger');
    }
}
echo $OUTPUT->footer();
