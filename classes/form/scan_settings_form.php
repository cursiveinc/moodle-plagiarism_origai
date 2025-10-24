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
  * Scan settings ajax form
  * @package   plagiarism_origai
  * @category  plagiarism
  * @copyright Originality.ai, https://originality.ai
  * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace plagiarism_origai\form;

use core_form\dynamic_form;
use plagiarism_origai\helpers\plagiarism_origai_plugin_config;

class scan_settings_form extends dynamic_form {

    /**
     * Define form elements
     * @return void
     */
    protected function definition() {
        $mform = $this->_form;

        // -----------------------------
        // Hidden fields
        // -----------------------------
        $contextid = $this->optional_param('contextid', 0, PARAM_INT);
        $mform->addElement('hidden', 'contextid', $contextid);
        $mform->setType('contextid', PARAM_INT);

        $cmid = $this->optional_param('cmid', 0, PARAM_INT);
        $mform->addElement('hidden', 'cmid', $cmid);
        $mform->setType('cmid', PARAM_INT);

        // -----------------------------
        // Section: Omit Settings
        // -----------------------------
        $mform->addElement('header', 'omitsettings', get_string('omitsettings', 'plagiarism_origai'));
        $mform->addElement(
            'html',
            '<div class="mt-2 mb-3">' . get_string('omitsettingsdesc', 'plagiarism_origai') .'</div>'
        );

        $mform->addElement('checkbox', 'exclude_toc', get_string('excludetoc', 'plagiarism_origai'));
        $mform->addElement('checkbox', 'exclude_quotes', get_string('excludequotes', 'plagiarism_origai'));
        $mform->addElement('checkbox', 'exclude_citations', get_string('excludecitations', 'plagiarism_origai'));
        $mform->addElement('checkbox', 'exclude_references', get_string('excludereferences', 'plagiarism_origai'));

        // -----------------------------
        // Section: Upload Exclude Template
        // -----------------------------
        $mform->addElement('header', 'uploadtemplate', get_string('uploadtemplate', 'plagiarism_origai'));
        $mform->addElement(
            'html', 
            '<div class="mt-2 mb-3">' . get_string('uploadtemplatedesc', 'plagiarism_origai')
            . '</div>');

        $mform->addElement(
            'filemanager',
            'exclude_templates',
            get_string('excludetemplates', 'plagiarism_origai'),
            null,
            [
                'subdirs' => 0,
                'maxbytes' => 500000,
                'maxfiles' => 2,
                'accepted_types' => ['.doc', '.docx', '.pdf', '.txt']
            ]
        );
        $mform->setType('exclude_templates', PARAM_INT);

        // -----------------------------
        // Section: Exclude URLs
        // -----------------------------
        $mform->addElement('header', 'excludeurls', get_string('excludeurlssection', 'plagiarism_origai'));
        $mform->addElement('html', '<div class="mt-2 mb-3">' . get_string('excludeurlsdesc', 'plagiarism_origai') . "</div>");

        $mform->addElement(
            'textarea',
            'exclude_urls',
            get_string('excludeurlslabel', 'plagiarism_origai'),
            'wrap="virtual" rows="4" cols="60" placeholder="Enter URLs to exclude, one per line&#10;https://example.com&#10;https://reference.com/article"'
        );
        $mform->setType('exclude_urls', PARAM_RAW);
    }

    /**
     * Validate form input data.
     *
     * @param array $data
     * @param array $files
     * @return array of errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['exclude_urls'])) {
            // Split by newlines, trim whitespace.
            $urls = preg_split('/\r\n|\r|\n/', trim($data['exclude_urls']));
            $lineNumber = 1;

            foreach ($urls as $url) {
                $url = trim($url);
                if ($url === '') {
                    $lineNumber++;
                    continue;
                }

                // Validate URL format using PHP's built-in filter.
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    $errors['exclude_urls'] = get_string('invalidurl', 'plagiarism_origai', $lineNumber);
                    break; // stop at first invalid line.
                }

                $lineNumber++;
            }
        }

        return $errors;
    }

    /**
     * Return context for the form based on JS args.
     * @return \context
     */
    protected function get_context_for_dynamic_submission(): \context {
        $contextid = $this->optional_param('contextid', 0, PARAM_INT);
        return \context::instance_by_id($contextid);
    }

    /**
     * Check that the user can access this form.
     * @return void
     * @throws \moodle_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        $context = $this->get_context_for_dynamic_submission();
        require_capability('mod/assign:grade', $context);
    }

    /**
     * Handle form submission.
     * @return void
     * @throws \moodle_exception
     */
    public function process_dynamic_submission() {
        $data = $this->get_data();
        $context = $this->get_context_for_dynamic_submission();
        $cmid = $this->optional_param('cmid', 0, PARAM_INT);

        // Save files from draft to permanent area.
        if (!empty($data->exclude_templates)) {
            file_save_draft_area_files(
                $data->exclude_templates,
                $context->id,
                'plagiarism_origai',
                'exclude_templates',
                $cmid, // Itemid should match what you use in file_prepare_draft_area
                [
                    'subdirs' => 0,
                    'maxfiles' => 2,
                    'maxbytes' => 50000
                ]
            );
        }

        // Save other fields (store in config or DB as needed).
        $defaults = plagiarism_origai_plugin_config::scan_setting_defaults();
        foreach ($defaults as $key => $value) {
            $value = $data->{$key} ?? $value;
            if (empty($value)){
                continue;
            }
            plagiarism_origai_plugin_config::set_cm_config($cmid, $key, $value);
        }

        file_put_contents(__DIR__ . '/form_data.json', 'data to be saved' . json_encode($data) . PHP_EOL, FILE_APPEND);
    }

    /**
     * Prepopulate the form with existing data and files.
     * @return void
     * @throws \moodle_exception
     */
    public function set_data_for_dynamic_submission(): void {
        $context = $this->get_context_for_dynamic_submission();
        $cmid = $this->optional_param('cmid', 0, PARAM_INT);

        // Get or create draft item ID.
        $draftitemid = file_get_submitted_draft_itemid('exclude_templates');
        file_put_contents(__DIR__ . '/form_data.json', 'draftitemid: '.json_encode($draftitemid) . PHP_EOL, FILE_APPEND);

        // Copy existing files to draft area.
        file_prepare_draft_area(
            $draftitemid,
            $context->id,
            'plagiarism_origai',
            'exclude_templates',
            $cmid,
            [
                'subdirs' => 0,
                'maxfiles' => 2,
                'maxbytes' => 50000
            ]
        );

        // Prepare default data.
        $data = new \stdClass();
        $formdata = plagiarism_origai_plugin_config::get_saved_scan_properties($cmid);
        foreach ($formdata as $key => $value) {
            $data->{$key} = $value;
        }
        $data->contextid = $context->id;
        $data->cmid = $cmid;
        $data->exclude_templates = $draftitemid;
        file_put_contents(__DIR__ . '/form_data.json', 'data set: ' . json_encode($data) . PHP_EOL, FILE_APPEND);

        $this->set_data($data);
    }



    /**
     * Defines the page URL for dynamic form metadata.
     * @return \moodle_url
     */
    public function get_page_url_for_dynamic_submission(): \moodle_url {
        $contextid = $this->optional_param('contextid', 0, PARAM_INT);
        $cmid = $this->optional_param('cmid', 0, PARAM_INT);

        return new \moodle_url('/plagiarism/origai/scan_settings.php', [
            'contextid' => $contextid,
            'cmid' => $cmid
        ]);
    }
}
