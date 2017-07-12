<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 5/27/2016
 * Time: 11:32 PM
 */

namespace Multisoft\MPP\Replication;

use LePlugin\Core\AbstractController;
use Multisoft\MPP\Core\CoreController;
use Multisoft\MPP\Settings\SettingsGateway;
use LePlugin\Core\View;
use LePlugin\Core\Utils;
use Katzgrau\KLogger\Logger;
use Psr\Log\LogLevel;

class ReplicationController extends AbstractController
{

    const MENU_SLUG = 'replication';
    const SECTION_MPP_REPLICATION = 'mpp_replication_section';
    const OPTION_NON_EXIST_REDIRECTION = 'nonexistent_site_redirection';
    const OPTION_AUTOPREFIX = 'autoprefix';
    const OPTION_REPLICATION_PATH = 'replication_path';
    const OPTION_REPLICATION_PATH_QUERY_FORMAT = 'replication_path_query_format';
    const OPTION_CHECK_SITE_NAME_PATH = 'check_site_name_path';
    const OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT = 'check_site_name_path_query_format';
    private $replicationGateway;
    private $mpp_error_view;

    protected function setup()
    {
        $this->enable_activation_hook();
        $this->setup_settings();
        $this->add_action('init', 'mpp_replication_check_site_name');
        $this->add_action('admin_notices', 'mppe_replication_message');
        $this->add_shortcode('mppe', 'mppe_replication_info');
        if (!defined('WP_HOME')) {
            $this->add_filter('option_home', 'replication_home_url');
        }
        if (!defined('WP_SITEURL')) {
            $this->add_filter('option_siteurl', 'replication_site_url');
        }

        if (MPP_DEBUG) {
            $logger = new Logger($this->_plugin_dir . '/logs', LogLevel::DEBUG, array(
                'extension' => 'log',
                'prefix' => 'mpp_replication_debug_'
            ));
        } else {
            $logger = new Logger($this->_plugin_dir . '/logs', LogLevel::INFO, array(
                'extension' => 'log',
                'prefix' => 'mpp_replication_'
            ));
        }
        $this->replicationGateway = ReplicationGateway::getInstance($logger);

        $this->add_submenu_page(
            CoreController::MENU_SLUG,
            "Multisoft MarketPowerPRO Replication Shortcodes",
            "Replication Shortcodes",
            CoreController::CAP,
            self::MENU_SLUG,
            [$this, 'index']
        );
        if (!is_admin()) {
            ob_start();
            add_action('shutdown', function () {
                $final = '';
                $levels = ob_get_level();

                for ($i = 0; $i < $levels; $i++) {
                    $final .= ob_get_clean();
                }
                echo apply_filters('final_output', $final);
            }, 0);
            $this->add_filter('final_output', 'mppe_autoprefix_content');
        }

    }

    public function activate()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $default_replication_path = $this->_config->__get('default_multisoft_mpp_replication_path');
        if ($default_replication_path) {
            $settingsGateway->update(
                self::OPTION_REPLICATION_PATH,
                $default_replication_path
            );
        }
        $default_replication_path_qf = $this->_config->__get('default_multisoft_mpp_replication_query_format');
        if ($default_replication_path_qf) {
            $settingsGateway->update(
                self::OPTION_REPLICATION_PATH_QUERY_FORMAT,
                $default_replication_path_qf
            );
        }
        $default_check_site_name_path = $this->_config->__get('default_multisoft_mpp_check_site_name_path');
        if ($default_check_site_name_path) {
            $settingsGateway->update(
                self::OPTION_CHECK_SITE_NAME_PATH,
                $default_check_site_name_path
            );
        }
        $default_check_site_name_path_qf = $this->_config->__get('default_multisoft_mpp_check_site_name_query_format');
        if ($default_check_site_name_path_qf) {
            $settingsGateway->update(
                self::OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT,
                $default_check_site_name_path_qf
            );
        }

