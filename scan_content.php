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
$apiurl = get_config('plagiarism_origai', 'apiurl').'/scan';
$aimodel = get_config('plagiarism_origai', 'aiModel');

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
            'storeScan' => false,
            'aiModel' => $aimodel,
            'scan_ai' => ($scantype == 'ai')? true: false,
            'scan_plag' => ($scantype == 'plagiarism')? true: false,
            'scan_readability' => true,
            'scan_grammar_spelling' => true
        )
    );
    $response = $c->post($apiurl, params: $request_string);
    $responseObj = json_decode($response);
    $info = $c->get_info();
    $httpcode = $info['http_code'];

    if ($httpcode == 200) {
        $recordObj->success = true;
        $recordObj->public_link = $responseObj->properties->public_link;
        if ($scantype == "plagiarism" && $responseObj->plagiarism!==null) {
            $recordObj->total_text_score = $responseObj->plagiarism->score;
            $recordObj->sources = count($responseObj->plagiarism->results);
            $recordObj->flesch_grade_level = $responseObj->readability->readability->fleschGradeLevel;
        } else if ($scantype == "ai") {
            $recordObj->original_score = $responseObj->ai->confidence->Original;
            $recordObj->ai_score = $responseObj->ai->confidence->AI;
        }
        $recordObj->update_time = date('Y-m-d H:i:s');
        $DB->update_record('plagiarism_origai_plagscan', $recordObj);
        if ($scantype == "plagiarism") {
            if (isset($responseObj->plagiarism->results) && is_array($responseObj->plagiarism->results)) {
                foreach ($responseObj->plagiarism->results as $result) {
                    $resultArray = isset($result->results) && is_array($result->results) ? $result->results : [];
                    $matches = [];
                    foreach ($resultArray as $match) {
                        $matchObj = new stdClass();
                        $matchObj->scanid = $recordObj->id;
                        $matchObj->website = $match->link;
                        $matchObj->score = $match->scores[0]->score;
                        $matchObj->ptext = $match->scores[0]->sentence;
                        array_push($matches, $matchObj);
                    }
                    if(!empty($matches)){
                        $DB->insert_records('plagiarism_origai_match', $matches);
                    }
                }
            }
        } else if ($scantype == "ai") {
            if (isset($responseObj->ai->blocks) && is_array($responseObj->ai->blocks)) {
                $blocks = [];
                foreach ($responseObj->ai->blocks as $block) {
                    $blockObj = new stdClass();
                    $blockObj->scanid = $recordObj->id;
                    $blockObj->fakescore = $block->result->fake;
                    $blockObj->realscore = $block->result->real;
                    $blockObj->ptext = $block->text;
                    array_push($blocks, $blockObj);
                }
                if(!empty($blocks)){
                    $DB->insert_records('plagiarism_origai_match', $blocks);
                }
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
