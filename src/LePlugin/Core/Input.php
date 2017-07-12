<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */

namespace LePlugin\Core;

class Input {

    public static function get($name, $default = null) {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    public static function post($name, $default = null) {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    public static function request($name, $default = null) {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }

}
