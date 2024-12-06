<?php

require_once($CFG->dirroot . '/lib/formslib.php');

class plagiarism_setup_form extends moodleform
{

    /// Define the form
    function definition()
    {
        global $CFG;

        $mform = &$this->_form;
        $mform->addElement(
            'header',
            'adminconfigheader',
            get_string('adminconfig', 'plagiarism_origai', null, true)
        );

        $supportedmodules = array('assign', 'forum', 'quiz');
        foreach ($supportedmodules as $module) {
            $mform->addElement(
                'advcheckbox',
                'plagiarism_origai_mod_' . $module,
                get_string('enablemodule', 'plagiarism_origai', ucfirst($module == 'assign' ? 'Assignment' : $module))
            );
        }

        $mform->addElement('text', 'apiurl', get_string('apiurl', 'plagiarism_origai'));
        $mform->setType('apiurl', PARAM_TEXT);
        $mform->addRule('apiurl', get_string('apiurlrequired', 'plagiarism_origai'), 'required');

        $mform->addElement('text', 'apikey', get_string('apikey', 'plagiarism_origai'));
        $mform->setType('apikey', PARAM_TEXT);
        $mform->addRule('apikey', get_string('apikeyrequired', 'plagiarism_origai'), 'required');

        $mform->addElement('select', 
            'aiModel', // Name of the form field.
            get_string('aiModel', 'plagiarism_origai'), // Field label.
            array(
                'standard' => get_string('standard', 'plagiarism_origai'),
                'lite' => get_string('lite', 'plagiarism_origai'),
                'turbo' => get_string('turbo', 'plagiarism_origai'),
                'multilang' => get_string('multilang', 'plagiarism_origai'),
            )
        );

        // Set default value for the dropdown.
        $mform->setDefault('aiModel', 'lite');

        $mform->addElement('html', '<div class="form-group row fitem"><div class="col-md-12 col-form-label">'.get_string("apikeyhelp","plagiarism_origai"));

        $this->add_action_buttons(true);
    }

    function init_form_data()
    {
        $data =  get_config('plagiarism_origai');
        $this->set_data($data);
    }

    function save(stdClass $data)
    {
        set_config('apiurl', $data->apiurl, 'plagiarism_origai');
        set_config('apikey', $data->apikey, 'plagiarism_origai');
        set_config('aiModel', $data->aiModel, 'plagiarism_origai');

        set_config('plagiarism_origai_mod_assign', $data->plagiarism_origai_mod_assign, 'plagiarism_origai');
        set_config('plagiarism_origai_mod_forum', $data->plagiarism_origai_mod_forum, 'plagiarism_origai');
        set_config('plagiarism_origai_mod_quiz', $data->plagiarism_origai_mod_quiz, 'plagiarism_origai');

        $supportedmodules = array('assign', 'forum', 'quiz');
        $pluginenabled = 0;
        foreach ($supportedmodules as $module) {
            if ($data->{'plagiarism_origai_mod_' . $module}) {
                $pluginenabled = 1;
            }
        }
        set_config('enabled', $pluginenabled, 'plagiarism_origai');
    }
}
