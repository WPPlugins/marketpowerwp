<?php

/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/17/2016
 * Time: 3:08 PM
 */
namespace Multisoft\MPP\Distributor;

use LePlugin\Core\AbstractController;
use Multisoft\MPP\Settings\SettingsGateway;
use LePlugin\Core\View;

class DistributorController extends AbstractController
{
    const SECTION_MPP_DISTRIBUTOR = 'mpp_distributor_section';
    const OPTION_COUNTRIES_PATH = 'get_countries_path';
    const OPTION_REGIONS_PATH = 'get_regions_path';
    const OPTION_COMMISSION_PAYMENT_PATH = 'get_commission_payment_method_path';
    const OPTION_BINARY_SIDE_LIST = 'get_binary_side_list_path';

    protected function setup()
    {
        $this->enable_activation_hook();
        $this->setup_settings();
    }

    public function activate()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $default_multisoft_mpp_get_countries_path = $this->_config->__get
        ('default_multisoft_mpp_get_countries_path');
        if ($default_multisoft_mpp_get_countries_path) {
            $settingsGateway->update(
                self::OPTION_COUNTRIES_PATH,
                $default_multisoft_mpp_get_countries_path
            );
        }

        $default_multisoft_mpp_get_regions_path = $this->_config->__get
        ('default_multisoft_mpp_get_regions_path');
        if ($default_multisoft_mpp_get_regions_path) {
            $settingsGateway->update(
                self::OPTION_REGIONS_PATH,
                $default_multisoft_mpp_get_regions_path
            );
        }

        $default_multisoft_mpp_get_commission_payment_method_path = $this->_config->__get
        ('default_multisoft_mpp_get_commission_payment_method_path');
        if ($default_multisoft_mpp_get_commission_payment_method_path) {
            $settingsGateway->update(
                self::OPTION_COMMISSION_PAYMENT_PATH,
                $default_multisoft_mpp_get_commission_payment_method_path
            );
        }

        $default_multisoft_mpp_get_binary_side_list_path = $this->_config->__get
        ('default_multisoft_mpp_get_binary_side_list_path');
        if ($default_multisoft_mpp_get_binary_side_list_path) {
            $settingsGateway->update(
                self::OPTION_BINARY_SIDE_LIST,
                $default_multisoft_mpp_get_binary_side_list_path
            );
        }
    }

    private function setup_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $settingsGateway->addSettingsSection(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_DISTRIBUTOR,
            'Distributor Settings',
            [$this, "mpp_distributor_advanced_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_DISTRIBUTOR,
            self::OPTION_COUNTRIES_PATH
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_DISTRIBUTOR,
            self::OPTION_REGIONS_PATH
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_DISTRIBUTOR,
            self::OPTION_COMMISSION_PAYMENT_PATH
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_DISTRIBUTOR,
            self::OPTION_BINARY_SIDE_LIST
        );
    }

    public function mpp_distributor_advanced_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $distributorView = new View($this, 'mpp/distributor/advanced_settings_section.php');
        $distributorView->assign('get_countries_path',
            $settingsGateway->get(self::OPTION_COUNTRIES_PATH, ''));
        $distributorView->assign('get_regions_path',
            $settingsGateway->get(self::OPTION_REGIONS_PATH, ''));
        $distributorView->assign('get_commission_payment_method_path',
            $settingsGateway->get(self::OPTION_COMMISSION_PAYMENT_PATH, ''));
        $distributorView->assign('get_binary_side_list_path',
            $settingsGateway->get(self::OPTION_BINARY_SIDE_LIST, ''));
        $distributorView->display();
    }
}