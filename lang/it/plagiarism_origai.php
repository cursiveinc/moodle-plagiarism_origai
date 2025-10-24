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

$string['pluginname'] = 'Plugin per il plagio OriginalityAI';
$string['accountconfig'] = 'Configurazione del conto';
$string['adminconfig'] = 'Configurazione del plugin per il plagio OriginalityAI';
$string['adminconfigdesc'] = 'Il plugin Originality.AI offre una soluzione affidabile e completa per il rilevamento dei contenuti generati dall\'intelligenza artificiale e del plagio, aiutando educatori e studenti a garantire l\'autenticità dei loro lavori.<br />Per le istruzioni di configurazione e i dettagli di utilizzo, consultare la <a target="_blank" href="https://docs.google.com/document/d/1KzYqrmDeGbTCO-JlO2zDSV-cXuVUyeTH03F0buzufyw/edit" rel="noreferrer noopener">nostra guida</a>.<br /><br /><br />';
$string['adminconfigsavesuccess'] = 'OriginalityAI Plagiarism Plugin Impostazioni salvate';
$string['aiModel'] = 'Modello AI';
$string['aiModel_help'] = 'Inglese per contenuti ottimizzati in inglese o Multi-lang per il supporto in più lingue.';
$string['aipercentage'] = '{$a} Probabile AI';
$string['airesulttitle'] = 'Controllo AI';
$string['aiscan'] = 'Scansione AI da OriginalityAI';
$string['allowstudentreportaccess'] = 'Consentire agli studenti l\'accesso alla visualizzazione del rapporto';
$string['apiconnectionerror'] = 'Connessione API non riuscita. Verificare le impostazioni dell\'account';
$string['apikey'] = 'Chiave API OriginalityAI';
$string['apikeyhelp'] = 'Se non si dispone di una chiave API, fare <a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">clic qui per registrarsi</a>.';
$string['apikeyrequired'] = 'È richiesta la chiave di originalitàAI API';
$string['apiurl'] = 'OriginalityAI API URL di base';
$string['apiurlchanged'] = 'L\'URL dell\'API deve essere aggiornato a {$a} affinché le scansioni funzionino correttamente.';
$string['assign'] = 'Assignment';
$string['apiurlrequired'] = 'È richiesto l\'URL di base dell\'API OriginalityAI';
$string['classifierinfo'] = 'Classificare le informazioni:';
$string['defaultscanerror'] = 'Scansione fallita';
$string['defaultsettings'] = 'Impostazioni predefinite';
$string['disabledformodule'] = 'Il plugin OriginalityAI è disabilitato per questo modulo';
$string['enableautomatedscan'] = 'Abilita la scansione automatica all\'invio';
$string['enableautomatedscan_help'] = 'Esegue automaticamente la scansione delle attività inviate dopo che sono state inviate. Deselezionare per eseguire la scansione manuale.';
$string['enablemodule'] = 'Abilita il plugin per {$a}';
$string['failmsg'] = 'Bocciatura';
$string['fake'] = 'Falso';
$string['fileattachmentnotsupported'] = 'Tipo di file allegato non supportato';
$string['fleschscore'] = 'Punteggio di livello Flesch:';
$string['fullreportlink'] = 'Link al rapporto completo';
$string['fullscreenview'] = 'Visualizzazione a schermo intero';
$string['forum'] = 'Forum';
$string['humanpercentage'] = '{$a} Probabile originale';
$string['inserterror'] = 'Errore durante il tentativo di inserimento di record nel database';
$string['lite'] = 'Inglese';
$string['matchinfo'] = 'Informazioni sulla partita';
$string['matchpercentage'] = 'Partita %:';
$string['multilang'] = 'Multi-Lingua';
$string['nopageaccess'] = 'Accesso negato a questa pagina';
$string['origai'] = 'OriginalityAI Plugin per il plagio';
$string['origaicoursesettings'] = 'Impostazioni di OriginalitàAI';
$string['origaienable'] = 'Abilitare l\'OriginalityAI';
$string['original'] = 'Originale';
$string['originalityai'] = 'Originalità.ai';
$string['phrase'] = 'Frase:';
$string['plagiarismscan'] = 'Scansione del plagio da Originality.AI';
$string['plagresulttitle'] = 'Punteggio della partita';
$string['privacy:metadata:plagiarism_origai_client'] = 'Per generare un rapporto sul plagio, i dati dell\'invio devono essere scambiati con OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_client:submission_content'] = 'Il contenuto dell\'invio viene inviato a OriginalityAI per la segnalazione del plagio.';
$string['privacy:metadata:plagiarism_origai_client:coursemodule'] = 'L\'ID del modulo del corso in cui è stato effettuato l\'invio.';
$string['privacy:metadata:plagiarism_origai_client:submissiondate'] = 'La data in cui è stato effettuato l\'invio.';
$string['privacy:metadata:plagiarism_origai_client:moodleuserid'] = 'L\'ID utente Moodle della persona che ha effettuato l\'invio.';
$string['privacy:metadata:plagiarism_origai_client:submissionref'] = 'L\'identificatore di riferimento dell\'invio.';
$string['privacy:metadata:plagiarism_origai_files'] = 'Informazioni che collegano l\'invio di materiale su Moodle con i risultati di plagio generati da OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel'] = 'Il livello di grado flesch dell\'invio.';
$string['privacy:metadata:plagiarism_origai_plagscan:totaltextscore'] = 'Il punteggio totale del testo dell\'elaborato.';
$string['privacy:metadata:plagiarism_origai_plagscan:updatedtime'] = 'Un timestamp che indica quando l\'invio effettuato dall\'utente è stato modificato per l\'ultima volta.';
$string['privacy:metadata:plagiarism_origai_plagscan:userid'] = 'L\'ID dell\'utente che ha effettuato l\'invio.';
$string['quiz'] = 'Quiz';
$string['real'] = 'Reale';
$string['reportpagetitle'] = 'Rapporto di scansione dell\'intelligenza artificiale sull\'originalità';
$string['retryscan'] = 'Riprova la scansione';
$string['runaicheck'] = 'Controllare l\'intelligenza artificiale';
$string['runplagiarismcheck'] = 'Controllare il plagio';
$string['scanfailed'] = 'Scansione fallita';
$string['scaninprogress'] = 'La scansione è in corso';
$string['scanqueued'] = 'Scansione in coda';
$string['scanqueuednotification'] = 'Scansione accodata con successo. L\'elaborazione inizierà a breve.';
$string['scanreportfailed'] = 'Impossibile ottenere il rapporto di scansione';
$string['score'] = 'Score:';
$string['sendqueuedsubmissionstaskname'] = 'Plugin OriginalityAI - gestione dei file in coda';
$string['status'] = 'Stato =';
$string['studentdisclosure'] = 'Divulgazione degli studenti';
$string['studentdisclosure_help'] = 'Questo testo verrà visualizzato da tutti gli studenti nella pagina di caricamento dei file.';
$string['studentdisclosuredefault'] = 'Tutti i contenuti inviati saranno sottoposti al controllo del plagio e al rilevamento dell\'IA.';
$string['successmsg'] = 'Il successo';
$string['textextractionfailed'] = 'Impossibile estrarre il testo';
$string['totalmatches'] = 'Totale partite:';
$string['updateerror'] = 'Errore durante il tentativo di aggiornare i record nel database';
$string['useorigai'] = 'Abilitare l\'OriginalityAI';
$string['website'] = 'Sito web:';
$string['ai'] = 'IA';
$string['editscansettings'] = 'Modifica impostazioni scansione';
$string['excludeurlssection'] = 'Escludi URL';
$string['excludeurlsdesc'] = 'Aggiungi URL che devono essere esclusi dal rilevamento del plagio (uno per riga)';
$string['excludecitations'] = 'Escludi citazioni';
$string['excludequotes'] = 'Escludi virgolette';
$string['excludereferences'] = 'Escludi riferimenti/bibliografie';
$string['excludetoc'] = 'Escludi indice';
$string['excludeurlslabel'] = 'Inserisci URL/domini da escludere, uno per riga';
$string['excludetemplates'] = 'Scegli file';
$string['invalidurl'] = 'URL non valido';
$string['omitsettings'] = 'Impostazioni di omissione';
$string['omitsettingsdesc'] = 'Configura quale contenuto escludere dalle scansioni di rilevamento del plagio.';
$string['scansettingstitle'] = 'Modifica impostazioni scansione';
$string['scansettingssave'] = 'Salva impostazioni scansione';
$string['standard'] = 'Standard';
$string['uploadtemplate'] = 'Carica modello di esclusione';
$string['uploadtemplatedesc'] = 'Carica un file modello contenente sezioni da escludere dalle scansioni.';
$string['openscansettings'] = 'Apri impostazioni scansione';
