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
 * Http client wrapper
 * @package   plagiarism_origai
 * @category  plagiarism
 * @copyright Originality.ai, https://originality.ai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_origai\helpers;


/**
 * HTTP client wrapper that uses Guzzle if available, falls back to cURL
 * @package plagiarism_origai
 */
class plagiarism_origai_http_client {
    /** @var bool Whether Guzzle is available */
    protected $hasguzzle;

    /** @var array Default HTTP headers */
    protected $defaultheaders = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    /** @var int Default timeout in seconds */
    protected $timeout = 30;

    /**
     * Constructor
     */
    public function __construct() {
        $this->hasguzzle = class_exists('\GuzzleHttp\Client');
    }

    /**
     * Make a GET request
     *
     * @param string $url
     * @param array $headers
     * @return array [response, statuscode]
     * @throws \moodle_exception
     */
    public function get(string $url, array $headers = []): array {
        return $this->request('GET', $url, [], $headers);
    }

    /**
     * Make a POST request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array [response, statuscode]
     * @throws \moodle_exception
     */
    public function post(string $url, array $data = [], array $headers = []): array {
        return $this->request('POST', $url, $data, $headers);
    }

    /**
     * Make a PUT request
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array [response, statuscode]
     * @throws \moodle_exception
     */
    public function put(string $url, array $data = [], array $headers = []): array {
        return $this->request('PUT', $url, $data, $headers);
    }

    /**
     * Make a DELETE request
     *
     * @param string $url
     * @param array $headers
     * @return array [response, statuscode]
     * @throws \moodle_exception
     */
    public function delete(string $url, array $headers = []): array {
        return $this->request('DELETE', $url, [], $headers);
    }

    /**
     * Execute HTTP request
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array [response, statuscode]
     * @throws \moodle_exception
     */
    protected function request(string $method, string $url, array $data = [], array $headers = []): array {
        $headers = array_merge($this->defaultheaders, $headers);

        if ($this->hasguzzle) {
            return $this->request_guzzle($method, $url, $data, $headers);
        }

        return $this->request_curl($method, $url, $data, $headers);
    }

    /**
     * Make request using Guzzle
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array
     * @throws \moodle_exception
     */
    protected function request_guzzle(string $method, string $url, array $data, array $headers): array {
        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => $this->timeout,
                'http_errors' => false, // Don't throw exceptions for 4xx/5xx.
            ]);

            $options = ['headers' => $headers];

            if (!empty($data)) {
                $options['json'] = $data;
            }

            $response = $client->request($method, $url, $options);

            return [
                $response->getBody()->getContents(),
                $response->getStatusCode(),
            ];

        } catch (\Exception $e) {
            throw new \moodle_exception('httperror', 'plagiarism_origai', '', $e->getMessage());
        }
    }

    /**
     * Make request using cURL
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array
     * @throws \moodle_exception
     */
    protected function request_curl(string $method, string $url, array $data, array $headers): array {
        $ch = curl_init();

        // Set headers.
        $headerstrings = [];
        foreach ($headers as $key => $value) {
            $headerstrings[] = "$key: $value";
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headerstrings,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
        ]);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \moodle_exception('httperror', 'plagiarism_origai', '', $error);
        }

        return [$response, $status];
    }

    /**
     * Set default headers
     *
     * @param array $headers
     */
    public function set_default_headers(array $headers): void {
        $this->defaultheaders = $headers;
    }

    /**
     * Set timeout
     *
     * @param int $seconds
     */
    public function set_timeout(int $seconds): void {
        $this->timeout = $seconds;
    }
}
