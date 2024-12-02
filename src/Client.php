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

use \Abollinger\Helpers;

class Client 
{
    private $curl;


    public static function get(
        array $params = []
    ) :array {
        $params = Helpers::defaultParams([
            "url" => "",
            "headers" => []
        ], $params);

        return self::request(array_merge($params, ["method" => "GET"]));
    }

    public static function post(
        array $params = []
    ) :array {
        $params = Helpers::defaultParams([
            "url" => "",
            "data" => [],
            "headers" => []
        ], $params);

        return self::request(array_merge($params, ["method" => "POST"]));
    }

    private static function request(
        array $params = []
    ) :array {
        $params = Helpers::defaultParams([
            "url" => "",
            "method" => "GET",
            "data" => [],
            "headers" => []
        ], $params);

        $curl = curl_init($params["url"]);

        if (strtoupper($params["method"]) === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params["data"]));
        } elseif (strtoupper($params["method"]) === "GET") {
            if (!empty($params["data"])) {
                $urlWithParams = $params["url"] . '?' . http_build_query($params["data"]);
                curl_setopt($curl, CURLOPT_URL, $urlWithParams);
            }
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge([
            'Content-Type: application/x-www-form-urlencoded',
        ], $params["headers"]));

        curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            return ["error" => "cURL error: " . curl_error($curl)];
        } else {
            $data = json_decode($response, true);
            return $data;
        }

        curl_close($curl);
    }
}