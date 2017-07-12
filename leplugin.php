<?php

/**
 * Plugin Name: Multisoft MarketPowerPRO Tools
 * Description: Integration of Market Power Pro to wordpress
 * Version: 2.1.4
 * Author: Rodine Mark Paul L. Villar, Dexter John Campos
 * Author URI: mailto:dean.villar@gmail.com?subject= Multisoft MPP Tools
 */
define('MPP_DEBUG', false || WP_DEBUG);

if (!function_exists('is_plugin_active')) {
    require ABSPATH . 'wp-admin/includes/plugin.php';
}
include_once 'autoloader.php';

$multisoft_dirname = dirname(__FILE__);
$multisoft_pluginname = plugin_basename(__FILE__);

if (is_admin()) {
    LePlugin\Core\CoreController::instance($multisoft_dirname, $multisoft_pluginname);
}
\LePlugin\Core\CoreApiController::instance($multisoft_dirname, $multisoft_pluginname);

if (is_admin()) {
    $multisoft_config_file = $multisoft_dirname . '/config/core_settings.json';
    $multisoft_config = null;
    if (file_exists($multisoft_config_file)) {
        $multisoft_config_data = json_decode(
            file_get_contents($multisoft_config_file),
            true
        );
        if ($multisoft_config_data) {
            $multisoft_config = new LePlugin\Core\Config($multisoft_config_data);
        }
        unset($multisoft_config_data);
    }
    \Multisoft\MPP\Core\CoreController::instance(
        $multisoft_dirname,
        $multisoft_pluginname,
        $multisoft_config
    );
    \Multisoft\MPP\Settings\SettingsController::instance(
        $multisoft_dirname,
        $multisoft_pluginname
    );
}

$multisoft_config_file = $multisoft_dirname . '/config/replication_settings.json';
$multisoft_config = null;
if (file_exists($multisoft_config_file)) {
    $multisoft_config_data = json_decode(
        file_get_contents($multisoft_config_file),
        true
    );
    if ($multisoft_config_data) {
        $multisoft_config = new LePlugin\Core\Config($multisoft_config_data);
    }
    unset($multisoft_config_data);
}
\Multisoft\MPP\Replication\ReplicationController::instance(
    $multisoft_dirname,
    $multisoft_pluginname,
    $multisoft_config
);

if (is_admin()) {
    $multisoft_config_file = $multisoft_dirname . '/config/distributor_settings.json';
    $multisoft_config = null;
    if (file_exists($multisoft_config_file)) {
        $multisoft_config_data = json_decode(
            file_get_contents($multisoft_config_file),
            true
        );

        if ($multisoft_config_data) {
            $multisoft_config = new LePlugin\Core\Config($multisoft_config_data);
        }
        unset($multisoft_config_data);
    }
    \Multisoft\MPP\Distributor\DistributorController::instance(
        $multisoft_dirname,
        $multisoft_pluginname,
        $multisoft_config
    );
}

$multisoft_config_file = $multisoft_dirname . '/config/add_distributor_settings.json';
$multisoft_config = null;
if (file_exists($multisoft_config_file)) {
    $multisoft_config_data = json_decode(
        file_get_contents($multisoft_config_file),
        true
    );
    if ($multisoft_config_data) {
        $multisoft_config = new LePlugin\Core\Config($multisoft_config_data);
    }
    unset($multisoft_config_data);
}
$add = \Multisoft\MPP\Distributor\Add\AddController::instance(
    $multisoft_dirname,
    $multisoft_pluginname,
    $multisoft_config
);
unset(
    $multisoft_dirname,
    $multisoft_pluginname,
    $multisoft_config_file,
    $multisoft_config
);