<?php
// This file is part of the plagiarism_origai plugin for Moodle
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
 * Event handler for course_module
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\event\observer;

defined('MOODLE_INTERNAL') || die();

use plagiarism_origai\helpers\plagiarism_origai_plugin_config;
use plagiarism_origai\helpers\plagiarism_origai_action;

/**
 * Event handler class for course_module.
 */
class course_module_listener {

    /**
     * course_module_deleted event handler.
     * @param \core\event\course_module_deleted $event
     */
    public static function course_module_deleted($event) {
        $eventdata = $event->get_data();
        $cmid = $eventdata['contextinstanceid'];
        plagiarism_origai_action::delete_scans_by_cmid($cmid);
        plagiarism_origai_plugin_config::delete_cm_config($cmid);
    }
}
