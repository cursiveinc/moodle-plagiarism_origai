<?php
require (dirname(dirname(__FILE__)) . '/../config.php');
require_once ('../origai/lib.php');

// Get url params.
$cmid = required_param('cmid', PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$modulename = required_param('modulename', PARAM_TEXT);
$scantype = required_param('scantype', PARAM_TEXT);

if (!$itemid) {
    $itemid = null;
}

// Get instance modules.
$cm = get_coursemodule_from_id($modulename, $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

// Request login.
require_login($course, true, $cm);

// Setup page meta data.
$context = context_course::instance($cm->course);
$PAGE->set_course($course);
$PAGE->set_cm($cm);
$PAGE->set_pagelayout('incourse');

$PAGE->set_url(
    '/moodle/plagiarism/origai/plagiarism_origai_report.php',
    array(
        'cmid' => $cmid,
        'itemid' => $itemid,
        'userid' => $userid,
        'modulename' => $modulename
    )
);

// Setup page title and header.
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
$pagetitle = get_string('reportpagetitle', 'plagiarism_origai') . ' - ' . fullname($user);

$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();

$modulesettings = $DB->get_records_menu('plagiarism_origai_config', array('cm' => $cmid), '', 'name,value');

require_capability('mod/assign:grade', $context);

$moduleenabled = plagiarism_origai_is_plugin_configured('mod_' . $cm->modname);


if ($modulename != "quiz" && !$moduleenabled) {
    echo html_writer::div(get_string('disabledformodule', 'plagiarism_origai'));
} else {

    $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));
    $scanresult = $DB->get_record('plagiarism_origai_plagscan', array('cmid' => $cmid, 'itemid' => $itemid, 'userid' => $userid, 'scan_type' => $scantype));
    $matches = $DB->get_records('plagiarism_origai_match', array('scanid' => $scanresult->id));


    echo html_writer::start_tag("h3");
    echo get_string("status", "plagiarism_origai");
    echo ($scanresult->success == 1) ? get_string("successmsg", "plagiarism_origai") : get_string("failmsg", "plagiarism_origai");
    echo html_writer::end_tag("h3");

    echo html_writer::start_tag("div", array("class" => "mb-2"));
    echo html_writer::start_tag("div");

    //if status is fail, show error message else show full report
    if ($scanresult->success == 0) {
        echo $scanresult->error;
    } else {
        if ($scantype == "plagiarism") {
            echo html_writer::start_tag("strong");
            echo html_writer::start_tag("span");
            echo get_string("fleschscore", "plagiarism_origai");
            echo html_writer::end_tag("span");
            echo html_writer::end_tag("strong");

            echo html_writer::start_tag("span");
            echo $scanresult->flesch_grade_level . " |&nbsp;";
            echo html_writer::end_tag("span");
        }

        echo html_writer::tag("a", get_string("fullreportlink", "plagiarism_origai"), array("href" => $scanresult->public_link, 'target' => "_blank"));
        echo html_writer::end_tag("div");


        if ($scantype == "ai") {
            echo html_writer::start_tag("div");

            echo html_writer::start_tag("strong");
            echo html_writer::start_tag("span");
            echo get_string("classifierinfo", "plagiarism_origai");
            echo html_writer::end_tag("span");
            echo html_writer::end_tag("strong");
            echo html_writer::start_tag("span");
            echo get_string("original", "plagiarism_origai") . " ";
            echo round((float) $scanresult->original_score * 100) . '%';
            echo " | ";
            echo get_string("ai", "plagiarism_origai") . " ";
            echo round((float) $scanresult->ai_score * 100) . '%';
            echo html_writer::end_tag("span");

            echo html_writer::end_tag("div");
            echo html_writer::end_tag("div");
        }
        

        if ($scantype == 'plagiarism') {
            echo html_writer::start_tag("div");

            echo html_writer::start_tag("strong");
            echo html_writer::start_tag("span");
            echo get_string("matchpercentage", "plagiarism_origai");
            echo html_writer::end_tag("span");
            echo html_writer::end_tag("strong");

            echo html_writer::start_tag("span");
            echo $scanresult->total_text_score . " |&nbsp;";
            echo html_writer::end_tag("span");

            echo html_writer::start_tag("strong");
            echo html_writer::start_tag("span");
            echo get_string("totalmatches", "plagiarism_origai");
            echo html_writer::end_tag("span");
            echo html_writer::end_tag("strong");

            echo html_writer::start_tag("span");
            echo $scanresult->sources;
            echo html_writer::end_tag("span");

            echo html_writer::end_tag("div");
            echo html_writer::end_tag("div");
        }

        echo html_writer::tag("h3", get_string("matchinfo", "plagiarism_origai"));

        if ($scantype == "plagiarism") {
            foreach ($matches as $match) {
                echo html_writer::start_tag("div", array('class' => 'mb-2'));

                echo html_writer::start_tag("div");

                echo html_writer::start_tag("strong");
                echo html_writer::start_tag("span");
                echo get_string("score", "plagiarism_origai");
                echo html_writer::end_tag("span");
                echo html_writer::end_tag("strong");

                echo html_writer::start_tag("span");
                echo $match->score;
                echo html_writer::end_tag("span");

                echo html_writer::end_tag("div");

                echo html_writer::start_tag("div");

                echo html_writer::start_tag("strong");
                echo html_writer::start_tag("span");
                echo get_string("phrase", "plagiarism_origai");
                echo html_writer::end_tag("span");
                echo html_writer::end_tag("strong");

                echo html_writer::start_tag("span");
                echo $match->ptext;
                echo html_writer::end_tag("span");

                echo html_writer::end_tag("div");

                echo html_writer::start_tag("div");

                echo html_writer::start_tag("strong");
                echo html_writer::start_tag("span");
                echo get_string("website", "plagiarism_origai");
                echo html_writer::end_tag("span");
                echo html_writer::end_tag("strong");

                echo html_writer::start_tag("span");
                echo html_writer::tag("a", $match->website, array("href" => $match->website, "target" => "_blank"));
                echo html_writer::end_tag("span");

                echo html_writer::end_tag("div");

                echo html_writer::end_tag("div");
            }
        } else if ($scantype == "ai") {
            foreach ($matches as $match) {

                echo html_writer::start_tag("div", array('class' => 'mb-2'));
                echo html_writer::start_tag("div");
                echo html_writer::start_tag("strong");
                echo html_writer::start_tag("span");
                echo get_string("phrase", "plagiarism_origai");
                echo html_writer::end_tag("span");
                echo html_writer::end_tag("strong");
                echo html_writer::start_tag("span");
                echo $match->ptext;
                echo html_writer::end_tag("span");

                echo html_writer::end_tag("div");

                echo html_writer::start_tag("div");

                echo html_writer::start_tag("strong");
                echo html_writer::start_tag("span");
                echo get_string("ai", "plagiarism_origai") . ": ";
                echo html_writer::end_tag("span");
                echo html_writer::end_tag("strong");
                echo html_writer::start_tag("span");
                echo get_string("fake", "plagiarism_origai") . " ";
                echo round((float) $match->fakescore * 100) . '%';
                echo " | ";
                echo get_string("real", "plagiarism_origai") . " ";
                echo round((float) $match->realscore * 100) . '%';
                echo html_writer::end_tag("span");

                echo html_writer::end_tag("div");
                echo html_writer::end_tag("div");
            }
        }
        echo html_writer::end_tag("div");
    }

}
echo $OUTPUT->footer();
