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

/**
 * Class populate_contenthash
 * @package plagiarism_origai\task
 */
class populate_contenthash extends \core\task\adhoc_task {

    /**
     * Execute the task.
     *
     * @throws \coding_exception
     */
    public function execute() {
        global $DB;

        $rs = $DB->get_recordset_select('plagiarism_origai_plagscan',
            'content IS NOT NULL AND contenthash IS NULL',
            null, '', 'id,content', 0, 100); // Batch of 100

        foreach ($rs as $record) {
            $DB->update_record('plagiarism_origai_plagscan', [
                'id' => $record->id,
                'contenthash' => \plagiarism_origai\helpers\plagiarism_origai_action::generate_content_hash($record->content),
            ]);
        }
        $rs->close();

        // Reschedule if more records exist
        if ($DB->record_exists_select('plagiarism_origai_plagscan',
            'content IS NOT NULL AND contenthash IS NULL')) {
            \core\task\manager::queue_adhoc_task(new self());
        }
    }
}
