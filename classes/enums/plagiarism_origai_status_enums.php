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
 * Enum classes.
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\enums;


/**
 * Scan status enums.
 */
class plagiarism_origai_status_enums {

    /**
     * Submission has been created but not scheduled yet
     * @var string
     */
    const PENDING = "pending";

    /**
     * @var string
     */
    const SCHEDULED = "scheduled";
    /**
     * @var string
     */
    const PROCESSING = "processing";
    /**
     * @var string
     */
    const COMPLETED = "completed";
    /**
     * @var string
     */
    const FAILED = "failed";
    /**
     * @var string
     */
    const SKIPPED = "skipped";
}
