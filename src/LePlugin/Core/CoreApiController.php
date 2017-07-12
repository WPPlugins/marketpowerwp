<?php

namespace LePlugin\Core;

use LePlugin\Api\ApiController;
use LePlugin\Settings\Settings;
use WP_REST_Request;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
  @copyright Les Coders
 */
class CoreApiController extends ApiController {

    const PREFIX = "api";

    public function rest_url_prefix() {
        return self::PREFIX;
    }

    protected function setup() {
        $this->add_filter("rest_url_prefix", "rest_url_prefix");
        $this->add_filter("rest_pre_serve_request", "rest_pre_serve_request", 10, 4);
        $args = ["methods" => "GET", "callback" => [$this, "retrieveSetting"]];
        $this->register_route("leplugin", "settings/(?P<name>[\w-]+)", $args);
        $this->add_filter('rest_authentication_errors', 'rest_is_logged', 10);
    }

    public function rest_is_logged() {
        if (is_user_logged_in()) {
            return true;
        }
        return false;
    }

    public function retrieveSetting(WP_REST_Request $request) {
        return $this->success(Settings::get($request["name"], $request["default"],
                                $request["password"]));
    }

    public function rest_pre_serve_request($served, $result, $request, $server) {
        $data = $result->get_data();
        if (isset($data["leplugin_serve_as_html"])) {
            header('Content-Type: text/html');
            echo $data["html"];
            return true;
        } else if (isset($data["leplugin_serve_as_js"])) {
            header('Content-Type: text/javascript');
            echo $data["javascript"];
        }
        return $served;
    }

}
