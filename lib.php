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


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//get global class
global $CFG;
require_once($CFG->dirroot . '/plagiarism/lib.php');
require_once(__DIR__ . '/vendor/autoload.php');

use plagiarism_origai\helpers\plagiarism_origai_text_extractor;


class plagiarism_plugin_origai extends plagiarism_plugin
{
    /**
     * hook to allow plagiarism specific information to be displayed beside a submission 
     * @param array  $linkarray contains all relevant information for the plugin to generate a link
     * @return string
     * 
     */
    public function get_links($linkarray)
    {
        global $OUTPUT, $DB, $USER, $CFG, $COURSE, $PAGE;

        //$userid, $file, $cmid, $course, $module
        $cmid = $linkarray['cmid'];
        $userid = $linkarray['userid'];
        $fileerror = null;

        $content = "";
        if (!empty($linkarray['content'])) {
            $content = $linkarray['content'];
            $content = $linkarray['content'];
        } else if(
            !empty($linkarray["file"]) &&
            $linkarray["file"] instanceof \stored_file &&
            $linkarray['file']->get_filename() !== '.'
        ){
            try {
                $textextractor = new plagiarism_origai_text_extractor($linkarray['file']);
                if (!$textextractor->is_mime_type_supported()) {
                    throw new \core\exception\moodle_exception(get_string('fileattachmentnotsupported', 'plagiarism_origai'));
                }
                $extractedtext = $textextractor->extract();
                if ($extractedtext) {
                    $content = $extractedtext;
                } else {
                    throw new \core\exception\moodle_exception(get_string('textextractionfailed', 'plagiarism_origai'));
                }
            } catch (\core\exception\moodle_exception $e) {
                $fileerror = $e->getMessage();
                $content = "";
            } catch (\Throwable $e) {
                error_log('Error extracting text from file: ' . $e->getMessage());
                $fileerror = get_string('textextractionfailed', 'plagiarism_origai');
                $content = "";
            }
        }

        $itemid = $linkarray['itemid'] ?? null;

        $output = '';
        //add link/information about this file to $output

        $quizcomponent = (!empty($linkarray['component'])) ? $linkarray['component'] : "";
        if (empty($linkarray['cmid']) && !empty($linkarray['area']) && $quizcomponent == "qtype_essay") {
            $quizquestions = question_engine::load_questions_usage_by_activity($linkarray['area']);

            // Try to get cm using the questions owning context.
            $context = $quizquestions->get_owning_context();
            if ($context->contextlevel == CONTEXT_MODULE) {
                $linkarray['cmid'] = $context->instanceid;
                $cmid = $linkarray['cmid'];
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

        if (!empty($fileerror) && $isinstructor) {
            $PAGE->requires->js_init_code("
                document.addEventListener('DOMContentLoaded', function load() {
                    if (!window.jQuery) return setTimeout(load, 50);
                    jQuery('[data-toggle=\"tooltip\"]').tooltip();
                }, false);
            ");
            $output = '';
            $output .= html_writer::tag('i', '', [
                'class' => 'fa fa-exclamation-triangle me-2, text-danger',
                'title' => $fileerror,
                'aria-label' => $fileerror,
                'data-toggle' => 'tooltip'
            ]);
            return $output;
        }

        if ((!empty($linkarray["cmid"])) && (!empty($content) || (!empty($linkarray["file"]) && ($linkarray["file"]->get_mimetype() === 'application/pdf' || $linkarray["file"]->get_mimetype() === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))) && $isinstructor) {

            if (!plagiarism_origai_is_plugin_configured("mod_" . $coursemodule->modname)) {
                return "";
            }

            if ($coursemodule->modname == 'forum' || $coursemodule->modname == 'assign') {
                $enabled = $DB->get_record('plagiarism_origai_config', array('cm' => $cmid));
                if (!$enabled || $enabled && !$enabled->value) {
                   return "";
                }
            }

            //check whether scan records exists in scan table
            $sql = "SELECT * FROM {plagiarism_origai_plagscan} 
                    WHERE cmid = ? 
                    AND userid = ? 
                    AND  " . ($itemid === null ? "itemid IS NULL" : "itemid = ?") .
                " AND " . $DB->sql_compare_text('content') . " = ?";

            $params = $itemid === null ? [$cmid, $userid, $content] : [$cmid, $userid, $itemid, $content];
            $responses = $DB->get_records_sql($sql, $params);

            //check whether responses array contain responses for plagiarism scan or ai scan
            if (empty($responses)) {
                $respObj = new stdClass();
                $respObj->userid = $userid;
                $respObj->cmid = $cmid;
                $respObj->content = $content;
                $respObj->itemid = $itemid;
                $respObj->scan_type = 'plagiarism';
                $plag_scanid = $DB->insert_record('plagiarism_origai_plagscan', $respObj);
                $respObj->scan_type = 'ai';
                $ai_scanid = $DB->insert_record('plagiarism_origai_plagscan', $respObj);
                //show both scan icons
                $plagiarism_scanurl = "$CFG->wwwroot/plagiarism/origai/scan_content.php" .
                    "?scanid=$plag_scanid&cmid=$cmid&itemid=$itemid&userid=$userid&coursemodule=$coursemodule->modname&scantype=plagiarism";
                $output = "<div class='origai-getscan-button'>" .
                    html_writer::link(
                        "$plagiarism_scanurl",
                        html_writer::start_tag(
                            'i',
                            array(
                                'class' => 'fa-solid fa-code-compare',
                                'title' => get_string('plagiarismscan', 'plagiarism_origai')
                            )
                        ) .
                        html_writer::end_tag('i'),
                        array('class' => 'origai-getscan-button')
                    ) .
                    "</div>";
                $ai_scanurl = "$CFG->wwwroot/plagiarism/origai/scan_content.php" .
                    "?scanid=$ai_scanid&cmid=$cmid&itemid=$itemid&userid=$userid&coursemodule=$coursemodule->modname&scantype=ai";
                $output .= "<div class='origai-getscan-button'>" .
                    html_writer::link(
                        "$ai_scanurl",
                        html_writer::start_tag(
                            'i',
                            array(
                                'class' => 'fa-solid fa-robot',
                                'title' => get_string('aiscan', 'plagiarism_origai')
                            )
                        ) .
                        html_writer::end_tag('i'),
                        array('class' => 'origai-getscan-button')
                    ) .
                    "</div>";
            }

            //generate links for both scan types.
            if (count($responses) == 2) {
                $output = "";
                foreach ($responses as $response) {
                    if ($response->content != $content) {
                        $response->success = false;
                        $response->content = $content;
                        $DB->update_record('plagiarism_origai_plagscan', $response);
                    }
                    if (!isset($response->success)) {
                        $scanurl = "$CFG->wwwroot/plagiarism/origai/scan_content.php" .
                            "?scanid=$response->id&cmid=$cmid&itemid=$itemid&userid=$userid&coursemodule=$coursemodule->modname&scantype=" . $response->scan_type;
                        $output .= "<div class='origai-getscan-button'>" .
                            html_writer::link(
                                "$scanurl",
                                html_writer::start_tag(
                                    'i',
                                    array(
                                        'class' => $response->scan_type == "plagiarism" ? 'fa-solid fa-code-compare' : 'fa-solid fa-robot',
                                        'title' => get_string($response->scan_type == "plagiarism" ? 'plagiarismscan' : 'aiscan', 'plagiarism_origai')
                                    )
                                ) .
                                html_writer::end_tag('i'),
                                array('class' => 'origai-getscan-button')
                            ) .
                            "</div>";
                    }
                    if (isset($response->success) && $response->scan_type == "plagiarism") {
                        //if response exists, show the total text score along with link to the report
                        if ($response && $response->success == true) {
                            $reporturl = "$CFG->wwwroot/plagiarism/origai/plagiarism_origai_report.php" .
                                "?scanid=$response->id&cmid=$cmid&itemid=$itemid&userid=$userid&modulename=$coursemodule->modname&scantype=plagiarism";
                            $output .= "<div class='origai-getscan-button'>" .
                                html_writer::link(
                                    "$reporturl",
                                    get_string('matchpercentage', 'plagiarism_origai') . round((float) $response->total_text_score) . "%",
                                    array('class' => 'origai-getscan-button')
                                ) .
                                "</div>";
                        }
                    } else if (isset($response->success) && $response->scan_type == "ai") {
                        if ($response && $response->success == true) {
                            $reporturl = "$CFG->wwwroot/plagiarism/origai/plagiarism_origai_report.php" .
                                "?scanid=$response->id&cmid=$cmid&itemid=$itemid&userid=$userid&modulename=$coursemodule->modname&scantype=ai";
                            $output .= "<div class='origai-getscan-button'>" .
                                html_writer::link(
                                    "$reporturl",
                                    get_string('aipercentage', 'plagiarism_origai') . round((float) $response->ai_score * 100) . '%',
                                    array('class' => 'origai-getscan-button')
                                ) .
                                "</div>";
                        }
                    }
                }
            }
        }
        return $output;
    }
}

function plagiarism_origai_is_plugin_configured($modulename)
{
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


function plagiarism_origai_coursemodule_standard_elements($formwrapper, $mform)
{
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

        $cmid = optional_param('update', null, PARAM_INT);
        $savedvalues = $DB->get_records_menu('plagiarism_origai_config', array('cm' => $cmid), '', 'name,value');
        if (count($savedvalues) > 0) {
            $mform->setDefault(
                'plagiarism_origai_enable',
                isset($savedvalues['plagiarism_origai_enable']) ? $savedvalues['plagiarism_origai_enable'] : 0
            );
        } else {
            $mform->setDefault('plagiarism_origai_enable', false);
        }
    }
}

function plagiarism_origai_coursemodule_edit_post_actions($data, $course)
{
    global $DB;

    if (empty($data->modulename)) {
        return $data;
    }

    // Check if plagiarism plugin is enabled for this module
    if (!plagiarism_origai_is_plugin_configured('mod_' . $data->modulename)) {
        return $data;
    }

    $savedrecord = $DB->get_record('plagiarism_origai_config', array('cm' => $data->coursemodule));
    if (!$savedrecord) {
        $mod_config = new stdClass();
        $mod_config->cm = $data->coursemodule;
        $mod_config->name = 'plagiarism_origai_enable';
        $mod_config->value = $data->plagiarism_origai_enable;
        //insert a record
        $DB->insert_record('plagiarism_origai_config', $mod_config);
    } else {
        //update existing record
        $savedrecord->value = $data->plagiarism_origai_enable;
        $DB->update_record('plagiarism_origai_config', $savedrecord);
    }
    return $data;
}
