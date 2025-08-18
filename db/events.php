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
 * Event observer definitions for the plagiarism_origai plugin.
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\mod_assign\event\assessable_submitted',
        'callback'  => '\plagiarism_origai\event\observer\assessment_listener::assign_submitted',
    ],
    [
        'eventname' => '\mod_quiz\event\attempt_submitted',
        'callback'  => '\plagiarism_origai\event\observer\assessment_listener::quiz_submitted',
    ],
    [
        'eventname' => '\mod_forum\event\assessable_uploaded',
        'callback'  => '\plagiarism_origai\event\observer\assessment_listener::forum_submitted',
    ],
    [
        'eventname' => '\core\event\course_module_deleted',
        'callback'  => '\plagiarism_origai\event\observer\course_module_listener::course_module_deleted',
    ],
];

