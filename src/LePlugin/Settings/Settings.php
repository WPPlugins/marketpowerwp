<?php

namespace LePlugin\Settings;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 * Wrapper class for SettingsGateway get & update using static functions
 */
class Settings {

    public static function get($name, $default = "", $isPassword = false) {
        $gateway = new SettingsGateway();
        return $gateway->get($name, $default, $isPassword);
    }

    public static function update($name, $value, $isPassword = false) {
        $gateway = new SettingsGateway();
        return $gateway->update($name, $value, $isPassword);
    }

}
