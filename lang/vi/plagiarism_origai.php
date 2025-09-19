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

$string['pluginname'] = 'Plugin phát hiện đạo văn OriginalityAI';
$string['accountconfig'] = 'Cấu hình tài khoản';
$string['adminconfig'] = 'Cấu hình plugin phát hiện đạo văn OriginalityAI';
$string['adminconfigdesc'] = 'Plugin Originality.AI cung cấp một giải pháp đáng tin cậy và toàn diện để phát hiện nội dung do AI tạo ra và đạo văn, giúp giáo viên và học sinh đảm bảo tính xác thực của công việc của họ.<br />Để biết hướng dẫn cài đặt và chi tiết sử dụng, vui lòng tham khảo <a target="_blank" href="https://docs.google.com/document/d/1KzYqrmDeGbTCO-JlO2zDSV-cXuVUyeTH03F0buzufyw/edit" rel="noreferrer noopener">hướng dẫn của chúng tôi</a>.<br /><br /><br />';
$string['adminconfigsavesuccess'] = 'Cài đặt plugin phát hiện đạo văn OriginalityAI đã được lưu';
$string['ai'] = 'Trí tuệ nhân tạo';
$string['aiModel'] = 'Mô hình Trí tuệ Nhân tạo';
$string['aiModel_help'] = 'Tiếng Anh cho nội dung tiếng Anh tối ưu hóa hoặc Multi-lang để hỗ trợ nhiều ngôn ngữ.';
$string['aipercentage'] = '{$a} Có khả năng là Trí tuệ nhân tạo (AI)';
$string['airesulttitle'] = 'Kiểm tra AI';
$string['aiscan'] = 'Quét AI từ OriginalityAI';
$string['allowstudentreportaccess'] = 'Cho phép học sinh truy cập để xem báo cáo.';
$string['apiconnectionerror'] = 'Kết nối API đã thất bại. Vui lòng kiểm tra lại cài đặt tài khoản.';
$string['apikey'] = 'Khóa API OriginalityAI';
$string['apikeyhelp'] = 'Nếu bạn chưa có khóa API, vui lòng <a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">nhấp vào đây để đăng ký khóa API của riêng bạn.</a>';
$string['apikeyrequired'] = 'Khóa API OriginalityAI là bắt buộc.';
$string['apiurl'] = 'Đường dẫn cơ sở của API OriginalityAI';
$string['apiurlchanged'] = 'Địa chỉ URL API phải được cập nhật thành {$a} để quá trình quét diễn ra trơn tru.';
$string['apiurlrequired'] = 'Địa chỉ URL gốc của API OriginalityAI là bắt buộc.';
$string['assign'] = 'Bài tập';
$string['classifierinfo'] = 'Thông tin phân loại:';
$string['defaultscanerror'] = 'Quét không thành công';
$string['defaultsettings'] = 'Cài đặt mặc định';
$string['disabledformodule'] = 'Plugin OriginalityAI đã bị vô hiệu hóa cho mô-đun này.';
$string['enableautomatedscan'] = 'Bật tính năng quét tự động khi gửi.';
$string['enableautomatedscan_help'] = 'Tự động quét các bản nộp hoạt động sau khi chúng được nộp. Bỏ chọn để quét thủ công.';
$string['enablemodule'] = 'Kích hoạt plugin cho {$a}';
$string['failmsg'] = 'Thất bại';
$string['fake'] = 'Giả mạo';
$string['fileattachmentnotsupported'] = 'Loại tệp đính kèm không được hỗ trợ';
$string['fleschscore'] = 'Điểm cấp độ Flesch:';
$string['fullreportlink'] = 'Liên kết báo cáo đầy đủ';
$string['fullscreenview'] = 'Xem ở chế độ toàn màn hình';
$string['forum'] = 'Diễn đàn';
$string['humanpercentage'] = '{$a} Có thể là bản gốc';
$string['inserterror'] = 'Lỗi xảy ra khi cố gắng chèn dữ liệu vào cơ sở dữ liệu.';
$string['lite'] = 'Tiếng Anh';
$string['matchinfo'] = 'Thông tin trận đấu';
$string['matchpercentage'] = 'Tỷ lệ trận đấu:';
$string['multilang'] = 'Đa ngôn ngữ';
$string['nopageaccess'] = 'Truy cập bị từ chối vào trang này';
$string['origai'] = 'Plugin phát hiện đạo văn OriginalityAI';
$string['origaicoursesettings'] = 'Cài đặt OriginalityAI';
$string['origaienable'] = 'Bật OriginalityAI';
$string['original'] = 'Bản gốc';
$string['originalityai'] = 'Sáng tạo.ai';
$string['phrase'] = 'Cụm từ:';
$string['plagiarismscan'] = 'Kiểm tra đạo văn từ Originality.AI';
$string['plagresulttitle'] = 'Tỷ số trận đấu';
$string['privacy:metadata:plagiarism_origai_client'] = 'Để tạo báo cáo đạo văn, dữ liệu nộp cần được trao đổi với OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_client:submission_content'] = 'Nội dung bài nộp được gửi đến OriginalityAI để tạo báo cáo đạo văn.';
$string['privacy:metadata:plagiarism_origai_files'] = 'Thông tin liên kết giữa bài nộp được thực hiện trên Moodle với kết quả kiểm tra đạo văn do OriginalityAI tạo ra.';
$string['privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel'] = 'Mức độ dễ hiểu theo thang điểm Flesch của bài viết.';
$string['privacy:metadata:plagiarism_origai_plagscan:totaltextscore'] = 'Điểm tổng thể của bài viết.';
$string['privacy:metadata:plagiarism_origai_plagscan:updatedtime'] = 'Dấu thời gian cho biết thời điểm lần sửa đổi cuối cùng của bản nộp do người dùng thực hiện.';
$string['privacy:metadata:plagiarism_origai_plagscan:userid'] = 'ID của người dùng đã thực hiện việc nộp.';
$string['quiz'] = 'Bài kiểm tra';
$string['real'] = 'Thực tế';
$string['reportpagetitle'] = 'Báo cáo quét AI về tính độc đáo';
$string['retryscan'] = 'Thử lại quét';
$string['runaicheck'] = 'Kiểm tra AI';
$string['runplagiarismcheck'] = 'Kiểm tra đạo văn';
$string['scanfailed'] = 'Quét không thành công';
$string['scaninprogress'] = 'Quá trình quét đang diễn ra.';
$string['scanqueued'] = 'Quét đang chờ xử lý';
$string['scanqueuednotification'] = 'Quá trình quét đã được xếp hàng thành công. Quá trình xử lý sẽ bắt đầu trong thời gian ngắn.';
$string['scanreportfailed'] = 'Không thể lấy được báo cáo quét.';
$string['score'] = 'Điểm số:';
$string['sendqueuedsubmissionstaskname'] = 'Plugin OriginalityAI - Xử lý các tệp tin đang chờ xử lý';
$string['standard'] = 'Tiêu chuẩn';
$string['status'] = 'Trạng thái =';
$string['studentdisclosure'] = 'Thông báo của sinh viên';
$string['studentdisclosure_help'] = 'Nội dung này sẽ được hiển thị cho tất cả sinh viên trên trang tải lên tệp.';
$string['studentdisclosuredefault'] = 'Tất cả nội dung được gửi sẽ được kiểm tra đạo văn và phát hiện AI.';
$string['successmsg'] = 'Thành công';
$string['textextractionfailed'] = 'Không thể trích xuất văn bản';
$string['totalmatches'] = 'Tổng số trận đấu:';
$string['updateerror'] = 'Lỗi xảy ra khi cố gắng cập nhật các bản ghi trong cơ sở dữ liệu.';
$string['useorigai'] = 'Bật OriginalityAI';
$string['website'] = 'Trang web:';
