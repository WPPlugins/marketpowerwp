<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/17/2016
 * Time: 2:46 PM
 */
namespace Multisoft\MPP\Distributor\Add;

use LePlugin\Core\AbstractController;
use LePlugin\Core\Tab;
use LePlugin\Core\TabbedView;
use Multisoft\MPP\Settings\SettingsGateway;
use LePlugin\Core\View;
use Multisoft\MPP\Core\CoreController;
use LePlugin\Core\Input;


class AddController extends AbstractController
{
    const ACTION_SAVE_ADF = 'save-adf-form';
    const HANDLE_ADF_SCRIPT = 'multisoft-mpp-adf';
    const MENU_SLUG = 'add-distributor-form';
    const MENU_EDIT_SLUG = 'add-distributor-form-builder';
    const SECTION_MPP_ADD_DISTRIBUTOR = 'mpp_add_distributor_section';
    const OPTION_ADD_DISTRIBUTOR_PATH = 'add_distributor_path';
    const OPTION_ADD_DISTRIBUTOR_FORM_CONFIG = 'add_distributor_form_config';
    const OPTION_ADD_DISTRIBUTOR_FORM_SEPARATOR = 'add_distributor_form_separator';
    const OPTION_ADD_DISTRIBUTOR_FORM_FIELDS = 'add_distributor_form_fields';
    const ADF_TAB = "add_distibutor_form";

    protected function setup()
    {
        $this->enable_activation_hook();
        $this->setup_settings();

        $this->add_submenu_page(
            CoreController::MENU_SLUG,
            "Multisoft MarketPowerPRO Add Distributor",
            "Add Distributor",
            CoreController::CAP,
            self::MENU_SLUG,
            [$this, 'index']
        );

        $this->add_admin_js("mpp/adf.phpvars.php", self::LOAD_ON_SLUG, array('jquery'));
        $this->add_admin_js("mpp/adf.js", self::LOAD_ON_SLUG, array('jquery'));
        $this->add_public_js("mpp/adf.phpvars.php", array('jquery'));
        $this->add_action('admin_post_' . self::ACTION_SAVE_ADF, 'save_adf_form');
        $this->add_shortcode('mppe-add-distibutor-form', 'add_distributor');
    }

    public function activate($overwrite = false)
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $default_multisoft_mpp_add_distributor_path = $this->_config->__get
        ('default_multisoft_mpp_add_distributor_path');
        if ($default_multisoft_mpp_add_distributor_path) {
            $settingsGateway->update(
                self::OPTION_ADD_DISTRIBUTOR_PATH,
                $default_multisoft_mpp_add_distributor_path
            );
        }

        $default_multisoft_mpp_add_distributor_form_config = $this->_config->__get
        ('default_multisoft_mpp_add_distributor_form_config');
        if ($default_multisoft_mpp_add_distributor_form_config) {
            $settingsGateway->update(
                self::OPTION_ADD_DISTRIBUTOR_FORM_CONFIG,
                $default_multisoft_mpp_add_distributor_form_config,
                false,
                $overwrite
            );
        }

        $default_multisoft_mpp_add_distributor_form_separator = $this->_config->__get
        ('default_multisoft_mpp_add_distributor_form_separator');
        if ($default_multisoft_mpp_add_distributor_form_separator) {
            $settingsGateway->update(
                self::OPTION_ADD_DISTRIBUTOR_FORM_SEPARATOR,
                $default_multisoft_mpp_add_distributor_form_separator,
                false,
                $overwrite
            );
        }

        $default_multisoft_mpp_add_distributor_form_fields = $this->_config->__get
        ('default_multisoft_mpp_add_distributor_form_fields');
        if ($default_multisoft_mpp_add_distributor_form_fields) {
            $settingsGateway->update(
                self::OPTION_ADD_DISTRIBUTOR_FORM_FIELDS,
                $default_multisoft_mpp_add_distributor_form_fields,
                false,
                $overwrite
            );
        }

    }

    private function setup_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $settingsGateway->addSettingsSection(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_ADD_DISTRIBUTOR,
            'Add Distributor Settings',
            [$this, "mpp_add_distributor_advanced_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_ADD_DISTRIBUTOR,
            self::OPTION_ADD_DISTRIBUTOR_PATH
        );
    }

    public function mpp_add_distributor_advanced_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $distributorView = new View($this,
            'mpp/distributor/add/advanced_settings_section.php');
        $distributorView->assign('add_distributor_path',
            $settingsGateway->get(self::OPTION_ADD_DISTRIBUTOR_PATH, ''));
        $distributorView->display();
    }

    public function index()
    {
        $adf_tab_view = new TabbedView($this);
        $add_tab = new Tab(
            "add_view",
            "Add Distributor",
            [$this, 'add_distributor'],
            array("info" => true), null
        );
        $edit_tab = new Tab(
            "edit_view",
            "Edit Form",
            [$this, 'edit_add_distributor_form']
        );
        $adf_tab_view->addTab($add_tab);
        $adf_tab_view->addTab($edit_tab);
        $adf_tab_view->display();
    }

    public function add_distributor($atts, $content = null)
    {
        $info = false;
        extract(shortcode_atts(array(
            "info" => false
        ), $atts));

        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $formConfig = $settingsGateway->get(
            self::OPTION_ADD_DISTRIBUTOR_FORM_CONFIG
        );
        $form_view = new View($this,
            'mpp/distributor/add/form_frame.php');
        $form_view->assign('app_info', $info);
        $form_view->assign('app_src',
            $this->_plugin_dir_url . 'apps/adf/adf_form.html');
        $form_view->assign('app_width',
            ($formConfig['formWidth'] ? $formConfig['formWidth'] : '0') . '%');
        $form_view->assign('app_height',
            ($formConfig['formHeight'] ? $formConfig['formHeight'] : '0') . 'px');
        $form_view->display();
        return $content;
    }

    public function edit_add_distributor_form()
    {
        $form_builder_view = new View($this,
            'mpp/distributor/add/form_builder_frame.php');
        $form_builder_view->assign('form_action', self::ACTION_SAVE_ADF);
        $form_builder_view->assign('app_src',
            $this->_plugin_dir_url . 'apps/adf/adf_form_builder.html');
        $form_builder_view->display();
    }

    public function save_adf_form()
    {
        if (wp_verify_nonce(Input::post('_wpnonce'), self::ACTION_SAVE_ADF)) {
            /* @var $settingsGateway SettingsGateway */
            $settingsGateway = SettingsGateway::getInstance();
            $form_config = json_decode(stripslashes(Input::post('form_config')), true);
            $settingsGateway->update(
                self::OPTION_ADD_DISTRIBUTOR_FORM_CONFIG,
                $form_config['addDistributorConfigFormValues']
            );
            $settingsGateway->update(
                self::OPTION_ADD_DISTRIBUTOR_FORM_SEPARATOR,
                $form_config['addDistributorFormSeparator']
            );
            $settingsGateway->update(
                self::OPTION_ADD_DISTRIBUTOR_FORM_FIELDS,
                $form_config['addDistributorFormFields']
            );
            wp_redirect(Input::post("_wp_http_referer"));
        }
    }
}