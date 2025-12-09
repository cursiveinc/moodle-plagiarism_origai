<?php
// This file is part of the plagiarism_origai plugin for Moodle - http://moodle.org/
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
 * Task schedule configuration for the plagiarism_origai plugin.
 *  DOC -  https://moodledev.io/docs/5.1/apis/subsystems/task/adhoc
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\task;

use plagiarism_origai\helpers\plagiarism_origai_api;

/**
 * Class post_upgrade_task — handles post install/upgrades actions
 * @package plagiarism_origai\task
 */
class post_upgrade_task extends \core\task\adhoc_task {

    /**
     * Execute the task.
     *
     * @throws \coding_exception
     */
    public function execute() {
        $originalityapi = new plagiarism_origai_api();
        $originalityapi->integration_upgrade_data_sync();
    }
}
