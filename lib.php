<?php
// This file is part of Moodle - http://moodle.org/
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
 * Plugin class for origai plagiarism plugin
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/plagiarism/lib.php');

use core\output\html_writer;
use plagiarism_origai\helpers\plagiarism_origai_plugin_config;
use plagiarism_origai\helpers\plagiarism_origai_text_extractor;
use plagiarism_origai\helpers\plagiarism_origai_action;
use plagiarism_origai\enums\plagiarism_origai_scan_type_enums;
use plagiarism_origai\enums\plagiarism_origai_status_enums;
use plagiarism_origai\enums\plagiarism_origai_retry_mode_enums;

/**
 * Plugin class for origai plagiarism plugin
 */
class plagiarism_plugin_origai extends plagiarism_plugin {

    /**
     * hook to allow plagiarism specific information to be displayed beside a submission
     * @param array  $linkarray contains all relevant information for the plugin to generate a link
     * @return string
     *
     */
    public function get_links($linkarray) {
        global $OUTPUT, $DB, $USER, $CFG, $COURSE, $PAGE;

        $cmid = $linkarray['cmid'] ?? null;
        $userid = $linkarray['userid'] ?? null;
        $itemid = $linkarray['itemid'] ?? null;
        $fileerror = null;

        $content = '';
        if (!empty($linkarray['content'])) {
            $content = $linkarray['content'];
        } else if (
            !empty($linkarray["file"]) &&
            $linkarray["file"] instanceof \stored_file &&
            $linkarray['file']->get_filename() !== '.'
        ) {
            try {
                $filearea = $linkarray['file']->get_filearea();
                // Dont show file area of type feedback_files OR introattachment.
                if (in_array($filearea, ["feedback_files", "introattachment"])) {
                    return;
                }
                $textextractor = new plagiarism_origai_text_extractor($linkarray['file']);
                if (!$textextractor->is_mime_type_supported()) {
                    throw new \core\exception\moodle_exception(
                        get_string('fileattachmentnotsupported', 'plagiarism_origai')
                    );
                }
                $extractedtext = $textextractor->extract();
                if ($extractedtext) {
                    $content = $extractedtext;
                } else {
                    throw new \core\exception\moodle_exception(
                        get_string('textextractionfailed', 'plagiarism_origai')
                    );
                }
            } catch (\core\exception\moodle_exception $e) {
                $fileerror = $e->getMessage();
                $content = "";
            } catch (\Throwable $e) {
                debugging('Error extracting text from file: ' . $e->getMessage());
                $fileerror = get_string('textextractionfailed', 'plagiarism_origai');
                $content = "";
            }
        }

        $quizcomponent = !empty($linkarray['component'] ?? "") ? $linkarray['component'] : "";
        if (empty($linkarray['cmid']) && !empty($linkarray['area']) && $quizcomponent == "qtype_essay") {
            $quizquestions = question_engine::load_questions_usage_by_activity($linkarray['area']);

            // Try to get cm using the questions owning context.
            $context = $quizquestions->get_owning_context();
            if ($context->contextlevel == CONTEXT_MODULE) {
                $linkarray['cmid'] = $context->instanceid;
                $cmid = $linkarray['cmid'];
                $itemid = $linkarray['area'];
            }
        }

        // Get the course module.
        static $coursemodule;
        if (empty($coursemodule)) {
            $coursemodule = get_coursemodule_from_id(
                '',
                $linkarray["cmid"]
            );
        }

        // Get origai plugin admin config.
        static $adminconfig;
        if (empty($adminconfig)) {
            $adminconfig = get_config('plagiarism_origai');
        }

        static $context;
        $context = context_course::instance($coursemodule->course);

        // Check current user is instructor.
        static $isinstructor;
        if (empty($isinstructor)) {
            $isinstructor = has_capability('mod/assign:grade', $context);
        }

        static $studentcanviewreport;
        if (empty($studentcanviewreport)) {
            $studentcanviewreport = plagiarism_origai_plugin_config::get_cm_config(
                $cmid,
                'plagiarism_origai_allow_student_report_access',
                false
            );
        }

        // Display file errors to instructor.
        if (!empty($fileerror) && $isinstructor) {
            $PAGE->requires->js_init_code("
                document.addEventListener('DOMContentLoaded', function load() {
                    if (!window.jQuery) return setTimeout(load, 50);
                    jQuery('[data-toggle=\"tooltip\"]').tooltip();
                }, false);
            ");
            $output = '';
            $output .= html_writer::tag('i', '', [
                'class' => 'fa fa-exclamation-triangle me-2 text-danger',
                'title' => $fileerror,
                'aria-label' => $fileerror,
                'data-toggle' => 'tooltip',
            ]);
            return $output;
        }

        if ((!$isinstructor && !$studentcanviewreport) || !$cmid || !$content) {
            return '';
        }

        // Check plugin & module is enabled
        $modulename = $coursemodule->modname;
        $enabled = plagiarism_origai_plugin_config::is_module_enabled($modulename, $cmid);
        if (!$enabled) {
            return '';
        }

        $responses = [];

        foreach ([
                plagiarism_origai_scan_type_enums::PLAGIARISM,
                plagiarism_origai_scan_type_enums::AI,
            ] as $scantype) {
            $record = plagiarism_origai_action::get_scan_record(
                $cmid,
                $userid,
                $content,
                $scantype
            );

            if ($record) {
                // Backward compatibility: treat missing status as pending
                if (is_null($record->success) && is_null($record->status)) {
                    $record->status = plagiarism_origai_status_enums::PENDING;
                    plagiarism_origai_action::update_scan_record($record);
                }
            } else {
                $record = plagiarism_origai_action::create_scan_record([
                    'status'      => plagiarism_origai_status_enums::PENDING,
                    'scan_type'   => $scantype,
                    'cmid'        => $cmid,
                    'userid'      => $userid,
                    'itemid'      => $itemid,
                    'title'       => $title ?? substr(html_to_text($content, 0, false), 0, 255),
                    'content'     => $content,
                    'contenthash' => plagiarism_origai_action::generate_content_hash($content),
                ]);
            }

            $responses[] = $record;
        }

        // Skip result if scan isnt completed.
        if ((!$isinstructor && $studentcanviewreport)) {
            $scanwithresults = array_filter($responses, function ($response) {
                return $response->success;
            });
            if (count($scanwithresults) == 0) {
                return '';
            }
        }

        $output = '';
        // Main container.
        $output = html_writer::start_div('origai-report-container');
        // Logo section.
        $output .= html_writer::div(
            html_writer::img(
                $OUTPUT->image_url('originality-logo', 'plagiarism_origai'),
                get_string('originalityai', 'plagiarism_origai'),
                ['class' => 'origai-logo']
            ),
            'origai-logo-container'
        );

        // Generate links for both scan types.
        foreach ($responses as $response) {
            if ((!$isinstructor && $studentcanviewreport)) {
                if (!$response->success) {
                    continue;
                }
            }
            // Handle pending state.
            if ($response->status == plagiarism_origai_status_enums::PENDING) {
                $iconclass = $response->scan_type == plagiarism_origai_scan_type_enums::AI
                    ? 'fa-brain' : 'fa-copy';
                $output .= $this->build_scan_trigger([
                    'scanid' => $response->id,
                    'cmid' => $cmid,
                    'modulename' => $modulename,
                    'iconclass' => 'ml-1 fa-solid ' . $iconclass,
                    'title' => get_string(
                        $response->scan_type == plagiarism_origai_scan_type_enums::AI ?
                            'runaicheck' : 'runplagiarismcheck',
                        'plagiarism_origai'
                    ),
                ]);
                continue;
            }

            // Handle failure state.
            if (
                (!$response->success && !empty($response->success)) ||
                $response->status == plagiarism_origai_status_enums::FAILED
            ) {
                $output .= $this->build_scan_failed_component($response, $modulename, $cmid);
                continue;
            }

            // Handle in progress state.
            if (
                $response->status == plagiarism_origai_status_enums::PROCESSING ||
                $response->status == plagiarism_origai_status_enums::SCHEDULED
            ) {
                $output .= $this->build_scan_processing_component($response);
                continue;
            }

            if ($response->success) {
                $output .= $this->build_scan_successful_component(
                    $response,
                    $cmid,
                    $itemid,
                    $userid,
                    $coursemodule
                );
            }
        }
        $output .= html_writer::end_div(); // Close main container.

        return $output;
    }

    /**
     * Generate output html for scan trigger.
     * @param $options
     * @return string
     */
    private function build_scan_trigger($options) {
        $returnurl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        $fields = [
            'scanid'     => $options['scanid'],
            'cmid' => $options['cmid'],
            'coursemodule' => $options['modulename'],
            'sesskey'    => sesskey(),
            'returnurl'  => $returnurl,
        ];

        $url = (new \moodle_url('/plagiarism/origai/scan_content.php', $fields))->out(false);

        $iconclass = $options['iconclass'];
        $title     = $options['title'];

        $trigger = html_writer::tag(
            'a',
            html_writer::tag('span', $title) .
                html_writer::tag('i', '', ['class' => $iconclass, 'title' => $title]),
            [
                'href'  => $url,
                'class' => 'origai-scan-trigger origai-action-button my-2 mr-2',
            ]
        );

        return $trigger;
    }

    /**
     * Generate output html for scan failure.
     * @param object $response
     * @param string $modulename
     * @param int $cmid
     * @return string
     */
    private function build_scan_failed_component($response, $modulename, $cmid) {
        $returnurl = (isset($_SERVER['HTTPS']) ?
            "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $output = "";

        $tooltiptext = !empty($response->error) ?
            s($response->error) : get_string('defaultscanerror', 'plagiarism_origai');
        $retrytext = get_string('retryscan', 'plagiarism_origai');
        $scanfailedtext = get_string('scanfailed', 'plagiarism_origai');
        $output = html_writer::start_div('origai-section'); // section
        if ($response->scan_type == plagiarism_origai_scan_type_enums::PLAGIARISM) {
            $output .= html_writer::tag('h3', get_string('plagresulttitle', 'plagiarism_origai'), ['class' => 'origai-section-title']);
        } else if ($response->scan_type == plagiarism_origai_scan_type_enums::AI) {
            $output .= html_writer::tag('h3', get_string('airesulttitle', 'plagiarism_origai'), ['class' => 'origai-section-title']);
        }
        $output .= html_writer::start_div('d-flex align-items-center text-danger');
        $output .= html_writer::tag('i', '', [
            'class' => 'fa fa-exclamation-circle me-2',
            'title' => $tooltiptext,
            'aria-label' => $tooltiptext,
            'data-toggle' => 'tooltip',
        ]);
        $output .= $scanfailedtext;
        $output .= html_writer::link(
            (new moodle_url('/plagiarism/origai/resubmit_failed_scan.php', [
                'mode' => plagiarism_origai_retry_mode_enums::SINGLE,
                'scanid' => $response->id,
                'coursemodule' => $modulename,
                'cmid' => $cmid,
                'sesskey' => sesskey(),
                'returnurl' => $returnurl,
            ]))->out(false),
            html_writer::tag('i', '', [
                'class' => 'fa fa-repeat',
                'title' => $retrytext,
                'aria-label' => $retrytext,
                'data-toggle' => 'tooltip',
            ]),
            [
                'class' => 'ml-3',
            ]
        );
        $output .= html_writer::end_div(); // d-flex.
        $output .= html_writer::end_div(); // section.
        return $output;
    }

    /**
     * Generate output html for scan results.
     * @param object $response
     * @param int $cmid
     * @param int $itemid
     * @param int $userid
     * @param object $coursemodule
     * @return string
     */
    private function build_scan_successful_component($response, $cmid, $itemid, $userid, $coursemodule) {
        $output = "";

        $reporturl = new moodle_url("/plagiarism/origai/plagiarism_origai_report.php" . "?" . http_build_query([
            'scanid' => $response->id,
            'cmid' => $cmid,
            'itemid' => $itemid,
            'userid' => $userid,
            'modulename' => $coursemodule->modname,
            'scantype' => $response->scan_type,
        ]));
        if ($response->scan_type == plagiarism_origai_scan_type_enums::PLAGIARISM) {
            $thresholdcolor = plagiarism_origai_action::get_plag_threshold_color($response->total_text_score);
            $output .= html_writer::start_div('origai-section origai-section-' . $thresholdcolor);
            $output .= html_writer::tag(
                'h3',
                get_string('plagresulttitle', 'plagiarism_origai'),
                ['class' => 'origai-section-title']
            );
            $output .= html_writer::start_div('d-flex align-items-center');
            $output .= html_writer::span('', 'origai-dot me-1', ['style' => 'width: 8px; height: 8px;']);
            $output .= html_writer::link($reporturl, round((float) $response->total_text_score) . '%', ['class' => 'origai-percentage']);
            $output .= html_writer::end_div(); // d-flex.
            $output .= html_writer::end_div(); // origai-sectio.
        } else if ($response->scan_type == plagiarism_origai_scan_type_enums::AI) {
            list($aiclassification, $confidencescore) = plagiarism_origai_action::get_ai_classification(
                $response->ai_score,
                $response->original_score
            );
            $thresholdcolor = plagiarism_origai_action::get_ai_threshold_color($aiclassification, $confidencescore);
            $output .= html_writer::start_div('origai-section origai-section-' . $thresholdcolor);
            $classifytext = $aiclassification == "AI" ?
                get_string('aipercentage', 'plagiarism_origai', $confidencescore . '%') :
                get_string('humanpercentage', 'plagiarism_origai', $confidencescore . '%');
            $output .= html_writer::tag(
                'h3',
                get_string('airesulttitle', 'plagiarism_origai'),
                ['class' => 'origai-section-title']
            );
            $output .= html_writer::start_div('d-flex align-items-center');
            $output .= html_writer::span('', 'origai-dot me-1', ['style' => 'width: 8px; height: 8px;']);
            $output .= html_writer::link($reporturl, $classifytext, ['class' => 'origai-percentage']);
            $output .= html_writer::end_div();
            $output .= html_writer::end_div();
        }
        return $output;
    }

    /**
     * Generate output html for scan processing.
     * @param object $response
     * @return string
     */
    private function build_scan_processing_component($response) {
        global $OUTPUT;
        $output = '';
        // Create spinner icon.
        $spinner = $OUTPUT->pix_icon(
            'i/loading',
            get_string('loading', 'admin'),
            '',
            ['class' => 'origai-spinner me-2']
        );
        $loadingstring = $response->status == plagiarism_origai_status_enums::PROCESSING ?
            get_string('scaninprogress', 'plagiarism_origai') :
            get_string('scanqueued', 'plagiarism_origai');

        if ($response->scan_type == plagiarism_origai_scan_type_enums::PLAGIARISM) {
            $output .= html_writer::start_div('origai-section');
            $output .= html_writer::tag(
                'h3',
                get_string('plagresulttitle', 'plagiarism_origai'),
                ['class' => 'origai-section-title']
            );
            $output .= html_writer::start_div('d-flex align-items-center');
            $output .= $spinner . $loadingstring;
            $output .= html_writer::end_div(); // d-flex.
            $output .= html_writer::end_div(); // origai-section.

        } else if ($response->scan_type == plagiarism_origai_scan_type_enums::AI) {
            $output .= html_writer::start_div('origai-section');
            $output .= html_writer::tag(
                'h3',
                get_string('airesulttitle', 'plagiarism_origai'),
                ['class' => 'origai-section-title']
            );
            $output .= html_writer::start_div('d-flex align-items-center');
            $output .= $spinner . $loadingstring;
            $output .= html_writer::end_div(); // d-flex.
            $output .= html_writer::end_div(); // origai-section.
        }
        return $output;
    }

    /**
     * hook to allow a disclosure to be printed notifying users what will happen with their submission.
     *
     * @param int $cmid - course module id
     * @return string
     */
    public function print_disclosure($cmid) {
        global $OUTPUT;

        if (empty($cmid)) {
            return '';
        }
        // Get course details.
        $cm = get_coursemodule_from_id('', $cmid);
        if (!$cm) {
            return '';
        }

        // Check module is enabled.
        $modulename = $cm->modname;
        if (!plagiarism_origai_plugin_config::is_module_enabled($modulename, $cmid)) {
            return '';
        }

        $result = '';

        $result .= $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
        $studentdisclosure = plagiarism_origai_plugin_config::admin_config('plagiarism_origai_studentdisclosure');
        if (!$studentdisclosure) {
            $studentdisclosure = plagiarism_origai_plugin_config::get_default_student_disclosure();
        }

        $formatoptions = new stdClass;
        $formatoptions->noclean = true;

        $result .= '<div class="alert alert-info">';
        $result .= format_text($studentdisclosure, FORMAT_MOODLE, $formatoptions);
        $result .= '</div>';
        $result .= $OUTPUT->box_end();

        return $result;
    }
}

/**
 * Check if plugin is configured.
 * @param $modudlename
 * @return bool
 */
function plagiarism_origai_is_plugin_configured($modulename) {
    $apikey = get_config('plagiarism_origai', 'apikey');
    $apiurl = get_config('plagiarism_origai', 'apiurl');

    if (empty($apikey) || empty($apiurl)) {
        return false;
    }

    $moduleconfigname = 'plagiarism_origai_' . $modulename;
    $moduleenabled = get_config('plagiarism_origai', $moduleconfigname);
    if (!$moduleenabled) {
        return false;
    }

    return true;
}

/**
 * Add the Originality.ai settings form to an add/edit activity page.
 *
 * @param moodleform_mod $formwrapper
 * @param MoodleQuickForm $mform
 * @return mixed
 * @package plagiarism_origai
 */
function plagiarism_origai_coursemodule_standard_elements($formwrapper, $mform) {
    global $DB;
    $context = context_course::instance($formwrapper->get_course()->id);
    $modulename = $formwrapper->get_current()->modulename;

    if (!$context || !isset($modulename)) {
        return;
    }

    if (has_capability('plagiarism/origai:enable', $context)) {

        // Return no form if the plugin isn't configured or not enabled.
        if (!plagiarism_origai_is_plugin_configured("mod_" . $modulename)) {
            return;
        }

        $mform->addElement(
            'header',
            'plagiarism_origai_defaultsettings',
            get_string('origaicoursesettings', 'plagiarism_origai')
        );

        $mform->addElement(
            'advcheckbox',
            'plagiarism_origai_enable',
            get_string('origaienable', 'plagiarism_origai')
        );

        $mform->addElement(
            'select',
            'plagiarism_origai_ai_model',
            get_string('aiModel', 'plagiarism_origai'),
            plagiarism_origai_plugin_config::get_models()
        );
        $mform->addHelpButton('plagiarism_origai_ai_model', 'aiModel', 'plagiarism_origai');

        $mform->addElement(
            'advcheckbox',
            'plagiarism_origai_automated_scan',
            get_string('enableautomatedscan', 'plagiarism_origai')
        );

        $mform->addHelpButton('plagiarism_origai_automated_scan', 'enableautomatedscan', 'plagiarism_origai');

        $mform->addElement(
            'advcheckbox',
            'plagiarism_origai_allow_student_report_access',
            get_string('allowstudentreportaccess', 'plagiarism_origai')
        );

        $cmid = optional_param('update', null, PARAM_INT);
        $savedvalues = $DB->get_records_menu('plagiarism_origai_config', ['cm' => $cmid], '', 'name,value');

        $admindefaultmodel = plagiarism_origai_plugin_config::admin_config('aiModel');

        if (count($savedvalues) > 0) {
            $mform->setDefault(
                'plagiarism_origai_enable',
                isset($savedvalues['plagiarism_origai_enable']) ? $savedvalues['plagiarism_origai_enable'] : 0
            );
            $mform->setDefault(
                'plagiarism_origai_ai_model',
                isset($savedvalues['plagiarism_origai_ai_model']) ?
                    $savedvalues['plagiarism_origai_ai_model'] : $admindefaultmodel
            );
            $mform->setDefault(
                'plagiarism_origai_automated_scan',
                isset($savedvalues['plagiarism_origai_automated_scan']) ?
                    $savedvalues['plagiarism_origai_automated_scan'] : 0
            );
            $mform->setDefault(
                'plagiarism_origai_allow_student_report_access',
                isset($savedvalues['plagiarism_origai_allow_student_report_access']) ?
                    $savedvalues['plagiarism_origai_allow_student_report_access'] : 0
            );
        } else {

            $mform->setDefaults([
                'plagiarism_origai_enable' => 0,
                'plagiarism_origai_ai_model' => $admindefaultmodel,
                'plagiarism_origai_automated_scan' => 0,
                'plagiarism_origai_allow_student_report_access' => 0,
            ]);
        }
    }
}

/**
 * Handle saving data from the Originality.ai settings form.
 *
 * @param stdClass $data
 * @param stdClass $course
 * @return stdClass
 * @package plagiarism_origai
 */
function plagiarism_origai_coursemodule_edit_post_actions($data, $course) {
    if (empty($data->modulename)) {
        return $data;
    }

    // Check if plagiarism plugin is enabled for this module.
    if (!plagiarism_origai_is_plugin_configured('mod_' . $data->modulename)) {
        return $data;
    }
    $cm = $data->coursemodule;
    $cmproperties = [
        "plagiarism_origai_enable",
        "plagiarism_origai_ai_model",
        "plagiarism_origai_automated_scan",
        "plagiarism_origai_allow_student_report_access",
    ];
    foreach ($cmproperties as $cmproperty) {
        plagiarism_origai_plugin_config::set_cm_config(
            $cm,
            $cmproperty,
            $data->{$cmproperty}
        );
    }
    return $data;
}

/**
 * dump variables
 * @param mixed $values
 * @return void
 * @package plagiarism_origai
 */
function dump($values) {
    $arg = func_get_args();
    print(var_dump(...$arg) . PHP_EOL);
}

/**
 * dump variables and exit
 * @param mixed $values
 * @return void
 * @package plagiarism_origai
 */
function dd($values) {
    $arg = func_get_args();
    exit(var_dump(...$arg) . PHP_EOL);
}
