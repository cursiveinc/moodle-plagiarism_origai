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

class plagiarism_plugin_originalityai extends plagiarism_plugin
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
        $content = $linkarray['content'];
        $itemid = $linkarray['itemid'];

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

        // Get OriginalityAI plugin admin config.
        static $adminconfig;
        if (empty($adminconfig)) {
            $adminconfig = get_config('plagiarism_originalityai');
        }

        static $context;
        $context = context_course::instance($coursemodule->course);

        // Check current user if instructor.
        static $isinstructor;
        if (empty($isinstructor)) {
            $isinstructor = has_capability('mod/assign:grade', $context);
        }

        if (!empty($linkarray["cmid"] && (!empty($linkarray["content"]))) && $isinstructor) {

            if (!is_plugin_configured("mod_" . $coursemodule->modname)) {
                return;
            }

            if ($coursemodule->modname == 'forum' || $coursemodule->modname == 'assign') {
                $enabled = $DB->get_record('plagiarism_origai_config', array('cm' => $cmid));
                if (!$enabled->value) {
                    return;
                }
            }

            //check whether record exists in scan table
            $response = $DB->get_record('plagiarism_origai_plagscan', array(
                'cmid' => $cmid, 'userid' => $userid,
                'itemid' => $itemid
            ));

            //if response exists, show the total text score along with link to the report
            if ($response && $response->success == true) {
                $reporturl = "$CFG->wwwroot/plagiarism/originalityai/plagiarism_originalityai_report.php" .
                    "?cmid=$cmid&itemid=$itemid&userid=$userid&modulename=$coursemodule->modname";
                $output = "<div class='originalityai-getscan-button'>" .
                    html_writer::link(
                        "$reporturl",
                        get_string('matchpercentage', 'plagiarism_originalityai') . $response->total_text_score,
                        array('class' => 'originalityai-getscan-button')
                    )
                    .
                    "</div>";
            }

            //if response not exists or response is false or null, show link button to initiate plagiarism scan
            else {
                //user resubmitted assignment, update existing record into plagiarism_origai_plagscan table and reset the
                //text score 
                if ($response && $response->content != $content) {
                    $response->success = false;
                    $response->content = $content;
                    $DB->update_record('plagiarism_origai_plagscan', $response);
                } else if (!$response) {
                    $respObj = new stdClass();
                    $respObj->userid = $userid;
                    $respObj->cmid = $cmid;
                    $respObj->content = $content;
                    $respObj->itemid = $itemid;
                    $DB->insert_record('plagiarism_origai_plagscan', $respObj);
                }

                $getscanurl = "$CFG->wwwroot/plagiarism/originalityai/scan_content.php" .
                    "?cmid=$cmid&itemid=$itemid&userid=$userid&coursemodule=$coursemodule->modname";
                $output = "<div class='originalityai-getscan-button'>" .
                    html_writer::link(
                        "$getscanurl",
                        get_string('plagiarismscan', 'plagiarism_originalityai'),
                        array('class' => 'originalityai-getscan-button')
                    )
                    .
                    "</div>";
            }
        }


        return $output;
    }

    /**
     * hook to add plagiarism specific settings to a module settings page
     * @param object $mform  - Moodle form
     * @param object $context - current context
     */
    public function get_form_elements_module($mform, $context, $modulename = '')
    {
        //Add elements to form using standard mform like:
        //$mform->addElement('hidden', $element);
        //$mform->disabledIf('plagiarism_draft_submit', 'var4', 'eq', 0);
        global $DB, $CFG;

        if (has_capability('plagiarism/originalityai:enable', $context)) {

            // Return no form if the plugin isn't configured or not enabled.
            if (!is_plugin_configured($modulename)) {
                return;
            }

            $mform->addElement(
                'header',
                'plagiarism_originalityai_defaultsettings',
                get_string('origaicoursesettings', 'plagiarism_originalityai')
            );

            $mform->addElement(
                'advcheckbox',
                'plagiarism_originalityai_enable',
                get_string('originalityaienable', 'plagiarism_originalityai')
            );

            $cmid = optional_param('update', null, PARAM_INT);
            $savedvalues = $DB->get_records_menu('plagiarism_origai_config', array('cm' => $cmid), '', 'name,value');
            if (count($savedvalues) > 0) {
                $mform->setDefault(
                    'plagiarism_originalityai_enable',
                    isset($savedvalues['plagiarism_originalityai_enable']) ? $savedvalues['plagiarism_originalityai_enable'] : 0
                );
            } else {
                $mform->setDefault('plagiarism_originalityai_enable', false);
            }
        }
    }

    /* hook to save plagiarism specific settings on a module settings page
     * @param object $data - data from an mform submission.
    */
    public function save_form_elements($data)
    {
        global $DB;

        // Return no form if the plugin isn't configured or not enabled.
        if (empty($data->modulename) && is_plugin_configured('mod_' . $data->modulename)) {
            return;
        }

        $savedrecord = $DB->get_record('plagiarism_origai_config', array('cm' => $data->coursemodule));
        if (!$savedrecord) {
            $mod_config = new stdClass();
            $mod_config->cm = $data->coursemodule;
            $mod_config->name = 'plagiarism_originalityai_enable';
            $mod_config->value = 0;
            //insert a record
            $DB->insert_record('plagiarism_origai_config', $mod_config);
        } else {
            //update existing record
            $savedrecord->value = $data->plagiarism_originalityai_enable;
            $DB->update_record('plagiarism_origai_config', $savedrecord);
        }
    }

    /**
     * hook to allow a disclosure to be printed notifying users what will happen with their submission
     * @param int $cmid - course module id
     * @return string
     */
    public function print_disclosure($cmid)
    {
        global $OUTPUT;
        $plagiarismsettings = (array)get_config('plagiarism');
        //TODO: check if this cmid has plagiarism enabled.
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        echo format_text($plagiarismsettings['originalityai_student_disclosure'], FORMAT_MOODLE, $formatoptions);
        echo $OUTPUT->box_end();
    }

    /**
     * hook to allow status of submitted files to be updated - called on grading/report pages.
     *
     * @param object $course - full Course object
     * @param object $cm - full cm object
     */
    public function update_status($course, $cm)
    {
        //called at top of submissions/grading pages - allows printing of admin style links or updating status
    }

    /**
     * called by admin/cron.php 
     *
     */
    public function cron()
    {
        //do any scheduled task stuff
    }
}

