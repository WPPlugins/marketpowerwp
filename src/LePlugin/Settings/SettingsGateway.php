<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */

namespace LePlugin\Settings;

use LePlugin\Core\Utils;

class SettingsGateway {

    const PREFIX = "ccc2_";

    public static $_tabs = [];
    private $encryption_key = 'cY1wQEd5Ta9TvhCskcX0wMtjO4XjzG';

    public function __construct($prefix) {
        //predefined tabs
        $this->addSettingsTab("general", "General Settings");
    }

    public function addSettingsTab($name, $displayName) {
        //don't add if exists already
        if (!isset(self::$_tabs[$name])) {
            self::$_tabs[$name] = new SettingsTab($name, $displayName);
        }
    }

    public function addSettingsSection($tabName, $sectionId, $title, $displayCallback,
            $headerCallback = "") {
        $tab = self::$_tabs[$tabName];

        $section = new SettingsSection($sectionId, $title, $displayCallback, $headerCallback);
        $tab->addSection($section);
        self::$_tabs[$tab->name] = $tab;
    }

    public function registerSetting($tabName, $sectionId, $settingId, $isPassword = false) {
        $tab = self::$_tabs[$tabName];
        $tab->sections[$sectionId]->registerSetting($settingId, $isPassword);
    }

    public function getSettingsOnTab($tabName) {
        $tab = self::$_tabs[$tabName];
        $settings = [];
        foreach ($tab->sections as $sections) {
            $settings = array_merge($settings, $sections->settings);
        }
        return $settings;
    }

    public function get($name, $default = "", $isPassword = false) {
        $value = get_option(self::PREFIX . $name, $default);
        if ($isPassword) {
            return Utils::decrypt($value, $this->encryption_key);
        }
        return $value;
    }

    public function update($name, $value, $isPassword = false) {
        if ($isPassword) {
            return update_option(self::PREFIX . $name, Utils::encrypt($value, $this->encryption_key));
        }
        return update_option(self::PREFIX . $name, $value);
    }

}
