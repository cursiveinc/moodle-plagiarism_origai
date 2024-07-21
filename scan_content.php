<?php

require_once ('../../config.php');
require_once ($CFG->libdir . '/filelib.php');

$cmid = required_param('cmid', PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$modulename = required_param('coursemodule', PARAM_TEXT);
$scantype = required_param('scantype', PARAM_TEXT);

if (!$itemid) {
    $itemid = null;
}

global $DB;

require_login();
$coursemodule = get_coursemodule_from_id('', $cmid);
$context = context_course::instance($coursemodule->course);

require_capability('mod/assign:grade', $context);

$apikey = get_config('plagiarism_origai', 'apikey');
$apibaseurl = get_config('plagiarism_origai', 'apiurl');
$apiurl = $apibaseurl . ($scantype == "plagiarism" ? '/scan/plag' : '/scan/ai');

if (empty($apikey) || empty($apiurl)) {
    //redirect to grade/submission page with message that plugin is not configured.
}

$c = new curl();
$c->setHeader(['Accept: application/json']);
$c->setHeader(['X-OAI-API-KEY: ' . $apikey]);
$c->setHeader(['Content-Type: application/json']);

// //fetch content from the database
$recordObj = $DB->get_record('plagiarism_origai_plagscan', array('cmid' => $cmid, 'userid' => $userid, 'itemid' => $itemid, 'scan_type' => $scantype));
if (isset($recordObj->content)) {
    $request_string = json_encode(
        array(
            'content' => html_to_text($recordObj->content, 0),
            'storeScan' => "\"false\""
        )
    );
    $response = $c->post($apiurl, $request_string);
    $responseObj = json_decode($response);
    $info = $c->get_info();
    $httpcode = $info['http_code'];

    if ($httpcode == 200) {
        $recordObj->success = $responseObj->success;
        $recordObj->public_link = $responseObj->public_link;
        if ($scantype == "plagiarism") {
            $recordObj->total_text_score = $responseObj->total_text_score;
            $recordObj->sources = $responseObj->sources;
            $recordObj->flesch_grade_level = $responseObj->readability->readability->fleschGradeLevel;
        } else if ($scantype == "ai") {
            $recordObj->original_score = $responseObj->score->original;
            $recordObj->ai_score = $responseObj->score->ai;
        }
        $recordObj->update_time = date('Y-m-d H:i:s');
        $DB->update_record('plagiarism_origai_plagscan', $recordObj);
        if ($scantype == "plagiarism") {
            if (count($responseObj->results) > 0) {
                foreach ($responseObj->results as $result) {
                    if (count($result->matches) > 0) {
                        $matches = array();
                        foreach ($result->matches as $match) {
                            $matchObj = new stdClass();
                            $matchObj->scanid = $recordObj->id;
                            $matchObj->website = $match->website;
                            $matchObj->score = $match->score;
                            $matchObj->ptext = $match->pText;
                            array_push($matches, $matchObj);
                        }
                        $DB->insert_records('plagiarism_origai_match', $matches);
                    }
                }
            }
        } else if ($scantype == "ai") {
            if (count($responseObj->blocks) > 0) {
                $blocks = array();
                foreach ($responseObj->blocks as $block) {
                    $blockObj = new stdClass();
                    $blockObj->scanid = $recordObj->id;
                    $blockObj->fakescore = $block->result->fake;
                    $blockObj->realscore = $block->result->real;
                    $blockObj->ptext = $block->text;
                    array_push($blocks, $blockObj);
                }
                $DB->insert_records('plagiarism_origai_match', $blocks);
            }
        }
    } else if ($httpcode == 422 || $httpcode == 500) {
        $recordObj->success = false;
        $recordObj->update_time = date('Y-m-d H:i:s');
        if ($httpcode == 422) {
            $recordObj->error = $responseObj->error;
        } else {
            $recordObj->error = $responseObj->message;
        }
        $DB->update_record('plagiarism_origai_plagscan', $recordObj);
    }

    $url = new moodle_url('/plagiarism/origai/plagiarism_origai_report.php', array('cmid' => $cmid, 'itemid' => $itemid, 'userid' => $userid, 'modulename' => $modulename, 'scantype' => $scantype));
    redirect($url);
}
