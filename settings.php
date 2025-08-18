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
 * Settings page for plagiarism_origai plugin
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)) . '/../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/plagiarismlib.php');
require_once($CFG->dirroot.'/plagiarism/origai/lib.php');
require_once($CFG->dirroot.'/plagiarism/origai/plagiarism_form.php');

require_login();
admin_externalpage_setup('plagiarismorigai');

$context = context_system::instance();

require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

$mform = new plagiarism_setup_form();
$plagiarismplugin = new plagiarism_plugin_origai();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/category.php?category=plagiarism'));
}

echo $OUTPUT->header();

if (($data = $mform->get_data()) && confirm_sesskey()) {
    $mform->save($data);
    echo $OUTPUT->notification(get_string('adminconfigsavesuccess', 'plagiarism_origai'), 'notifysuccess');
}

$mform->init_form_data();

$mform->display();
echo $OUTPUT->footer();
