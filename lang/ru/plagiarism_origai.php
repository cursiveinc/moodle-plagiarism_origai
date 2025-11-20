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

$string['pluginname'] = 'Плагин для борьбы с плагиатом OriginalityAI';
$string['accountconfig'] = 'Конфигурация учетной записи';
$string['adminconfig'] = 'Настройка плагина OriginalityAI Plagiarism Plugin';
$string['adminconfigdesc'] = 'Плагин Originality.AI предлагает надежное и комплексное решение для обнаружения как сгенерированного искусственным интеллектом контента, так и плагиата, помогая преподавателям и студентам гарантировать подлинность их работ.<br />Инструкции по настройке и подробности использования см. в <a target="_blank" href="https://docs.google.com/document/d/1KzYqrmDeGbTCO-JlO2zDSV-cXuVUyeTH03F0buzufyw/edit" rel="noreferrer noopener">нашем руководстве</a>.<br /><br /><br />';
$string['adminconfigsavesuccess'] = 'Сохранены настройки плагина OriginalityAI Plagiarism Plugin';
$string['aiModel'] = 'Модель искусственного интеллекта';
$string['aiModel_help'] = 'English для оптимизированного англоязычного контента или Multi-lang для поддержки нескольких языков.';
$string['aipercentage'] = '{$a} Вероятный искусственный интеллект';
$string['airesulttitle'] = 'Проверка искусственного интеллекта';
$string['aiscan'] = 'ИИ-сканирование от OriginalityAI';
$string['allowstudentreportaccess'] = 'Предоставьте студентам доступ к просмотру отчета';
$string['apiconnectionerror'] = 'Не удалось установить соединение с API. Пожалуйста, проверьте настройки учетной записи';
$string['apikey'] = 'Ключ API OriginalityAI';
$string['apikeyhelp'] = 'Если у вас нет ключа API, <a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">нажмите здесь, чтобы зарегистрировать</a> его <a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">.</a>';
$string['apikeyrequired'] = 'Требуется ключ оригинальности APIAI';
$string['apiurl'] = 'Базовый URL OriginalityAI API';
$string['apiurlchanged'] = 'Чтобы сканирование прошло гладко, URL-адрес API должен быть обновлен до {$a}.';
$string['apiurlrequired'] = 'Требуется базовый URL OriginalityAI API';
$string['assign'] = 'Задание';
$string['classifierinfo'] = 'Классифицируйте информацию:';
$string['defaultscanerror'] = 'Сканирование не удалось';
$string['defaultsettings'] = 'Настройки по умолчанию';
$string['disabledformodule'] = 'Плагин OriginalityAI отключен для этого модуля';
$string['enableautomatedscan'] = 'Включите автоматическое сканирование при отправке';
$string['enableautomatedscan_help'] = 'Автоматически сканирует заявки на активность после их отправки. Снимите флажок, чтобы сканировать вручную.';
$string['enablemodule'] = 'Включить плагин для {$a}';
$string['failmsg'] = 'Провал';
$string['fake'] = 'Подделка';
$string['fileattachmentnotsupported'] = 'Тип вложения файла не поддерживается';
$string['fleschscore'] = 'Оценка уровня знаний по шкале Флеша:';
$string['fullreportlink'] = 'Ссылка на полный отчет';
$string['fullscreenview'] = 'Просмотр в полноэкранном режиме';
$string['forum'] = 'Форум';
$string['humanpercentage'] = '{$a} Вероятный оригинал';
$string['inserterror'] = 'Ошибка при попытке вставить записи в базу данных';
$string['lite'] = 'Английский язык';
$string['matchinfo'] = 'Информация о матче';
$string['matchpercentage'] = 'Соответствие %:';
$string['nopageaccess'] = 'Доступ к этой странице запрещен';
$string['origai'] = 'Плагин для борьбы с плагиатом OriginalityAI';
$string['origaicoursesettings'] = 'Настройки оригинальностиAI';
$string['origaienable'] = 'Включить OriginalityAI';
$string['original'] = 'Оригинал';
$string['originalityai'] = 'Оригинальность.ai';
$string['phrase'] = 'Фраза:';
$string['plagiarismscan'] = 'Проверка на плагиат от Originality.AI';
$string['plagresulttitle'] = 'Счет матча';
$string['privacy:metadata:plagiarism_origai_client'] = 'Чтобы сгенерировать отчет о плагиате, необходимо обменяться данными с OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_client:submission_content'] = 'Полученные материалы отправляются в OriginalityAI для проверки на плагиат.';
$string['privacy:metadata:plagiarism_origai_client:coursemodule'] = 'ID модуля курса, где была сделана подача.';
$string['privacy:metadata:plagiarism_origai_client:submissiondate'] = 'Дата, когда была сделана подача.';
$string['privacy:metadata:plagiarism_origai_client:moodleuserid'] = 'ID пользователя Moodle, который сделал подачу.';
$string['privacy:metadata:plagiarism_origai_client:submissionref'] = 'Идентификатор ссылки на подачу.';
$string['privacy:metadata:plagiarism_origai_files'] = 'Информация, которая связывает подачу материала на Moodle с результатами проверки на плагиат, полученными OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel'] = 'Уровень флешевой оценки материала.';
$string['privacy:metadata:plagiarism_origai_plagscan:totaltextscore'] = 'Общая оценка текста, представленного на конкурс.';
$string['privacy:metadata:plagiarism_origai_plagscan:updatedtime'] = 'Временная метка, указывающая на время последнего изменения отправления, сделанного пользователем';
$string['privacy:metadata:plagiarism_origai_plagscan:userid'] = 'Идентификатор пользователя, сделавшего отправку.';
$string['quiz'] = 'Тест';
$string['real'] = 'Настоящий';
$string['reportpagetitle'] = 'Отчет о сканировании ИИ на оригинальность';
$string['retryscan'] = 'Повторное сканирование';
$string['runaicheck'] = 'Проверка искусственного интеллекта';
$string['runplagiarismcheck'] = 'Проверьте плагиат';
$string['scanfailed'] = 'Сканирование не удалось';
$string['scaninprogress'] = 'Выполняется сканирование';
$string['scanqueued'] = 'Сканирование в очереди';
$string['scanqueuednotification'] = 'Сканирование успешно поставлено в очередь. Обработка начнется в ближайшее время.';
$string['scanreportfailed'] = 'Невозможно получить отчет о сканировании';
$string['score'] = 'Зачет:';
$string['sendqueuedsubmissionstaskname'] = 'Плагин OriginalityAI - работа с файлами, поставленными в очередь';
$string['standard'] = 'Стандарт';
$string['status'] = 'Статус =';
$string['studentdisclosure'] = 'Раскрытие информации о студентах';
$string['studentdisclosure_help'] = 'Этот текст будет отображаться для всех студентов на странице загрузки файла.';
$string['studentdisclosuredefault'] = 'Все представленные материалы будут проверены на плагиат и обнаружение искусственного интеллекта';
$string['successmsg'] = 'Успех';
$string['textextractionfailed'] = 'Не удалось извлечь текст';
$string['totalmatches'] = 'Всего матчей:';
$string['updateerror'] = 'Ошибка при попытке обновить записи в базе данных';
$string['useorigai'] = 'Включить OriginalityAI';
$string['website'] = 'Веб-сайт:';
