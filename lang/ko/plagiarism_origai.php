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
 * Language strings for plagiarism_origai.
 *
 * @package    plagiarism_origai
 * @category   string
 * @copyright  Created by Brickfield Translator https://www.brickfield.ie/translator/
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'OriginalityAI 표절 플러그인';
$string['accountconfig'] = '계정 구성';
$string['adminconfig'] = 'OriginalityAI 표절 플러그인 구성';
$string['adminconfigdesc'] = 'Originality.AI 플러그인은 AI가 생성한 콘텐츠와 표절을 모두 감지하는 안정적이고 포괄적인 솔루션을 제공하여 교육자와 학생이 작업의 진위를 확인할 수 있도록 도와줍니다.<br />설정 지침 및 자세한 사용 방법은 <a target="_blank" href="https://docs.google.com/document/d/1KzYqrmDeGbTCO-JlO2zDSV-cXuVUyeTH03F0buzufyw/edit" rel="noreferrer noopener">가이드를</a> 참조하세요.<br /><br /><br />';
$string['adminconfigsavesuccess'] = 'OriginalityAI 표절 플러그인 설정 저장됨';
$string['aiModel'] = 'AI 모델';
$string['aiModel_help'] = '최적화된 영어 콘텐츠를 위한 영어 또는 여러 언어를 지원하는 멀티랭귀지를 위한 멀티랭귀지.';
$string['aipercentage'] = '{$a} 유사 AI';
$string['airesulttitle'] = 'AI 확인';
$string['aiscan'] = 'OriginalityAI의 AI 스캔';
$string['allowstudentreportaccess'] = '학생의 보고서 보기 접근 허용';
$string['apiconnectionerror'] = 'API 연결에 실패했습니다. 계정 설정을 확인해 주세요.';
$string['apikey'] = 'OriginalityAI API 키';
$string['apikeyhelp'] = 'API 키가 없는 경우 <a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">여기를 클릭하여 직접 등록하세요</a>.';
$string['apikeyrequired'] = 'OriginalityAI API 키가 필요합니다.';
$string['apiurl'] = 'OriginalityAI API 기본 URL';
$string['apiurlchanged'] = '스캔이 원활하게 실행되려면 API URL을 {$a}로 업데이트해야 합니다.';
$string['apiurlrequired'] = 'OriginalityAI API의 기본 URL이 필요합니다.';
$string['assign'] = '과제';
$string['classifierinfo'] = '정보 분류:';
$string['defaultscanerror'] = '스캔 실패';
$string['defaultsettings'] = '기본 설정';
$string['disabledformodule'] = '이 모듈에 대해 OriginalityAI 플러그인이 비활성화되었습니다.';
$string['enableautomatedscan'] = '제출 시 자동 스캔 사용';
$string['enableautomatedscan_help'] = '활동 제출이 제출된 후 자동으로 스캔합니다. 수동으로 스캔하려면 확인란을 선택 취소합니다.';
$string['enablemodule'] = 'a}에 플러그인 사용';
$string['failmsg'] = '실패';
$string['fake'] = '가짜';
$string['fileattachmentnotsupported'] = '파일 첨부 유형이 지원되지 않음';
$string['fleschscore'] = '플레쉬 학년 수준 점수:';
$string['fullreportlink'] = '전체 보고서 링크';
$string['fullscreenview'] = '전체 화면으로 보기';
$string['forum'] = '포럼';
$string['humanpercentage'] = '{$a} 유사 원본';
$string['inserterror'] = '데이터베이스에 레코드를 삽입하는 동안 오류가 발생했습니다.';
$string['lite'] = '영어';
$string['matchinfo'] = '경기 정보';
$string['matchpercentage'] = '일치 %:';
$string['multilang'] = '멀티 랭';
$string['nopageaccess'] = '이 페이지에 대한 액세스 거부';
$string['origai'] = 'OriginalityAI 표절 플러그인';
$string['origaicoursesettings'] = '독창성AI 설정';
$string['origaienable'] = '독창성 AI 활성화';
$string['original'] = '원본';
$string['phrase'] = '문구:';
$string['plagiarismscan'] = 'Originality.AI의 표절 검사';
$string['plagresulttitle'] = '경기 점수';
$string['privacy:metadata:plagiarism_origai_client'] = '표절 보고서를 생성하려면 제출 데이터를 OriginalityAI와 교환해야 합니다.';
$string['privacy:metadata:plagiarism_origai_client:submission_content'] = '제출된 콘텐츠는 표절 신고를 위해 OriginalityAI로 전송됩니다.';
$string['privacy:metadata:plagiarism_origai_files'] = '무들에서 제출된 제출물을 OriginalityAI에서 생성된 표절 결과와 연결하는 정보입니다.';
$string['privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel'] = '제출물의 육체 등급 수준입니다.';
$string['privacy:metadata:plagiarism_origai_plagscan:totaltextscore'] = '제출물의 총 텍스트 점수입니다.';
$string['privacy:metadata:plagiarism_origai_plagscan:updatedtime'] = '사용자가 제출한 문서가 마지막으로 수정된 시점을 나타내는 타임스탬프입니다.';
$string['privacy:metadata:plagiarism_origai_plagscan:userid'] = '제출한 사용자의 ID입니다.';
$string['assign'] = '과제';
$string['real'] = '실제';
$string['reportpagetitle'] = '독창성 AI 스캔 보고서';
$string['retryscan'] = '스캔 다시 시도';
$string['runaicheck'] = 'AI 확인';
$string['runplagiarismcheck'] = '표절 확인';
$string['scanfailed'] = '스캔 실패';
$string['scaninprogress'] = '스캔이 진행 중입니다.';
$string['scanqueued'] = '대기 중인 스캔';
$string['scanqueuednotification'] = '스캔이 성공적으로 대기열에 추가되었습니다. 곧 처리가 시작됩니다.';
$string['scanreportfailed'] = '스캔 보고서를 받을 수 없음';
$string['score'] = '점수:';
$string['sendqueuedsubmissionstaskname'] = 'OriginalityAI 플러그인 - 대기열에 있는 파일 처리';
$string['standard'] = '표준';
$string['status'] = '상태 =';
$string['studentdisclosure'] = '학생 공개';
$string['studentdisclosure_help'] = '이 텍스트는 파일 업로드 페이지에서 모든 학생에게 표시됩니다.';
$string['studentdisclosuredefault'] = '제출된 모든 콘텐츠는 표절 및 AI 탐지를 위해 통과됩니다.';
$string['successmsg'] = '성공';
$string['textextractionfailed'] = '텍스트 추출에 실패했습니다.';
$string['totalmatches'] = '총 경기 수:';
$string['updateerror'] = '데이터베이스에서 레코드를 업데이트하는 동안 오류가 발생했습니다.';
$string['useorigai'] = '독창성 AI 활성화';
$string['website'] = '웹사이트:';
