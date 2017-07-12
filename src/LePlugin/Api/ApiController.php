<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */

namespace LePlugin\Api;

use LePlugin\Core\AbstractController;

abstract class ApiController extends AbstractController {

    const API_VERSION = "1.0"; //used as the main namespace

    protected final function register_route($namespace, $route, $args = array(), $override = false) {
        add_action('rest_api_init',
                function ()use($namespace, $route, $args, $override) {
            register_rest_route(self::API_VERSION . "/" . $namespace, $route, $args, $override);
        });
    }

    private function createResponse($status, $code = null, $data = null, $message = null) {
        //standard response implementing http://labs.omniti.com/labs/jsend
        $response = ["status" => $status];
        if ($code !== null) {
            $response["code"] = $code;
        }
        if ($message !== null) {
            $response["message"] = $message;
        }
        if ($data !== null) {
            $response["data"] = $data;
        }
        return $response;
    }

    protected function error($code = null, $message = null, $data = null) {
        return $this->createResponse(Api::STATUS_ERROR, $code, $data, $message);
    }

    protected function fail($code = null, $message = null, $data = null) {
        return $this->createResponse(Api::STATUS_FAIL, $code, $data, $message);
    }

    protected function success($data = null, $message = null) {
        return $this->createResponse(Api::STATUS_SUCCESS, null, $data, $message);
    }

    protected function sendAsHtml($html) {
       
        $response = [
            "leplugin_serve_as_html" => true,
            "html"=>$html
        ];
        return $response;
    }

}
