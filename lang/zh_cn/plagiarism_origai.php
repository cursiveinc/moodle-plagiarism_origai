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

$string['pluginname'] = 'OriginalityAI剽窃插件';
$string['accountconfig'] = '账户配置';
$string['adminconfig'] = 'OriginalityAI剽窃插件配置';
$string['adminconfigdesc'] = 'Originality.AI插件为检测人工智能生成的内容和剽窃行为提供了可靠而全面的解决方案，帮助教育工作者和学生确保其作品的真实性。<br />有关设置说明和使用细节，请参阅<a target="_blank" href="https://docs.google.com/document/d/1KzYqrmDeGbTCO-JlO2zDSV-cXuVUyeTH03F0buzufyw/edit" rel="noreferrer noopener">我们的指南</a>。<br /><br /><br />';
$string['adminconfigsavesuccess'] = 'OriginalityAI剽窃插件设置已保存';
$string['ai'] = '人工智能';
$string['aiModel'] = '人工智能模型';
$string['aiModel_help'] = '英语用于优化英语内容，多语言用于支持多种语言。';
$string['aipercentage'] = '{$a}可能的人工智能';
$string['airesulttitle'] = '人工智能检查';
$string['aiscan'] = '来自 OriginalityAI 的人工智能扫描';
$string['allowstudentreportaccess'] = '允许学生查看报告';
$string['apiconnectionerror'] = 'API 连接失败。请验证账户设置';
$string['apikey'] = 'OriginalityAI API 密钥';
$string['apikeyhelp'] = '如果您没有 API 密钥，<a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">请单击此处注册自己的</a>密钥<a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">。</a>';
$string['apikeyrequired'] = '原创性关键 API 是必需的';
$string['apiurl'] = 'OriginalityAI API 基础 URL';
$string['apiurlchanged'] = '必须将 API URL 更新为 {$a}，扫描才能顺利进行。';
$string['apiurlrequired'] = '需要 OriginalityAI API 的基本 URL';
$string['assign'] = '作业';
$string['classifierinfo'] = '分类信息：';
$string['defaultscanerror'] = '扫描失败';
$string['defaultsettings'] = '默认设置';
$string['disabledformodule'] = '此模块已禁用 OriginalityAI 插件';
$string['enableautomatedscan'] = '启用提交时自动扫描';
$string['enableautomatedscan_help'] = '提交后自动扫描提交的活动。取消选中可手动扫描。';
$string['enablemodule'] = '为 {$a} 启用插件';
$string['failmsg'] = '失败';
$string['fake'] = '伪造';
$string['fileattachmentnotsupported'] = '不支持文件附件类型';
$string['fleschscore'] = '弗莱什等级分数：';
$string['fullreportlink'] = '报告全文链接';
$string['fullscreenview'] = '全屏查看';
$string['forum'] = '论坛';
$string['humanpercentage'] = '{$a}可能原创';
$string['inserterror'] = '在尝试向数据库插入记录时出错';
$string['lite'] = '英语';
$string['matchinfo'] = '比赛信息';
$string['matchpercentage'] = '匹配 %：';
$string['multilang'] = '多语言';
$string['nopageaccess'] = '拒绝访问本页';
$string['origai'] = 'OriginalityAI剽窃插件';
$string['origaicoursesettings'] = '原创性AI 设置';
$string['origaienable'] = '启用 OriginalalityAI';
$string['original'] = '原创';
$string['originalityai'] = '原创性.ai';
$string['phrase'] = '短语：';
$string['plagiarismscan'] = 'Originality.AI的剽窃扫描';
$string['plagresulttitle'] = '比赛得分';
$string['privacy:metadata:plagiarism_origai_client'] = '为了生成抄袭报告，需要与 OriginalalityAI 交换提交数据。';
$string['privacy:metadata:plagiarism_origai_client:submission_content'] = '提交的内容将被发送至OriginalityAI进行抄袭报告。';
$string['privacy:metadata:plagiarism_origai_client:coursemodule'] = '进行提交的课程模块ID。';
$string['privacy:metadata:plagiarism_origai_client:submissiondate'] = '进行提交的日期。';
$string['privacy:metadata:plagiarism_origai_client:moodleuserid'] = '进行提交的人员的Moodle用户ID。';
$string['privacy:metadata:plagiarism_origai_client:submissionref'] = '提交参考标识符。';
$string['privacy:metadata:plagiarism_origai_files'] = '将在 Moodle 上提交的材料与 OriginalityAI 生成的抄袭结果联系起来的信息。';
$string['privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel'] = '提交材料的年级。';
$string['privacy:metadata:plagiarism_origai_plagscan:totaltextscore'] = '提交材料的文本总分。';
$string['privacy:metadata:plagiarism_origai_plagscan:updatedtime'] = '时间戳，表示用户提交的内容最后一次修改的时间';
$string['privacy:metadata:plagiarism_origai_plagscan:userid'] = '提交信息的用户 ID。';
$string['quiz'] = '测验';
$string['real'] = '真实';
$string['reportpagetitle'] = '原创性 AI 扫描报告';
$string['retryscan'] = '重试扫描';
$string['runaicheck'] = '检查人工智能';
$string['runplagiarismcheck'] = '检查剽窃';
$string['scanfailed'] = '扫描失败';
$string['scaninprogress'] = '扫描中';
$string['scanqueued'] = '排队扫描';
$string['scanqueuednotification'] = '扫描成功排队。处理即将开始。';
$string['scanreportfailed'] = '无法获取扫描报告';
$string['score'] = '得分：';
$string['sendqueuedsubmissionstaskname'] = 'OriginalityAI 插件 - 处理排队文件';
$string['standard'] = '标准';
$string['status'] = '状态 =';
$string['studentdisclosure'] = '学生披露';
$string['studentdisclosure_help'] = '该文本将在文件上传页面显示给所有学生。';
$string['studentdisclosuredefault'] = '所有提交的内容都将通过剽窃和人工智能检测';
$string['successmsg'] = '成功';
$string['textextractionfailed'] = '提取文本失败';
$string['totalmatches'] = '比赛总数';
$string['updateerror'] = '尝试更新数据库记录时出错';
$string['useorigai'] = '启用 OriginalalityAI';
$string['website'] = '网站：';
$string['editscansettings'] = '编辑扫描设置';
$string['excludeurlssection'] = '排除URL';
$string['excludeurlsdesc'] = '添加需要从抽窃检测中排除的URL（每行一个）';
$string['excludecitations'] = '排除引用';
$string['excludequotes'] = '排除引号';
$string['excludereferences'] = '排除参考文献/书目';
$string['excludetoc'] = '排除目录';
$string['excludeurlslabel'] = '输入要排除的URL/域名，每行一个';
$string['excludetemplates'] = '选择文件';
$string['invalidurl'] = '无效URL';
$string['omitsettings'] = '省略设置';
$string['omitsettingsdesc'] = '配置从抽窃检测扫描中排除的内容。';
$string['scansettingstitle'] = '编辑扫描设置';
$string['scansettingssave'] = '保存扫描设置';
$string['uploadtemplate'] = '上传排除模板';
$string['uploadtemplatedesc'] = '上传包含需要从扫描中排除的部分的模板文件。';
$string['openscansettings'] = '打开扫描设置';
