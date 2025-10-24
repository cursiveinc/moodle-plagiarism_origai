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

$string['accountconfig'] = 'Configuration du compte';
$string['adminconfig'] = 'Configuration du plugin OriginalityAI Plagiarism';
$string['adminconfigdesc'] = 'Le plugin Originality.AI offre une solution fiable et complète pour détecter à la fois le contenu généré par l\'IA et le plagiat, aidant ainsi les enseignants et les étudiants à garantir l\'authenticité de leur travail.<br />Pour les instructions d\'installation et les détails d\'utilisation, veuillez vous référer à <a target="_blank" href="https://docs.google.com/document/d/1KzYqrmDeGbTCO-JlO2zDSV-cXuVUyeTH03F0buzufyw/edit" rel="noreferrer noopener">notre guide.</a><br /><br /><br />';
$string['adminconfigsavesuccess'] = 'Enregistrement des paramètres du plugin OriginalityAI Plagiarism';
$string['aiModel'] = 'Modèle IA';
$string['aiModel_help'] = 'Anglais pour un contenu optimisé en anglais ou Multi-lang pour une prise en charge dans plusieurs langues.';
$string['aipercentage'] = '{$a} IA probable';
$string['airesulttitle'] = 'Contrôle de l\'IA';
$string['aiscan'] = 'Analyse de l\'IA par OriginalityAI';
$string['allowstudentreportaccess'] = 'Permettre à l\'étudiant de consulter le rapport';
$string['apiconnectionerror'] = 'La connexion API a échoué. Veuillez vérifier les paramètres du compte';
$string['apikey'] = 'Clé API d\'OriginalityAI';
$string['apikeyhelp'] = 'Si vous n\'avez pas de clé API, veuillez <a target="_blank" href="https://originality.ai/?via=origai-moodle" rel="noreferrer noopener">cliquer ici pour en obtenir une.</a>';
$string['apikeyrequired'] = 'Clé d\'originalité L\'API de l\'IA est nécessaire';
$string['apiurl'] = 'URL de base de l\'API d\'OriginalityAI';
$string['apiurlchanged'] = 'L\'URL de l\'API doit être mise à jour en {$a} pour que les analyses se déroulent correctement.';
$string['apiurlrequired'] = 'L\'URL de base de l\'API OriginalityAI est nécessaire';
$string['assign'] = 'Devoir';
$string['classifierinfo'] = 'Classifier Info:';
$string['defaultscanerror'] = 'Échec du balayage';
$string['defaultsettings'] = 'Paramètres par défaut';
$string['disabledformodule'] = 'Le plugin OriginalityAI est désactivé pour ce module';
$string['enableautomatedscan'] = 'Activer le balayage automatique lors de la soumission';
$string['enableautomatedscan_help'] = 'Analyse automatiquement les soumissions d\'activités après leur envoi. Décochez cette case pour effectuer une analyse manuelle.';
$string['failmsg'] = 'Échec';
$string['fake'] = 'Faux';
$string['fileattachmentnotsupported'] = 'Le type de pièce jointe n\'est pas pris en charge';
$string['fleschscore'] = 'Score de niveau de Flesch :';
$string['fullreportlink'] = 'Lien vers le rapport complet';
$string['fullscreenview'] = 'Afficher en plein écran';
$string['forum'] = 'Forum';
$string['humanpercentage'] = '{$a} Probable Original';
$string['inserterror'] = 'Erreur lors de l\'insertion d\'enregistrements dans la base de données';
$string['lite'] = 'Anglais';
$string['matchinfo'] = 'Informations sur le match';
$string['matchpercentage'] = '% de concordance :';
$string['multilang'] = 'Multi-langues';
$string['nopageaccess'] = 'Accès refusé à cette page';
$string['origaicoursesettings'] = 'Originalité Paramètres de l\'IA';
$string['origaienable'] = 'Activer l\'OriginalityAI';
$string['phrase'] = 'Phrase :';
$string['plagiarismscan'] = 'Analyse du plagiat par Originality.AI';
$string['plagresulttitle'] = 'Score du match';
$string['privacy:metadata:plagiarism_origai_client'] = 'Afin de générer un rapport de plagiat, les données de soumission doivent être échangées avec OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_client:submission_content'] = 'Le contenu de la soumission est envoyé à OriginalityAI pour un rapport de plagiat.';
$string['privacy:metadata:plagiarism_origai_client:coursemodule'] = 'L\'ID du module de cours où la soumission a été effectuée.';
$string['privacy:metadata:plagiarism_origai_client:submissiondate'] = 'La date à laquelle la soumission a été effectuée.';
$string['privacy:metadata:plagiarism_origai_client:moodleuserid'] = 'L\'ID utilisateur Moodle de la personne qui a effectué la soumission.';
$string['privacy:metadata:plagiarism_origai_client:submissionref'] = 'L\'identifiant de référence de la soumission.';
$string['privacy:metadata:plagiarism_origai_files'] = 'Information qui relie les soumissions faites sur Moodle aux résultats de plagiat générés par OriginalityAI.';
$string['privacy:metadata:plagiarism_origai_plagscan:fleschgradelevel'] = 'Le niveau scolaire de la soumission.';
$string['privacy:metadata:plagiarism_origai_plagscan:totaltextscore'] = 'La note totale du texte de la soumission.';
$string['privacy:metadata:plagiarism_origai_plagscan:updatedtime'] = 'Un horodatage indiquant la date de la dernière modification de la soumission effectuée par l\'utilisateur.';
$string['privacy:metadata:plagiarism_origai_plagscan:userid'] = 'L\'ID de l\'utilisateur qui a effectué la soumission.';
$string['quiz'] = 'Quiz';
$string['real'] = 'Réel';
$string['reportpagetitle'] = 'Originalité Rapport d\'analyse de l\'IA';
$string['retryscan'] = 'Réessayer le balayage';
$string['runaicheck'] = 'Vérifier l\'IA';
$string['runplagiarismcheck'] = 'Vérifier le plagiat';
$string['scanfailed'] = 'Échec du balayage';
$string['scaninprogress'] = 'Le balayage est en cours';
$string['scanqueuednotification'] = 'Scan mis en file d\'attente avec succès. Le traitement va commencer sous peu.';
$string['scanreportfailed'] = 'Impossible d\'obtenir le rapport d\'analyse';
$string['score'] = 'Score :';
$string['sendqueuedsubmissionstaskname'] = 'Plugin OriginalityAI - gestion des fichiers en file d\'attente';
$string['status'] = 'Statut =';
$string['studentdisclosure'] = 'Divulgation aux étudiants';
$string['studentdisclosure_help'] = 'Ce texte sera affiché à tous les étudiants sur la page de téléchargement du fichier.';
$string['studentdisclosuredefault'] = 'Tous les contenus soumis seront soumis à la détection du plagiat et de l\'IA.';
$string['successmsg'] = 'Succès';
$string['textextractionfailed'] = 'Échec de l\'extraction du texte';
$string['totalmatches'] = 'Nombre total de matches :';
$string['updateerror'] = 'Erreur lors de la mise à jour des enregistrements dans la base de données';
$string['useorigai'] = 'Activer l\'OriginalityAI';
$string['website'] = 'Site web :';
$string['ai'] = 'IA';
$string['editscansettings'] = 'Modifier les paramètres de scan';
$string['enablemodule'] = 'Activer le plugin pour {$a}';
$string['excludeurlssection'] = 'Exclure les URLs';
$string['excludeurlsdesc'] = 'Ajouter des URLs qui doivent être exclues de la détection de plagiat (une par ligne)';
$string['excludecitations'] = 'Exclure les citations';
$string['excludequotes'] = 'Exclure les guillemets';
$string['excludereferences'] = 'Exclure les références/bibliographies';
$string['excludetoc'] = 'Exclure la table des matières';
$string['excludeurlslabel'] = 'Entrez les URLs/domaines à exclure, un par ligne';
$string['excludetemplates'] = 'Choisir le fichier';
$string['invalidurl'] = 'URL invalide';
$string['omitsettings'] = 'Paramètres d\'omission';
$string['omitsettingsdesc'] = 'Configurez le contenu à exclure des scans de détection de plagiat.';
$string['origai'] = 'Plugin de plagiat OriginalityAI';
$string['original'] = 'Original';
$string['originalityai'] = 'Originality.ai';
$string['pluginname'] = 'Plugin de plagiat OriginalityAI';
$string['scanqueued'] = 'Scan en file d\'attente';
$string['scansettingstitle'] = 'Modifier les paramètres de scan';
$string['scansettingssave'] = 'Enregistrer les paramètres de scan';
$string['standard'] = 'Standard';
$string['uploadtemplate'] = 'Télécharger le modèle d\'exclusion';
$string['uploadtemplatedesc'] = 'Téléchargez un fichier modèle contenant les sections à exclure des scans.';
$string['openscansettings'] = 'Ouvrir les paramètres de scan';
