<?php

require_once('../../config.php');

$cmid = required_param('cmid', PARAM_INT);
$itemid = optional_param('itemid',0,PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$modulename = required_param('coursemodule', PARAM_TEXT);
if(!$itemid){
    $itemid = null;
}

global $DB;

require_login();
$coursemodule = get_coursemodule_from_id('',    $cmid);
$context = context_course::instance($coursemodule->course);

//Check if user has capability
if (!has_capability('mod/assign:grade', $context)) {
    return;
}

$apikey = get_config('plagiarism_originalityai', 'apikey');
$apibaseurl = get_config('plagiarism_originalityai', 'apiurl');
$apiurl = $apibaseurl . '/scan/plag';

if (empty($apikey) || empty($apiurl)) {
    //redirect to grade/submission page with message that plugin is not configured.
}
$curl = curl_init();
//fetch content from the database
$recordObj = $DB->get_record('plagiarism_origai_plagscan', array('cmid' => $cmid, 'userid' => $userid,'itemid'=>$itemid));
if (isset($recordObj->content)) {
    $request_string = json_encode(
        array(
            'content' =>html_to_text($recordObj->content,0),
            'storeScan' => "\"false\""
        )
    );
    curl_setopt_array($curl, array(
        CURLOPT_URL => $apiurl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $request_string,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'X-OAI-API-KEY: ' . $apikey,
            'Content-Type: application/json'
        ),
    ));

    curl_setopt($curl, CURLOPT_VERBOSE, true);
    $response = curl_exec($curl);

    $responseObj = json_decode($response);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($httpcode == 200) {
        $recordObj->success = $responseObj->success;
        $recordObj->public_link = $responseObj->public_link;
        $recordObj->total_text_score = $responseObj->total_text_score;
        $recordObj->sources = $responseObj->sources;
        $recordObj->flesch_grade_level = $responseObj->readability->readability->fleschGradeLevel;
        $recordObj->update_time = date('Y-m-d H:i:s');
        $DB->update_record('plagiarism_origai_plagscan', $recordObj);
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
    } else if ($httpcode == 422 || $httpcode == 500) {
        $recordObj->success = false;
        $recordObj->update_time = date('Y-m-d H:i:s');
        if($httpcode == 422){
            $recordObj->error = $responseObj->error;
        }
        else{
            $recordObj->error = $responseObj->message;
        }
        $DB->update_record('plagiarism_origai_plagscan', $recordObj);
    }

    curl_close($curl);
    $url = new moodle_url('/plagiarism/originalityai/plagiarism_originalityai_report.php', array('cmid' => $cmid, 'itemid'=>$itemid, 'userid'=>$userid,'modulename'=>$modulename));
    redirect($url);
}
