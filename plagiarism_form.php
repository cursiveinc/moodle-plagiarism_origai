<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Defines the form used by the OrigAI plagiarism plugin.
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

use plagiarism_origai\helpers\plagiarism_origai_plugin_config;
use plagiarism_origai\helpers\plagiarism_origai_api;

/**
 * Class defined plugin settings form.
 */
class plagiarism_setup_form extends moodleform {


    /**
     * Define setting form elements
     * @return void
     */
    public function definition() {
        global $CFG;

        $mform = &$this->_form;
        // Plugin Configurations.
        $mform->addElement(
            'header',
            'adminconfigheader',
            get_string('adminconfig', 'plagiarism_origai', null, true)
        );

        $mform->addElement(
            'html',
            get_string('adminconfigdesc', 'plagiarism_origai')
        );

        $supportedmodules = ['assign', 'forum', 'quiz'];
        foreach ($supportedmodules as $module) {
            $mform->addElement(
                'advcheckbox',
                'plagiarism_origai_mod_' . $module,
                get_string('enablemodule', 'plagiarism_origai', get_string($module, 'plagiarism_origai'))
            );
        }

        $mform->addElement(
            'html',
            get_string('defaultsettings', 'plagiarism_origai')
        );

        $mform->addElement(
            'textarea',
            'plagiarism_origai_studentdisclosure',
            get_string('studentdisclosure', 'plagiarism_origai')
        );

        $mform->addHelpButton(
            'plagiarism_origai_studentdisclosure',
            'studentdisclosure',
            'plagiarism_origai'
        );

        $mform->addElement('select',
            'aiModel', // Name of the form field.
            get_string('aiModel', 'plagiarism_origai'), // Field label.
            plagiarism_origai_plugin_config::get_models()
        );
        $mform->addHelpButton('aiModel', 'aiModel', 'plagiarism_origai');

        // Orginality.ai Account Configurations.
        $mform->addElement(
            'header',
            'plagiarism_origai_accountconfigheader',
            get_string('accountconfig', 'plagiarism_origai')
        );

        $mform->addElement('text', 'apiurl', get_string('apiurl', 'plagiarism_origai'));
        $mform->setType('apiurl', PARAM_TEXT);
        $mform->addRule('apiurl', get_string('apiurlrequired', 'plagiarism_origai'), 'required');

        $mform->addElement('passwordunmask', 'apikey', get_string('apikey', 'plagiarism_origai'));
        $mform->setType('apikey', PARAM_TEXT);
        $mform->addRule('apikey', get_string('apikeyrequired', 'plagiarism_origai'), 'required');

        // Set default value for the dropdown.
        $mform->setDefault('aiModel', plagiarism_origai_plugin_config::get_default_model());

        $mform->addElement(
            'html',
            '<div class="form-group row fitem"><div class="col-md-12 col-form-label">'
            .get_string("apikeyhelp", "plagiarism_origai")
        );

        $this->add_action_buttons(true);
    }

    /**
     * Initialize form data.
     *
     * @return void
     */
    public function init_form_data() {
        plagiarism_origai_plugin_config::clear_admin_config_cache();
        $config = plagiarism_origai_plugin_config::admin_config();
        $defaultapibaseurl = plagiarism_origai_plugin_config::get_default_api_base_url();
        if (isset($config['apiurl']) && $config['apiurl'] != $defaultapibaseurl) {
            \core\notification::warning(get_string('apiurlchanged', 'plagiarism_origai', $defaultapibaseurl));
        }

        if (!isset($config['apiurl']) || empty($config['apiurl'])) {
            $config['apiurl'] = $defaultapibaseurl;
        }

        if (
            !isset($config['plagiarism_origai_studentdisclosure']) ||
            empty($config['plagiarism_origai_studentdisclosure'])
        ) {
            $config['plagiarism_origai_studentdisclosure'] = plagiarism_origai_plugin_config::get_default_student_disclosure();
        }

        $this->set_data($config);
    }

    /**
     * Save form data.
     *
     * @param stdClass $data
     *
     * @return void
     */
    public function save(stdClass $data) {
        set_config('apiurl', $data->apiurl, 'plagiarism_origai');
        set_config('apikey', $data->apikey, 'plagiarism_origai');
        set_config('aiModel', $data->aiModel, 'plagiarism_origai');

        set_config('plagiarism_origai_mod_assign', $data->plagiarism_origai_mod_assign, 'plagiarism_origai');
        set_config('plagiarism_origai_mod_forum', $data->plagiarism_origai_mod_forum, 'plagiarism_origai');
        set_config('plagiarism_origai_mod_quiz', $data->plagiarism_origai_mod_quiz, 'plagiarism_origai');
        set_config('plagiarism_origai_studentdisclosure', $data->plagiarism_origai_studentdisclosure, 'plagiarism_origai');

        $supportedmodules = ['assign', 'forum', 'quiz'];
        $pluginenabled = 0;
        foreach ($supportedmodules as $module) {
            if ($data->{'plagiarism_origai_mod_' . $module}) {
                $pluginenabled = 1;
            }
        }
        set_config('enabled', $pluginenabled, 'plagiarism_origai');

        $integrationapi = new plagiarism_origai_api();
        if (!$integrationapi->integration_data_sync()) {
            \core\notification::warning(get_string('apiconnectionerror', 'plagiarism_origai'));
        }
    }
}
