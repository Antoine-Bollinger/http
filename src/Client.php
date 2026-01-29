<?php 
/*
 * This file is part of the Abollinger\Http package.
 *
 * (c) Antoine Bollinger <abollinger@partez.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abollinger\Http;
 
use Abollinger\Helpers;

/**
 * HTTP Client for making GET and POST requests.
 */
class Client
{
    /** @var resource|null cURL handle */
    private $curl = null;

    /** @var string Default content type for requests */
    private const DEFAULT_CONTENT_TYPE = "application/x-www-form-urlencoded";

    /**
     * Perform a GET request.
     *
     * @param array $params Request parameters (e.g., url, headers, data).
     * @return array Response data or error.
     */
    public static function get(
        array $params = []
    ) :array {
        return self::request(array_merge($params, ["method" => "GET"]));
    }

    /**
     * Perform a POST request.
     *
     * @param array $params Request parameters (e.g., url, headers, data).
     * @return array Response data or error.
     */
    public static function post(
        array $params = []
    ) :array {
        return self::request(array_merge($params, ["method" => "POST"]));
    }

    /**
     * Perform a PUT request.
     *
     * @param array $params Request parameters (e.g., url, headers, data).
     * @return array Response data or error.
     */
    public static function put(
        array $params = []
    ) :array {
        return self::request(array_merge($params, ["method" => "PUT"]));
    }

    /**
     * Perform a DELETE request.
     *
     * @param array $params Request parameters (e.g., url, headers, data).
     * @return array Response data or error.
     */
    public static function delete(
        array $params = []
    ) :array {
        return self::request(array_merge($params, ["method" => "DELETE"]));
    }

    /**
     * Perform an HTTP request.
     *
     * @param array $params Request parameters.
     * @return array Response data or error.
     */
    private static function request(
        array $params = []
    ) :array {
        $params = Helpers::defaultParams([
            "url" => "",
            "method" => "GET",
            "data" => [],
            "headers" => []
        ], $params);

        $method = strtoupper($params["method"]);
        if (!in_array($method, ["GET", "POST", "PUT", "DELETE"], true)) {
            return ["error" => "Invalid HTTP method", "code" => 400];
        }

        $curl = curl_init();
        if (!$curl) {
            return ["error" => "Failed to initialize cURL", "code" => 500];
        }

        try {
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, self::prepareHeaders($params["headers"]));
            curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

            if (in_array($method, ["POST", "PUT", "DELETE"], true) && !empty($params["data"])) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($params["data"]) ? http_build_query($params["data"]) : $params["data"]);
            } elseif ($method === "GET" && !empty($params["data"])) {
                $params["url"] .= "?" . http_build_query($params["data"]);
            }

            curl_setopt($curl, CURLOPT_URL, $params["url"]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (curl_errno($curl)) {
                throw new \RuntimeException("cURL error: " . curl_error($curl), 500);
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new \RuntimeException($response ?: "HTTP error", $httpCode);
            }

            return self::parseResponse($response);

        } catch (\RuntimeException $e) {
            return ["error" => $e->getMessage(), "code" => $e->getCode()];
        } finally {
            curl_close($curl);
        }
    }

    /**
     * Prepare headers for the cURL request.
     *
     * @param array $headers Additional headers.
     * @return array Merged headers.
     */
    private static function prepareHeaders(
        array $headers
    ) :array {
        return array_merge(
            ["Content-Type: " . self::DEFAULT_CONTENT_TYPE],
            $headers
        );
    }

    /**
     * Parse and decode the response.
     *
     * @param string $response Raw response.
     * @return array Parsed response data.
     */
    private static function parseResponse(
        string $response
    ) :array {
        $decoded = json_decode($response, true);
        return $decoded === null && json_last_error() !== JSON_ERROR_NONE
            ? ["raw" => $response]
            : $decoded;
    }
} 