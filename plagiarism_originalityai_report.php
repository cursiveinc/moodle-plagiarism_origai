<?php
require(dirname(dirname(__FILE__)) . '/../config.php');
require_once('../originalityai/lib.php');

// Get url params.
$cmid = required_param('cmid', PARAM_INT);
$itemid = optional_param('itemid',0,PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$modulename = required_param('modulename', PARAM_TEXT);
if(!$itemid){
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

$PAGE->set_url('/moodle/plagiarism/originalityai/plagiarism_originalityai_report.php', array(
    'cmid' => $cmid,
    'itemid' =>$itemid,
    'userid' => $userid,
    'modulename' => $modulename
));

// Setup page title and header.
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
$pagetitle = get_string('reportpagetitle', 'plagiarism_originalityai') . ' - ' . fullname($user);

$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();

$modulesettings = $DB->get_records_menu('plagiarism_origai_config', array('cm' => $cmid), '', 'name,value');

$isinstructor = has_capability('mod/assign:grade', $context);

$moduleenabled = is_plugin_configured('mod_' . $cm->modname);


if ($modulename != "quiz" && !$moduleenabled) {
    echo html_writer::div(get_string('disabledformodule', 'plagiarism_originalityai'));
} else {

    $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));
    $scanresult = $DB->get_record('plagiarism_origai_plagscan', array('cmid' => $cmid, 'itemid'=>$itemid, 'userid' => $userid));
    $matches = $DB->get_records('plagiarism_origai_match', array('scanid' => $scanresult->id));

    // Proceed to displaying the report.
    if ($isinstructor) {
        echo html_writer::start_tag("h3");
        echo "Status = ";
        echo ($scanresult->success == 1) ? "Success" : "Fail";
        echo html_writer::end_tag("h3");

        echo html_writer::start_tag("div", array("class" => "mb-2"));
        echo html_writer::start_tag("div");

        //if status is fail, show error message else show full report
        if ($scanresult->success == 0) {
            echo $scanresult->error;
        } else {
            echo html_writer::start_tag("strong");
            echo html_writer::start_tag("span");
            echo "Flesch Grade Level Score: ";
            echo html_writer::end_tag("span");
            echo html_writer::end_tag("strong");

            echo html_writer::start_tag("span");
            echo $scanresult->flesch_grade_level . " |&nbsp;";
            echo html_writer::end_tag("span");

            echo html_writer::tag("a", "Full Report Link", array("href" => $scanresult->public_link, 'target' => "_blank"));

            echo html_writer::end_tag("div");

            echo html_writer::start_tag("div");

            echo html_writer::start_tag("strong");
            echo html_writer::start_tag("span");
            echo "Match Percentage: ";
            echo html_writer::end_tag("span");
            echo html_writer::end_tag("strong");

            echo html_writer::start_tag("span");
            echo $scanresult->total_text_score . " |&nbsp;";
            echo html_writer::end_tag("span");

            echo html_writer::start_tag("strong");
            echo html_writer::start_tag("span");
            echo "Total Matches: ";
            echo html_writer::end_tag("span");
            echo html_writer::end_tag("strong");

            echo html_writer::start_tag("span");
            echo $scanresult->sources;
            echo html_writer::end_tag("span");

            echo html_writer::end_tag("div");
            echo html_writer::end_tag("div");

            echo html_writer::tag("h3", "Match Information");

            foreach ($matches as $match) {
                echo html_writer::start_tag("div", array('class' => 'mb-2'));

                echo html_writer::start_tag("div");

                echo html_writer::start_tag("strong");
                echo html_writer::start_tag("span");
                echo "Score: ";
                echo html_writer::end_tag("span");
                echo html_writer::end_tag("strong");

                echo html_writer::start_tag("span");
                echo $match->score;
                echo html_writer::end_tag("span");

                echo html_writer::end_tag("div");

                echo html_writer::start_tag("div");

                echo html_writer::start_tag("strong");
                echo html_writer::start_tag("span");
                echo "Phrase: ";
                echo html_writer::end_tag("span");
                echo html_writer::end_tag("strong");

                echo html_writer::start_tag("span");
                echo $match->ptext;
                echo html_writer::end_tag("span");

                echo html_writer::end_tag("div");

                echo html_writer::start_tag("div");

                echo html_writer::start_tag("strong");
                echo html_writer::start_tag("span");
                echo "Website: ";
                echo html_writer::end_tag("span");
                echo html_writer::end_tag("strong");

                echo html_writer::start_tag("span");
                echo html_writer::tag("a", $match->website, array("href" => $match->website, "target" => "_blank"));
                echo html_writer::end_tag("span");

                echo html_writer::end_tag("div");

                echo html_writer::end_tag("div");
            }

            echo html_writer::end_tag("div");
        }
    } else {
        echo html_writer::div(get_string('nopageaccess', 'plagiarism_originalityai'), null, array('style' => $errormessagestyle));
    }
}
echo $OUTPUT->footer();
