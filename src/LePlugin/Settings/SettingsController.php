<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 * The main settings controller
 */

namespace LePlugin\Settings;

use LePlugin\Core\AbstractController;
use LePlugin\Core\CoreController;
use LePlugin\Core\Input;
use LePlugin\Core\Tab;
use LePlugin\Core\View;

class SettingsController extends AbstractController {

    protected $parent_slug;
    protected $menu_slug;
    public static $_TABS = [];
    private $settingsGateway;

    protected function setup() {
        $this->enable_activation_hook();
        $this->enable_deactivation_hook();
//        $this->parent_slug = CoreController::MENU_SLUG;
//        $this->menu_slug = CoreController::MENU_SLUG . "/settings";
//        $this->add_submenu_page($this->parent_slug, "Contacted Settings", "Settings",
//                self::MANAGE_CAP, $this->menu_slug, array($this, "index_page"));

        $this->settingsGateway = new SettingsGateway();
    }

    public static function addSettingsTab(Tab $tab) {
        self::$_TABS[$tab->name] = $tab;
    }

//    public function index_page() {
//        $headerView = new View($this, "admin_head.php");
//        $headerView->assign("title", "Contacted Settings");
//        $footerView = new View($this, "admin_foot.php");
//
//        $tabContainer = new SettingsTabbedView($this);
//        foreach (SettingsGateway::$_tabs as $tab) {
//            $tabContainer->addTab($tab);
//        }
//        $tabContainer->assign("nonce_action", "ccc2_update_settings");
//
//        $submitted = $this->process_submit();
//        if ($submitted) {
//            $tabContainer->addNotice("Settings Updated.");
//        }
//        $headerView->display();
//        $tabContainer->display();
//        $footerView->display();
//    }

    private function process_submit() {
        $nonce = Input::post("_wpnonce");
        if (wp_verify_nonce($nonce, "ccc2_update_settings")) {
            $settings = $this->settingsGateway->getSettingsOnTab(Input::get("tab", "general"));
            foreach ($settings as $setting) {
                $value = Input::post($setting->id, "");
                //no update if blank and empty
                if (trim($value) == "" && $setting->isPassword) {
                    continue;
                }
                $this->settingsGateway->update($setting->id, $value, $setting->isPassword);
            }
            return true;
        }
        return false;
    }

//    public function activate() {
//        $this->add_capability("administrator", self::MANAGE_CAP);
//    }
//
//    public function deactivate() {
//        $this->remove_capability("administrator", self::MANAGE_CAP);
//    }
}
