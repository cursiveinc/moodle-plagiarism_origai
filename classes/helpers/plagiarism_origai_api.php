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
 * Utility class for plagiarism_origai api
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\helpers;

use plagiarism_origai\helpers\plagiarism_origai_http_client;
use plagiarism_origai\helpers\plagiarism_origai_plugin_config;

/**
 * Utility class for available originality API
 * @package plagiarism_origai
 */
class plagiarism_origai_api {

    /** @var string $baseurl */
    protected $baseurl;

    /** @var string $apikey */
    protected $apikey;

    /** @var string */
    const RESPONSEOK = 200;

    /**
     * Constructor
     * @throws \moodle_exception
     */
    public function __construct() {
        plagiarism_origai_plugin_config::clear_admin_config_cache();
        $baseurl = plagiarism_origai_plugin_config::admin_config(
            'apiurl',
            plagiarism_origai_plugin_config::get_default_api_base_url()
        );
        $this->baseurl = rtrim($baseurl, '/');
        $this->apikey = plagiarism_origai_plugin_config::admin_config('apikey');
    }

    /**
     * Test connection to the originality API
     * @return boolean
     */
    public function test_connection() {
        try {
            $httpclient = new plagiarism_origai_http_client();
            $httpclient->set_timeout(15);
            list($responsebody, $statuscode) = $httpclient->get($this->baseurl . '/moodle/status');
            if(
                $statuscode == static::RESPONSEOK &&
                $responsebody && isset(json_decode($responsebody)->success) &&
                json_decode($responsebody)->success
            )
            {
                return true;
            }
            return false;
        } catch (\moodle_exception $th) {
            return false;
        }
    }

    /**
     * Sync the integration data with the originality API
     * @return boolean
     *
     */
    public function integration_data_sync() {
        global $CFG;
        $httpclient = new plagiarism_origai_http_client();
        $httpclient->set_timeout(15);
        $domain = (new \moodle_url('/'))->out(false);
        $pluginversion = plagiarism_origai_plugin_config::admin_config('version');
        $moodleversion = $CFG->version;

        try {
            $response = $httpclient->post(
                $this->baseurl . '/moodle/integration',
                [
                    "domain" => $domain,
                    "plugin_version" => $pluginversion,
                    "moodle_version" => $moodleversion,
                ],
                ['X-OAI-API-KEY' => $this->apikey]
            );

            return $response[1] == static::RESPONSEOK;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Post install/upgrade data sync 
     */
    public function integration_upgrade_data_sync() {
        global $CFG;
        $httpclient = new plagiarism_origai_http_client();
        $httpclient->set_timeout(15);
        $domain = (new \moodle_url('/'))->out(false);
        $pluginversion = plagiarism_origai_plugin_config::admin_config('version');
        $moodleversion = $CFG->version;

        try {

            $response = $httpclient->post(
                $this->baseurl . '/moodle/integration/installs',
                [
                    "domain" => $domain,
                    "plugin_version" => $pluginversion,
                    "moodle_version" => $moodleversion,
                ],
                ['X-OAI-API-KEY' => $this->apikey]
            );

            return $response[1] == static::RESPONSEOK;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Scan a batch of content with the originality API
     * @param array $batch
     * @return object|false
     *
     */
    public function batch_scan(array $batch) {
        global $CFG;
        $httpclient = new plagiarism_origai_http_client();
        $httpclient->set_timeout(100);
        $domain = (new \moodle_url('/'))->out(false);
        $pluginversion = plagiarism_origai_plugin_config::admin_config('version');
        $moodleversion = $CFG->version;
        $payload = [
            'domain' => $domain,
            'plugin_version' => $pluginversion,
            'moodle_version' => $moodleversion,
            'batch' => $batch,
        ];

        try {
            $response = $httpclient->post(
                $this->baseurl . '/moodle/batch-scan',
                $payload,
                ['X-OAI-API-KEY' => $this->apikey]
            );
            list($responsebody, $statuscode) = $response;
            if($statuscode != static::RESPONSEOK && empty($responsebody)) {
                return false;
            }
            return json_decode($responsebody);
        } catch (\Throwable $th) {
            debugging("Batch scan failed: " . $th->getMessage());
            return false;
        }
    }

    /**
     * Get report
     * @param string $identifier
     * @return object|false
     */
    public function get_report($identifier) {
        $httpclient = new plagiarism_origai_http_client();
        $httpclient->set_timeout(10);
        try {
            $response = $httpclient->get(
                $this->baseurl . '/moodle/report/' . $identifier,
                ['X-OAI-API-KEY' => $this->apikey]
            );
            list($responsebody, $statuscode) = $response;
            if($statuscode != static::RESPONSEOK && empty($responsebody)) {
                return false;
            }
            return json_decode($responsebody);
        } catch (\Throwable $th) {
            debugging("Report retrieval failed: " . $th->getMessage());
            return false;
        }
    }
}
