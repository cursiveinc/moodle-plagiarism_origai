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

$string['pluginname'] = 'Plugin de plagio OriginalityAI';
$string['accountconfig'] = 'Configuración de la cuenta';
$string['adminconfig'] = 'Configuración del plugin de plagio OriginalityAI';
$string['adminconfigdesc'] = 'El plugin Originality.AI ofrece una solución fiable y completa para detectar tanto el contenido generado por IA como el plagio, ayudando a educadores y estudiantes a garantizar la autenticidad de sus trabajos.<br />Para obtener instrucciones de configuración y detalles de uso, consulte <a target="_blank" href="https://docs.google.com/document/d/1KzYqrmDeGbTCO-JlO2zDSV-cXuVUyeTH03F0buzufyw/edit" rel="noreferrer noopener">nuestra guía</a>.<br /><br /><br />';
$string['adminconfigsavesuccess'] = 'OriginalityAI Plagio Plugin Ajustes guardados';
$string['aiModel'] = 'Modelo de IA';
$string['aiModel_help'] = 'Inglés para contenidos optimizados en inglés o Multi-lang para soporte en varios idiomas.';
$string['aipercentage'] = '{$a} Probable AI';
$string['airesulttitle'] = 'Control AI';
$string['aiscan'] = 'Escaneado AI de OriginalityAI';
$string['allowstudentreportaccess'] = 'Permitir el acceso de los estudiantes para ver el informe';
$string['apiconnectionerror'] = 'Error en la conexión API. Verifique la configuración de la cuenta';
$string['apikey'] = 'Clave API de OriginalityAI';
$string['apikeyhelp'] = 'Si no dispone de una clave API, <a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">haga clic aquí para obtener la suya.</a>';
$string['apikeyrequired'] = 'Clave de originalidadSe requiere API de IAI';
$string['apiurl'] = 'URL base de la API de OriginalityAI';
$string['apiurlchanged'] = 'La URL de la API debe actualizarse a {$a} para que los escaneos funcionen correctamente.';
$string['apiurlrequired'] = 'Se requiere la URL base de la API OriginalityAI';
$string['assign'] = 'Tarea';
$string['classifierinfo'] = 'Clasificar Info:';
$string['defaultscanerror'] = 'Error de escaneado';
$string['defaultsettings'] = 'Ajustes por defecto';
$string['disabledformodule'] = 'El plugin OriginalityAI está desactivado para este módulo';
$string['enableautomatedscan'] = 'Activar el escaneado automático en el momento del envío';
$string['enableautomatedscan_help'] = 'Analiza automáticamente los envíos de actividad después de que se envíen. Desmarque esta opción para escanear manualmente.';
$string['enablemodule'] = 'Activar Plugin para {$a}';
$string['failmsg'] = 'Falla';
$string['fake'] = 'Falso';
$string['fileattachmentnotsupported'] = 'No se admite el tipo de archivo adjunto';
$string['fleschscore'] = 'Puntuación de nivel de grado Flesch:';
$string['fullreportlink'] = 'Enlace al informe completo';
$string['fullscreenview'] = 'Ver en pantalla completa';
$string['forum'] = 'Foro';
$string['humanpercentage'] = '{$a} Probable Original';
$string['inserterror'] = 'Error al intentar insertar registros en la base de datos';
$string['lite'] = 'Inglés';
$string['matchinfo'] = 'Información sobre el partido';
$string['matchpercentage'] = 'Match %:';
$string['multilang'] = 'Multilingüe';
$string['nopageaccess'] = 'Acceso denegado a esta página';
$string['origai'] = 'Plugin de plagio OriginalityAI';
$string['origaicoursesettings'] = 'OriginalidadAjustes de la IA';
$string['origaienable'] = 'Activar OriginalityAI';
$string['originalityai'] = 'Originalidad.ai';
$string['phrase'] = 'Frase:';
$string['plagiarismscan'] = 'Escaneo de plagio de Originality.AI';
$string['plagresulttitle'] = 'Resultado del partido';
$string['privacy:metadata:plagiarism_origai_client'] = 'Para generar un informe de plagio, es necesario intercambiar los datos de envío con OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_client:submission_content'] = 'El contenido del envío se envía a OriginalityAI para el informe de plagio.';
$string['privacy:metadata:plagiarism_origai_files'] = 'Información que vincula el envío realizado en Moodle con los resultados de plagio generados por OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel'] = 'El nivel de grado de la presentación.';
$string['privacy:metadata:plagiarism_origai_plagscan:totaltextscore'] = 'Puntuación total del texto presentado.';
$string['privacy:metadata:plagiarism_origai_plagscan:updatedtime'] = 'Una marca de tiempo que indica cuándo se modificó por última vez el envío realizado por el usuario';
$string['privacy:metadata:plagiarism_origai_plagscan:userid'] = 'El ID del usuario que ha realizado el envío.';
$string['quiz'] = 'Cuestionario';
$string['reportpagetitle'] = 'Originalidad Informe de exploración de la IA';
$string['retryscan'] = 'Reintentar escaneado';
$string['runaicheck'] = 'Comprobar IA';
$string['runplagiarismcheck'] = 'Comprobar el plagio';
$string['scanfailed'] = 'Error de escaneado';
$string['scaninprogress'] = 'Escaneado en curso';
$string['scanqueued'] = 'Escaneado en cola';
$string['scanqueuednotification'] = 'Escaneado en cola con éxito. El proceso comenzará en breve.';
$string['scanreportfailed'] = 'No se puede obtener el informe de análisis';
$string['score'] = 'Puntuación:';
$string['sendqueuedsubmissionstaskname'] = 'OriginalityAI plugin - gestión de archivos en cola';
$string['standard'] = 'Estándar';
$string['status'] = 'Estado =';
$string['studentdisclosure'] = 'Divulgación a los estudiantes';
$string['studentdisclosure_help'] = 'Este texto se mostrará a todos los estudiantes en la página de carga de archivos.';
$string['studentdisclosuredefault'] = 'Todos los contenidos enviados pasarán un control de plagio y detección de IA';
$string['successmsg'] = 'Éxito';
$string['textextractionfailed'] = 'Error al extraer texto';
$string['totalmatches'] = 'Total de partidos:';
$string['updateerror'] = 'Error al intentar actualizar registros en la base de datos';
$string['useorigai'] = 'Activar OriginalityAI';
$string['website'] = 'Página web:';
