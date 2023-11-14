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
            get_string('adminconfig', 'plagiarism_originalityai', null, true)
        );

        $supportedmodules = array('assign', 'forum', 'quiz');
        foreach ($supportedmodules as $module) {
            $mform->addElement(
                'advcheckbox',
                'plagiarism_originalityai_mod_' . $module,
                get_string('enablemodule', 'plagiarism_originalityai', ucfirst($module == 'assign' ? 'Assignment' : $module))
            );
        }

        $mform->addElement('text', 'apiurl', get_string('apiurl', 'plagiarism_originalityai'));
        $mform->setType('apiurl', PARAM_TEXT);
        $mform->addRule('apiurl', get_string('apiurlrequired', 'plagiarism_originalityai'), 'required');

        $mform->addElement('text', 'apikey', get_string('apikey', 'plagiarism_originalityai'));
        $mform->setType('apikey', PARAM_TEXT);
        $mform->addRule('apikey', get_string('apikeyrequired', 'plagiarism_originalityai'), 'required');

        $mform->addElement('html', '<div class="form-group row fitem"><div class="col-md-12 col-form-label">'.get_string("apikeyhelp","plagiarism_originalityai"));

        $this->add_action_buttons(true);
    }

    function init_form_data()
    {
        $data =  get_config('plagiarism_originalityai');
        $this->set_data($data);
    }

    function save(stdClass $data)
    {
        set_config('apiurl', $data->apiurl, 'plagiarism_originalityai');
        set_config('apikey', $data->apikey, 'plagiarism_originalityai');

        set_config('plagiarism_originalityai_mod_assign', $data->plagiarism_originalityai_mod_assign, 'plagiarism_originalityai');
        set_config('plagiarism_originalityai_mod_forum', $data->plagiarism_originalityai_mod_forum, 'plagiarism_originalityai');
        set_config('plagiarism_originalityai_mod_quiz', $data->plagiarism_originalityai_mod_quiz, 'plagiarism_originalityai');

        $supportedmodules = array('assign', 'forum', 'quiz');
        $pluginenabled = 0;
        foreach ($supportedmodules as $module) {
            if ($data->{'plagiarism_originalityai_mod_' . $module}) {
                $pluginenabled = 1;
            }
        }
        set_config('enabled', $pluginenabled, 'plagiarism_originalityai');
    }
}
