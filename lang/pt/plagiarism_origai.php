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

$string['pluginname'] = 'Plugin de plágio OriginalityAI';
$string['accountconfig'] = 'Configuração da conta';
$string['adminconfig'] = 'Configuração do plug-in de plágio do OriginalityAI';
$string['adminconfigdesc'] = 'O plug-in Originality.AI oferece uma solução fiável e abrangente para detetar conteúdos gerados por IA e plágio, ajudando educadores e estudantes a garantir a autenticidade dos seus trabalhos.<br />Para obter instruções de configuração e detalhes de utilização, consulte o <a target="_blank" href="https://docs.google.com/document/d/1KzYqrmDeGbTCO-JlO2zDSV-cXuVUyeTH03F0buzufyw/edit" rel="noreferrer noopener">nosso guia</a>.<br /><br /><br />';
$string['adminconfigsavesuccess'] = 'Definições do plug-in de plágio do OriginalityAI guardadas';
$string['ai'] = 'IA';
$string['aiModel'] = 'Modelo de IA';
$string['aiModel_help'] = 'Inglês para conteúdo optimizado em inglês ou Multi-lang para suporte em vários idiomas.';
$string['aipercentage'] = '{$a} IA provável';
$string['airesulttitle'] = 'Verificação da IA';
$string['aiscan'] = 'Análise de IA da OriginalityAI';
$string['allowstudentreportaccess'] = 'Permitir o acesso do aluno para ver o relatório';
$string['apiconnectionerror'] = 'A ligação à API falhou. Verifique as definições da conta';
$string['apikey'] = 'Chave API da OriginalityAI';
$string['apikeyhelp'] = 'Se não tiver uma chave API, <a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">clique aqui para se registar.</a>';
$string['apikeyrequired'] = 'Chave de originalidadeAI API é necessária';
$string['apiurl'] = 'URL de base da API do OriginalityAI';
$string['apiurlchanged'] = 'O URL da API tem de ser atualizado para {$a} para que as verificações decorram sem problemas.';
$string['apiurlrequired'] = 'É necessário o URL de base da API OriginalityAI';
$string['assign'] = 'Tarefa';
$string['classifierinfo'] = 'Classificar informações:';
$string['defaultscanerror'] = 'A digitalização falhou';
$string['defaultsettings'] = 'Definições por defeito';
$string['disabledformodule'] = 'O plugin OriginalityAI está desativado para este módulo';
$string['enableautomatedscan'] = 'Ativar a verificação automática na apresentação';
$string['enableautomatedscan_help'] = 'Analisa automaticamente as submissões de actividades depois de estas serem submetidas. Desmarque para verificar manualmente.';
$string['enablemodule'] = 'Ativar o plug-in para {$a}';
$string['failmsg'] = 'Falhar';
$string['fake'] = 'Falso';
$string['fileattachmentnotsupported'] = 'Tipo de anexo de ficheiro não suportado';
$string['fleschscore'] = 'Pontuação de Flesch:';
$string['fullreportlink'] = 'Ligação do relatório completo';
$string['fullscreenview'] = 'Ver em ecrã inteiro';
$string['forum'] = 'Fórum';
$string['humanpercentage'] = '{$a} Original provável';
$string['inserterror'] = 'Erro ao tentar inserir registos na base de dados';
$string['lite'] = 'Inglês';
$string['matchinfo'] = 'Informações sobre o jogo';
$string['matchpercentage'] = '% de correspondência:';
$string['multilang'] = 'Multi-língua';
$string['nopageaccess'] = 'Acesso negado a esta página';
$string['origai'] = 'Plugin de plágio OriginalityAI';
$string['origaicoursesettings'] = 'OriginalidadeDefinições de IA';
$string['origaienable'] = 'Ativar OriginalityAI';
$string['originalityai'] = 'Originalidade.ai';
$string['phrase'] = 'Frase:';
$string['plagiarismscan'] = 'Análise de plágio do Originality.AI';
$string['plagresulttitle'] = 'Resultado do jogo';
$string['privacy:metadata:plagiarism_origai_client'] = 'Para gerar um relatório de plágio, os dados de envio têm de ser trocados com o OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_client:submission_content'] = 'O conteúdo da submissão é enviado para o OriginalityAI para que seja efectuado um relatório de plágio.';
$string['privacy:metadata:plagiarism_origai_files'] = 'Informação que relaciona a submissão efectuada no Moodle com os resultados de plágio gerados pelo OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel'] = 'O nível de ensino da apresentação.';
$string['privacy:metadata:plagiarism_origai_plagscan:totaltextscore'] = 'A pontuação total do texto da apresentação.';
$string['privacy:metadata:plagiarism_origai_plagscan:updatedtime'] = 'Um carimbo de data/hora que indica a última modificação do envio efectuado pelo utilizador';
$string['privacy:metadata:plagiarism_origai_plagscan:userid'] = 'O ID do utilizador que efectuou a submissão.';
$string['quiz'] = 'Teste';
$string['reportpagetitle'] = 'Originalidade AI Scan Report';
$string['retryscan'] = 'Repetir a digitalização';
$string['runaicheck'] = 'Verificar a IA';
$string['runplagiarismcheck'] = 'Verificar o plágio';
$string['scanfailed'] = 'A digitalização falhou';
$string['scaninprogress'] = 'A digitalização está a decorrer';
$string['scanqueued'] = 'Digitalização em fila de espera';
$string['scanqueuednotification'] = 'Digitalização em fila de espera com sucesso. O processamento começará em breve.';
$string['scanreportfailed'] = 'Não é possível obter o relatório de digitalização';
$string['score'] = 'Pontuação:';
$string['sendqueuedsubmissionstaskname'] = 'Plugin OriginalityAI - tratar ficheiros em fila de espera';
$string['standard'] = 'Padrão';
$string['status'] = 'Estado =';
$string['studentdisclosure'] = 'Divulgação aos alunos';
$string['studentdisclosure_help'] = 'Este texto será apresentado a todos os alunos na página de carregamento de ficheiros.';
$string['studentdisclosuredefault'] = 'Todos os conteúdos apresentados serão submetidos a um teste de deteção de plágio e de IA';
$string['successmsg'] = 'Sucesso';
$string['textextractionfailed'] = 'Falha na extração do texto';
$string['totalmatches'] = 'Total de jogos:';
$string['updateerror'] = 'Erro ao tentar atualizar registos na base de dados';
$string['useorigai'] = 'Ativar OriginalityAI';
$string['website'] = 'Sítio Web:';
