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
 * Plugin config utility
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\helpers;

require_once($CFG->dirroot . '/plagiarism/origai/lib.php');


/**
 * Utility class for configuration setting management
 * @package plagiarism_origai
 */
class plagiarism_origai_plugin_config {

    /**
     * Get the admin config settings for the plugin
     * @param ?string (optional)$key The key to retrieve from the config
     * @param ?string (optional)$default The default value to return if the key is not found
     * @return string|array|null plagiarism_origai plugin admin configurations
     */
    public static function admin_config($key = null, $default = null) {
        if ($key) {
            $value = get_config('plagiarism_origai', $key);
            return $value === false ? $default : $value;
        }
        return (array) get_config('plagiarism_origai');
    }

    /**
     * Get the default student disclosure text
     * @return string The default student disclosure text
     */
    public static function get_default_student_disclosure() {
        return get_string('studentdisclosuredefault', 'plagiarism_origai');
    }

    /**
     * Get default model
     * @return string
     */
    public static function get_default_model() {
        return "lite";
    }

    /**
     *  Get default api base url
     *  @return string
     */
    public static function get_default_api_base_url() {
        return "https://integrations.originality.ai/api/v1";
    }

    /**
     * Get supported models
     * 
     */
    public static function get_models() {
        return [
            'lite' => get_string('lite', 'plagiarism_origai'),
            'turbo' => get_string('turbo', 'plagiarism_origai'),
            'multilang' => get_string('multilang', 'plagiarism_origai'),
        ];
    }

    /**
     * Set plugin course module config
     * @param int $cm The course module id
     * @param string $name The configuration name
     * @param string $value The configuration value
     * @return void
     */
    public static function set_cm_config($cm, $name, $value) {
        global $DB;
        $table = 'plagiarism_origai_config';
        $record = $DB->get_record($table, ['cm' => $cm, 'name' => $name]);
        if ($record === false) {
            $record = new \stdClass;
            $record->cm = $cm;
            $record->name = $name;
            $record->value = $value;
            $DB->insert_record($table, $record);
        } else {
            $DB->set_field($table, 'value', $value, ['id' => $record->id]);
        }
    }

    /**
     * Get the configuration settings for the given course module id
     * @param int $cm The course module id
     * @param string $name The configuration name
     * @param string $default The default value to return if the configuration is not found
     * @return string|null The configuration value
     */
    public static function get_cm_config($cm, $name = null, $default = null) {
        global $DB;
        $table = 'plagiarism_origai_config';
        if ($name === null) {
            return $DB->get_records_menu($table, ['cm' => $cm], '', 'name,value');
        }
        $record = $DB->get_record($table, ['cm' => $cm, 'name' => $name]);
        if ($record === false) {
            return $default;
        }
        return $record->value;
    }

    /**
     * Delete the configuration settings for the given course module id
     * @param int $cmid The course module id
     * @return bool True if the configuration settings were deleted, false otherwise
     */
    public static function delete_cm_config($cmid) {
        global $DB;
        $table = 'plagiarism_origai_config';
        return $DB->delete_records($table, ['cm' => $cmid]);
    }

    /**
     * Get the configuration settings for the given course module ids
     * @param array $cmids The course module ids
     * @param array $names The configuration names
     * @return array The configuration settings grouped by course module id
     */
    public static function get_cms_config($cmids, $names) {
        global $DB;
        $table = 'plagiarism_origai_config';
        list($cmidsql, $cmidparams) = $DB->get_in_or_equal($cmids, SQL_PARAMS_NAMED, 'cm');
        list($namesql, $nameparams) = $DB->get_in_or_equal($names, SQL_PARAMS_NAMED, 'name');

        $sql = "SELECT id, cm, name, value
        FROM {{$table}}
        WHERE cm $cmidsql AND name $namesql";

        $params = array_merge($cmidparams, $nameparams);

        $rows = $DB->get_records_sql($sql, $params);

        // Group by cmid → [name => value]
        $grouped = [];

        foreach ($rows as $row) {
            if (!isset($grouped[$row->cm])) {
                $grouped[$row->cm] = [];
            }
            $grouped[$row->cm][$row->name] = $row->value;
        }
        return $grouped;
    }

    /**
     * Check if the module is enabled for the given course module id
     * @param string $modulename The module name
     * @param int $cmid The course module id
     * @return bool True if the module is enabled, false otherwise
     */
    public static function is_module_enabled($modulename, $cmid) {
        $moduledenabled = plagiarism_origai_is_plugin_configured('mod_' . $modulename);

        $cmenabled = self::get_cm_config($cmid, 'plagiarism_origai_enable', false);
        if (!$cmenabled || !$moduledenabled) {
            return false;
        }
        return true;
    }
}
