<?php

namespace LePlugin\Api;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */
class Api {

    const STATUS_SUCCESS = "success";
    const STATUS_ERROR = "error";
    const STATUS_FAIL = "fail";

    public static function buildApiKey($emember_id, $username) {
        return md5(md5($emember_id) . md5($username));
    }

    public static function validateApiKey($apiKey, $emember_id, $username) {
        return $apiKey == md5(md5($emember_id) . md5($username));
    }

    public static function curl_get($url, $data = array(), $curl_opts = array()) {
        if (empty($curl_opts)) {
            $curl_opts = array(
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HEADER => FALSE,
                CURLOPT_SSL_VERIFYPEER => TRUE,
            );
        }
        if (!empty($data)) {
            $curl_opts[CURLOPT_URL] = $url . "?" . http_build_query($data);
        }

        return self::curl($url, $data, $curl_opts);
    }

    public static function curl_post($url, $data, $curl_opts = array()) {
        if (empty($curl_opts)) {
            $curl_opts = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => TRUE,
                CURLOPT_POST => count($data),
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_HEADER => FALSE,
                CURLOPT_SSL_VERIFYPEER => TRUE,
            );
        }
        return self::curl($url, $data, $curl_opts);
    }

    public static function curl($url, $data, $curl_opts = array()) {
        if (empty($curl_opts)) {
            $curl_opts = array(
                CURLOPT_URL => $url . "?" . http_build_query($data),
                CURLOPT_RETURNTRANSFER => TRUE,
            );
        }
        $curl_handler = curl_init();
        curl_setopt_array($curl_handler, $curl_opts);
        $response = curl_exec($curl_handler);

        curl_close($curl_handler);

        return $response;
    }

}