        $settingsGateway->update(self::OPTION_AUTOPREFIX, true);
    }

    private function setup_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $settingsGateway->addSettingsSection(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_REPLICATION,
            'Replication Settings',
            [$this, "mpp_replication_general_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_NON_EXIST_REDIRECTION
        );
        $settingsGateway->registerSetting(
            $settingsGateway::GENERAL_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_AUTOPREFIX
        );

        $settingsGateway->addSettingsSection(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            'Replication Settings',
            [$this, "mpp_replication_advanced_settings"]
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_REPLICATION_PATH
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_REPLICATION_PATH_QUERY_FORMAT
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_CHECK_SITE_NAME_PATH
        );
        $settingsGateway->registerSetting(
            $settingsGateway::ADVANCED_TAB,
            self::SECTION_MPP_REPLICATION,
            self::OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT
        );
    }

    public function index()
    {
        $view = new View($this, 'mpp/replication/shortcodes.php');
        $sample_content = "[mppe]Hello my name is MPPE_FIRSTNAME, " .
            "I live in MPPE_ADDRESS1 MPPE_ADDRESS2 MPPE_CITY, MPPE_COUNTRYNAME MPPE_POSTALCODE[/mppe]!";
        $parsed = do_shortcode($sample_content);
        $view->assign('sample', $sample_content);
        $view->assign('parsed_sample', $parsed);
        $view->display();
    }

    public function replication_site_url($url)
    {
        if (is_admin()) {
            return $url;
        }
        $href = parse_url($url);
        return $href['scheme'] . '://' . $_SERVER['HTTP_HOST'];
    }

    public function replication_home_url($url)
    {
        if (is_admin()) {
            return $url;
        }
        $href = parse_url($url);
        return $href['scheme'] . '://' . $_SERVER['HTTP_HOST'];
    }

    public function mppe_autoprefix_content($output)
    {
        require_once $this->_plugin_dir . '/src/Multisoft/MPP/Replication/simple_html_dom.php';
        /* @var $replicationGateway ReplicationGateway */
        /* @var $settingsGateway SettingsGateway */
        $replicationGateway = $this->replicationGateway;
        $settingsGateway = SettingsGateway::getInstance();
        $autoprefix = $settingsGateway->get(self::OPTION_AUTOPREFIX);

        $xxx = $replicationGateway->get_replication_site_name();
        $host = Utils::get_domain(site_url());
        $web_address_host = Utils::get_domain($settingsGateway->get(CoreController::OPTION_WEB_ADDRESS));
        $html = str_get_html($output);
        if ($html) {
            foreach ($html->find('a') as $element) {
                $href = parse_url($element->href);
                $element_host = Utils::get_domain($href['host']);
                if (strtolower($element_host) == strtolower($host) ||
                    strtolower($element_host) == strtolower($web_address_host)
                ) {
                    $url_splice = explode('.', $href['host']);
                    $site_name = $url_splice[0];
                    if (strtolower($site_name) === 'www') {
                        $site_name = $url_splice[1];
                    }
                    if ($autoprefix && $site_name !== $xxx) {
                        $new_href = $href['scheme'] . '://' . $xxx . '.' .
                            Utils::str_replace_first($href['scheme'] . '://', '', $element->href);
                        $element->href = $new_href;
                    } else if (!$autoprefix && $site_name === $xxx) {
                        $new_href = Utils::str_replace_first($xxx . '.', '', $element->href);
                        $element->href = $new_href;
                    }
                }
            }
        }
        return $html;
    }

    public function mppe_replication_info($atts, $content = null)
    {
        /* @var $replicationGateway ReplicationGateway */

        $replicationGateway = ReplicationGateway::getInstance();

        $info = false;

        extract(shortcode_atts(array(
            "info" => false
        ), $atts));

        $mppe_info = $replicationGateway->get_replication_info(MPP_DEBUG);
        if (is_wp_error($mppe_info)) {
            $error_view = new View($this, 'mpp/error_view.php');
            $error_view->assign('code', $mppe_info->get_error_code());
            $error_view->assign('message', $mppe_info->get_error_message());
            $content = $error_view->getHtml() . $content;
        } else if ($mppe_info) {
            if ($info) {
                $info = str_replace("MPPE_", "", str_replace("&quot;", "", $info));
                return $mppe_info[strtoupper($info)];
            }
            if ($content) {
                foreach ($mppe_info as $key => $value) {
                    $content = str_replace("MPPE_" . $key, $value, $content);
                }
            }
        }

        return $content;
    }

    public function mpp_replication_check_site_name()
    {
        /* @var $replicationGateway ReplicationGateway */
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $replicationGateway = $this->replicationGateway;
        $site_name = $replicationGateway->get_replication_site_name();
        $valid = $replicationGateway->check_site_name($site_name);
        $this->mpp_error_view = new View($this, '');
        if (is_wp_error($valid)) {
            /* @var $valid \WP_Error */
            $this->mpp_error_view->addNotice(
                'Multisoft MarketPowerPRO ' .
                $valid->get_error_code() . ': ' . $valid->get_error_message(),
                'notice-error', false
            );
        } else if ($valid === false) {
            $redirection_page = $settingsGateway->get(self::OPTION_NON_EXIST_REDIRECTION);
            if ($redirection_page) {
                wp_redirect(get_permalink($redirection_page));
                exit();
            } else {
                if (is_admin()) {
                    $this->mpp_error_view->addNotice(
                        'Multisoft MarketPowerPRO INVALID_SITE_NAME: ' .
                        'Invalid/non-existent replication site name: ' . $site_name,
                        'notice-error', false
                    );
                } else {
//                    No more redirect on non-existing subdomain. Just display the page
//                    wp_redirect(Utils::getMainDomain());
//                    exit();
                }
            }
        }
    }

    public function mppe_replication_message()
    {
        if ($this->mpp_error_view) {
            /* @var $errorView \LePlugin\Core\View */
            $errorView = $this->mpp_error_view;
            $errorView->displayNotices();
        }
    }

    public function mpp_replication_general_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $replicationView = new View($this, 'mpp/replication/general_settings_section.php');
        $replicationView->assign('page_drop_down_args', array(
            'depth' => 0,
            'child_of' => 0,
            'selected' => $settingsGateway->get(self::OPTION_NON_EXIST_REDIRECTION, 0),
            'echo' => 1,
            'name' => self::OPTION_NON_EXIST_REDIRECTION,
            'id' => null,
            'class' => null,
            'show_option_none' => "Select Page",
            'show_option_no_change' => null,
            'option_none_value' => null
        ));
        $replicationView->assign('autoprefix', $settingsGateway->get(
            self::OPTION_AUTOPREFIX, false
        ));
        $replicationView->display();
    }

    public function mpp_replication_advanced_settings()
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $replicationView = new View($this, 'mpp/replication/advanced_settings_section.php');
        $replicationView->assign('replication_path',
            $settingsGateway->get(self::OPTION_REPLICATION_PATH, ''));
        $replicationView->assign('replication_path_query_format',
            $settingsGateway->get(self::OPTION_REPLICATION_PATH_QUERY_FORMAT, ''));
        $replicationView->assign('check_site_name_path',
            $settingsGateway->get(self::OPTION_CHECK_SITE_NAME_PATH, ''));
        $replicationView->assign('check_site_name_path_query_format',
            $settingsGateway->get(self::OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT, ''));
        $replicationView->display();
    }


}