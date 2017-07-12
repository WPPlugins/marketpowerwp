<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 5/28/2016
 * Time: 3:07 AM
 */

namespace Multisoft\MPP\Settings;

use LePlugin\Core\AbstractController;
use LePlugin\Core\View;
use LePlugin\Core\Input;
use LePlugin\Settings\SettingsTabbedView;
use Multisoft\MPP\Core\CoreController;

class SettingsController extends AbstractController
{

    const MENU_SLUG = 'multisoft-mpp-settings';

    protected function setup()
    {
        $this->add_submenu_page(
            CoreController::MENU_SLUG,
            "Multisoft MarketPowerPRO Settings",
            "Settings",
            CoreController::CAP,
            self::MENU_SLUG,
            [$this, 'index']
        );
    }

    public function index()
    {
        $header_view = new View($this, "admin_head.php");
        $header_view->assign("title", "Multisoft MarketPowerPRO Settings");
        $footer_view = new View($this, "admin_foot.php");
        $settings_view = new SettingsTabbedView($this);

        foreach (SettingsGateway::$_tabs as $tab) {
            $settings_view->addTab($tab);
        }
        $settings_view->assign("nonce_action", "multisoft_mpp_update_settings");

        $submitted = $this->process_submit();
        if ($submitted) {
            $settings_view->addNotice("Settings Updated.");
        }
        $header_view->display();
        $settings_view->display();
        $footer_view->display();
    }

    private function process_submit()
    {
        $nonce = Input::post("_wpnonce");
        if (wp_verify_nonce($nonce, "multisoft_mpp_update_settings")) {
            /* @var $settingsGateway SettingsGateway */
            $settingsGateway = SettingsGateway::getInstance();
            $settings = $settingsGateway->getSettingsOnTab(Input::get("tab", "general"));
            foreach ($settings as $setting) {
                $value = Input::post($setting->id, "");
                if (trim($value) == "" && $setting->isPassword) {
                    continue;
                }
                $settingsGateway->update($setting->id, $value, $setting->isPassword);
            }
            return true;
        }
        return false;
    }
}