function is_plugin_configured($modulename)
{
    $apikey = get_config('plagiarism_originalityai', 'apikey');
    $apiurl = get_config('plagiarism_originalityai', 'apiurl');


    if (empty($apikey) || empty($apiurl)) {
        return false;
    }

    $moduleconfigname = 'plagiarism_originalityai_' . $modulename;
    $moduleenabled = get_config('plagiarism_originalityai', $moduleconfigname);
    if (!$moduleenabled) {
        return false;
    }

    return true;
}


function plagiarism_originalityai_coursemodule_standard_elements($formwrapper, $mform)
{
    global $DB;
    $context = context_course::instance($formwrapper->get_course()->id);
    $modulename = $formwrapper->get_current()->modulename;
    if (!$context || !isset($modulename)) {
        return;
    }
    if (has_capability('plagiarism/originalityai:enable', $context)) {

        // Return no form if the plugin isn't configured or not enabled.
        if (!is_plugin_configured("mod_".$modulename)) {
            return;
        }

        $mform->addElement(
            'header',
            'plagiarism_originalityai_defaultsettings',
            get_string('origaicoursesettings', 'plagiarism_originalityai')
        );

        $mform->addElement(
            'advcheckbox',
            'plagiarism_originalityai_enable',
            get_string('originalityaienable', 'plagiarism_originalityai')
        );

        $cmid = optional_param('update', null, PARAM_INT);
        $savedvalues = $DB->get_records_menu('plagiarism_origai_config', array('cm' => $cmid), '', 'name,value');
        if (count($savedvalues) > 0) {
            $mform->setDefault(
                'plagiarism_originalityai_enable',
                isset($savedvalues['plagiarism_originalityai_enable']) ? $savedvalues['plagiarism_originalityai_enable'] : 0
            );
        } else {
            $mform->setDefault('plagiarism_originalityai_enable', false);
        }
    }
}

function plagiarism_originalityai_coursemodule_edit_post_actions($data, $course)
{
    global $DB;

        // Return no form if the plugin isn't configured or not enabled.
        if (empty($data->modulename) && is_plugin_configured('mod_' . $data->modulename)) {
            return;
        }

        $savedrecord = $DB->get_record('plagiarism_origai_config', array('cm' => $data->coursemodule));
        if (!$savedrecord) {
            $mod_config = new stdClass();
            $mod_config->cm = $data->coursemodule;
            $mod_config->name = 'plagiarism_originalityai_enable';
            $mod_config->value = 0;
            //insert a record
            $DB->insert_record('plagiarism_origai_config', $mod_config);
        } else {
            //update existing record
            $savedrecord->value = $data->plagiarism_originalityai_enable;
            $DB->update_record('plagiarism_origai_config', $savedrecord);
        }
        return $data;
}
