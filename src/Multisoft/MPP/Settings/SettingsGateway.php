<?php

/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 5/28/2016
 * Time: 3:07 AM
 */

namespace Multisoft\MPP\Settings;

use LePlugin\Core\Utils;
use LePlugin\Settings\SettingsTab;
use LePlugin\Settings\SettingsSection;

class SettingsGateway
{

    const PREFIX = "multisoft_mpp_";
    const GENERAL_TAB = "general";
    const ADVANCED_TAB = "advanced";

    private static $instance;
    public static $_tabs = [];
    private $encryption_key = 'cbi9pS&AgUIUzlbZ&vMagRTd%qc0tW';

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct()
    {
        $this->addSettingsTab(self::GENERAL_TAB, "General");
        $this->addSettingsTab(self::ADVANCED_TAB, "Advanced");
    }

    public function addSettingsTab($name, $displayName)
    {
        //don't add if exists already
        if (!isset(self::$_tabs[$name])) {
            self::$_tabs[$name] = new SettingsTab($name, $displayName);
        }
    }

    public function addSettingsSection($tabName, $sectionId, $title, $displayCallback,
                                       $headerCallback = "")
    {
        /* @var $tab \LePlugin\Settings\SettingsTab */
        $tab = self::$_tabs[$tabName];
        $section = new SettingsSection($sectionId, $title, $displayCallback, $headerCallback);
        $tab->addSection($section);
        self::$_tabs[$tab->name] = $tab;
    }

    public function registerSetting($tabName, $sectionId, $settingId, $isPassword = false)
    {
        /* @var $section \LePlugin\Settings\SettingsSection */
        /* @var $tab \LePlugin\Settings\SettingsTab */

        $tab = self::$_tabs[$tabName];
        $section = $tab->sections[$sectionId];
        $section->registerSetting($settingId, $isPassword);
    }

    public function getSettingsOnTab($tabName)
    {
        $tab = self::$_tabs[$tabName];
        $settings = [];
        foreach ($tab->sections as $sections) {
            $settings = array_merge($settings, $sections->settings);
        }
        return $settings;
    }

    public function get($name, $default = "", $isPassword = false)
    {
        $value = get_option(self::PREFIX . $name, $default);
        if ($isPassword) {
            return Utils::decrypt($value, $this->encryption_key);
        }
        return $value;
    }

    public function update($name, $value, $isPassword = false, $overwrite = true)
    {
        $opt = $this->get($name, false, $isPassword);
        if ($opt === false || $overwrite) {
            if ($isPassword) {
                return update_option(self::PREFIX . $name, Utils::encrypt($value, $this->encryption_key));
            }
            return update_option(self::PREFIX . $name, $value);
        } else {
            return true;
        }
    }

}
