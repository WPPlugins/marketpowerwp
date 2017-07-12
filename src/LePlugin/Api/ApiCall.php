<?php

namespace LePlugin\Api;

use WP_REST_Request;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 * Calls api routes internally using global WP_REST_Server. Based on serve_request
 */
class ApiCall {

    const INTERNAL_KEY = "FQLlzx#frEsKT1U^RF*KU&x5";
    const KEY_NAME = "ApiCallPrivateKey";

    public static function isInternal(WP_REST_Request $request) {
        $key = $request[self::KEY_NAME];
        return $key === self::INTERNAL_KEY;
    }

    public static function post($route, $params) {
        $request = new WP_REST_Request("POST", $route);
        $params[self::KEY_NAME] = self::INTERNAL_KEY;
        $request->set_body_params($params);
        return self::dispatch($request);
    }

    public static function get($route, $params = array()) {
        $request = new WP_REST_Request("GET", $route);
        $params[self::KEY_NAME] = self::INTERNAL_KEY;
        $request->set_query_params($params);
        return self::dispatch($request);
    }

    private static function dispatch($request) {
        global $wp_rest_server;

        if ($wp_rest_server === null) {
            $wp_rest_server_class = apply_filters('wp_rest_server_class', 'WP_REST_Server');
            $wp_rest_server = new $wp_rest_server_class;
            do_action('rest_api_init', $wp_rest_server);
        }
        $result = $wp_rest_server->dispatch($request);
        if (is_wp_error($result)) {
            $result = $wp_rest_server->error_to_response($result);
        }
        $result = apply_filters('rest_post_dispatch', rest_ensure_response($result),
                $wp_rest_server, $request);
        return $result->data;
    }

}
