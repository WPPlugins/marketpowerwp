<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 5/27/2016
 * Time: 11:56 PM
 */

namespace Multisoft\MPP\Core;

use LePlugin\Core\AbstractController;
use LePlugin\Core\View;
use Multisoft\MPP\Settings\SettingsGateway;

class CoreController extends AbstractController
{
    const API_NAMESPACE = 'multisoft-mpp';
    const MENU_SLUG = 'multisoft-mpp';
    const CAP = 'manage-multisoft-mpp';
    const SECTION_MPP_DEPLOYMENT = 'mpp_deployment_information_section';
    const OPTION_WEB_ADDRESS = 'base_web_address';
    const OPTION_APPLICATION_ID = 'application_id';
    const OPTION_WEB_PATH = 'web_path';

    protected function setup()
    {
        $this->enable_activation_hook();
        $this->enable_deactivation_hook();

        $this->add_menu_page(
            "Multisoft MarketPowerPRO",
            "MarketPowerPRO",
            self::CAP,
            self::MENU_SLUG,
            [$this, 'index']
        );

        $this->add_action('admin_menu', 'mpp_admin_menu');
        $this->add_action('admin_footer', 'mpp_admin_menu_footer');

        $this->setup_settings();
    }

    public function mpp_admin_menu()
    {

        global $submenu;
        foreach ($submenu[self::MENU_SLUG] as $index => $menu) {
            if ($menu[2] == self::MENU_SLUG) {
                $menu[0] = "<div id='mppro_link'>" . $menu[0] . "</div>";
                $menu[2] = "multisoft-mpp-settings";
                $submenu[self::MENU_SLUG][$index] = $menu;
                break;
            }
        }
    }

    public function mpp_admin_menu_footer()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $url = $settingsGateway->get(
            CoreController::OPTION_WEB_ADDRESS,
            "http://www.marketpowerpro.com/"
        );
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var link = $('#mppro_link');
                $(link).parent().attr('href', '<?php echo $url;?>');
                $(link).parent().attr('target', '_blank');
            });
        </script>
        <?php
    }

    private function setup_settings()
    {

        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $settingsGateway->addSettingsSection(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_DEPLOYMENT,
            'MarketPowerPRO Deployment Information',
            [$this, "mpp_deployment_general_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_DEPLOYMENT,
            self::OPTION_WEB_ADDRESS
        );
        $settingsGateway->registerSetting(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_DEPLOYMENT,
            self::OPTION_APPLICATION_ID
        );

        $settingsGateway->addSettingsSection(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_DEPLOYMENT,
            'MarketPowerPRO Deployment Information',
            [$this, "mpp_deployment_advanced_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_DEPLOYMENT,
            self::OPTION_WEB_PATH
        );
    }

    public function index()
    {
        $content = new \stdClass();
        do_action('mpp_index_content', $content);
    }

    public function mpp_deployment_general_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();

        $view = new View($this, 'mpp/deployment_general_settings_section.php');

        $base_web_address = $settingsGateway->get(self::OPTION_WEB_ADDRESS);
        $view->assign('base_web_address', $base_web_address);

        $application_id = $settingsGateway->get(self::OPTION_APPLICATION_ID);
        $view->assign('application_id', $application_id);

        $view->display();
    }

    public function mpp_deployment_advanced_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();

        $view = new View($this, 'mpp/deployment_advanced_settings_section.php');

        $web_path = $settingsGateway->get(self::OPTION_WEB_PATH);
        $view->assign('web_path', $web_path);

        $view->display();
    }

    public function activate()
    {
        $this->add_capability('administrator', self::CAP);
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $default_web_path = $this->_config->__get('default_multisoft_mpp_web_path');
        if ($default_web_path) {
            $settingsGateway->update(self::OPTION_WEB_PATH, $default_web_path);
        }
    }

    public function deactivate()
    {
        $this->remove_capability('administrator', self::CAP);
    }
}