<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 5/31/2016
 * Time: 12:15 AM
 */

namespace Multisoft\MPP\Replication;

use LePlugin\Core\GatewayInterface;
use LePlugin\Core\Utils;
use Multisoft\MPP\Core\CoreGateway;
use Multisoft\MPP\Settings\SettingsGateway;
use Multisoft\MPP\Core\CoreController;
use Katzgrau\KLogger\Logger;

class ReplicationGateway implements GatewayInterface
{
    const TRANS_REPLICATION_KEY = 'mpp_replication_info';
    const TRANS_CHECK_SITE_KEY = 'mpp_check_site';

    private static $instance;
    private $logger;

    private function __construct(Logger $logger = null)
    {
        if ($logger) {
            $this->logger = $logger;
        }
    }

    public static function getInstance($logger = null)
    {
        if (null === static::$instance) {
            static::$instance = new static($logger);
        }
        return static::$instance;
    }

    public function get_replication_site_name()
    {
        global $wpdb;

        $prefix = $wpdb->prefix;

        $original_site_url = $wpdb->get_var("SELECT option_value FROM {$prefix}options WHERE option_name = 'siteurl'");

        $original_site_url = Utils::str_replace_first('https://', '', Utils::str_replace_first('http://', '', strtolower($original_site_url)));

        $original_site_url = Utils::str_replace_first('www.', '', strtolower($original_site_url));

        $server_name = Utils::str_replace_first('www.', '', strtolower($_SERVER["HTTP_HOST"]));

        $xxx = str_replace('.', '', Utils::str_replace_first($original_site_url, '', $server_name));

        if (!strlen($xxx) || $xxx === 'localhost' || $xxx === 'www') {

            $xxx = "admin";

        }
        return $xxx;

    }

    public function get_replication_info($force_new = false)
    {
        /* @var $settingsGateway SettingsGateway */
        /* @var $coreGateway CoreGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $coreGateway = CoreGateway::getInstance();

        $mppe_domain = $settingsGateway->get(CoreController::OPTION_WEB_ADDRESS);
        $mppe_application_id = $settingsGateway->get(CoreController::OPTION_APPLICATION_ID);
        $mppe_web_path = $settingsGateway->get(CoreController::OPTION_WEB_PATH);
        $mppe_replication_path = $settingsGateway->get(ReplicationController::OPTION_REPLICATION_PATH);
        $mppe_replication_query_format = $settingsGateway->get(ReplicationController::OPTION_REPLICATION_PATH_QUERY_FORMAT);
        /* @var $logger \Katzgrau\KLogger\Logger */
        $logger = $this->logger;
        $logger->debug('Get Replication Info: ',
            array(
                'mppe_domain' => $mppe_domain,
                'mppe_application_id' => $mppe_application_id,
                'mppe_web_path' => $mppe_web_path,
                'mppe_replication_path' => $mppe_replication_path,
                'mppe_replication_query_format' => $mppe_replication_query_format
            )
        );

        $replication_info = array();

        if ($mppe_domain && $mppe_application_id && $mppe_web_path && $mppe_replication_path) {
            $subdomain = $this->get_replication_site_name();
            $replication_pulled = $coreGateway->isset_transient(self::TRANS_REPLICATION_KEY, $subdomain);
            if (!$replication_pulled || $force_new) {
                try {
                    $q_str = sprintf($mppe_replication_query_format, $mppe_application_id, $subdomain);
                    parse_str($q_str, $post_fields);
                    $url = $mppe_domain . $mppe_web_path . $mppe_replication_path;
                    $logger->info('Pulling replication info from: ' . $url);
                    $logger->debug('Post fields: ', $post_fields);
                    $data = wp_remote_post($url, array(
                        'method' => 'POST',
                        'timeout' => 45,
                        'redirection' => 5,
                        'httpversion' => '1.0',
                        'blocking' => true,
                        'headers' => array(),
                        'body' => $post_fields,
                        'cookies' => array()
                    ));
                    if (is_wp_error($data)) {
                        $error_message = $data->get_error_message();
                        $logger->critical($error_message);
                        $replication_info = $data;
                    } else {
                        if ($data['response']['code'] == '200') {
                            $logger->info("Successfully pulled replication info. Parsing replication data...");
                            $logger->debug("Received data: " . PHP_EOL . $data['body']);
                            $parser = xml_parser_create();
                            xml_parse_into_struct($parser, $data['body'], $values, $index);
                            xml_parser_free($parser);
                            $parsed_replication_info = array();
                            foreach ($values as $xml_child) {
                                if ($xml_child["level"] == 2) {
                                    $parsed_replication_info[$xml_child["tag"]] =
                                        isset($xml_child["value"]) ? $xml_child["value"] : "";
                                }
                            }
                            $logger->debug('Parsed replication info:', $parsed_replication_info);
                            if ($parsed_replication_info["DISTRIBUTORID"] != "00000000-0000-0000-0000-000000000000") {
                                $replication_info = $parsed_replication_info;
                                $coreGateway->set_transient(self::TRANS_REPLICATION_KEY, $replication_info, $subdomain);
                                $logger->info('Successfully set replication info in mpp transient.');
                            } else {
                                $logger->error('Retrieved DISTRIBUTORID is invalid . Replication info not set!');
                                $replication_info = new \WP_Error('mpp_invalid_distributor_id',
                                    'Retrieved DISTRIBUTORID is invalid ');
                            }
                        } else {
                            $logger->error("HTTP Error: ", $data['response']);
                            $replication_info = new \WP_Error($data['response']['code'], $data['response']['message']);
                        }
                    }
                } catch (\Exception $e) {
                    $logger->critical($e->getMessage());
                    $logger->debug($e->getTraceAsString());
                    $replication_info = new \WP_Error($e->getCode(), $e->getMessage());
                }
            } else {
                $replication_info = $coreGateway->get_transient(self::TRANS_REPLICATION_KEY, $subdomain);
                $logger->debug("Replication info already in transient.", array($subdomain, $replication_info));
            }
        } else {
            $logger->warning("MPP Web Address or Application ID or replication settings not set!");
            $replication_info = new \WP_Error("NOT_SET", "MPP Web Address or Application ID or replication settings not set.");
        }
        return $replication_info;
    }

    public function check_site_name($site_name, $force_new = false)
    {
        $result = false;
        if ($site_name) {
            /* @var $settingsGateway SettingsGateway */
            /* @var $coreGateway CoreGateway */
            $settingsGateway = SettingsGateway::getInstance();
            $coreGateway = CoreGateway::getInstance();

            $mppe_domain = $settingsGateway->get(CoreController::OPTION_WEB_ADDRESS);
            $mppe_application_id = $settingsGateway->get(CoreController::OPTION_APPLICATION_ID);
            $mppe_web_path = $settingsGateway->get(CoreController::OPTION_WEB_PATH);
            $mppe_check_site_path = $settingsGateway->get(ReplicationController::OPTION_CHECK_SITE_NAME_PATH);
            $mppe_check_site_path_qf = $settingsGateway->get(ReplicationController::OPTION_CHECK_SITE_NAME_PATH_QUERY_FORMAT);

            /* @var $logger \Katzgrau\KLogger\Logger */
            $logger = $this->logger;
            $logger->debug('Check Site Name: ',
                array(
                    'site_name' => $site_name,
                    'mppe_domain' => $mppe_domain,
                    'mppe_application_id' => $mppe_application_id,
                    'mppe_web_path' => $mppe_web_path,
                    'mppe_check_site_path' => $mppe_check_site_path,
                    'mppe_check_site_path_qf' => $mppe_check_site_path_qf
                )
            );
            if ($mppe_domain && $mppe_application_id && $mppe_web_path && $mppe_check_site_path) {
                $checked = $coreGateway->isset_transient(self::TRANS_CHECK_SITE_KEY, $site_name);
                if (!$checked || $force_new) {
                    try {
                        $q_format = sprintf($mppe_check_site_path_qf, $mppe_application_id, $site_name);
                        parse_str($q_format, $post_fields);
                        $url = $mppe_domain . $mppe_web_path . $mppe_check_site_path;
                        $logger->info('Checking replication site name from: ' . $url);
                        $logger->debug('Post fields: ', $post_fields);

                        $data = wp_remote_post($url, array(
                            'method' => 'POST',
                            'timeout' => 45,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking' => true,
                            'headers' => array(),
                            'body' => $post_fields,
                            'cookies' => array()
                        ));
                        if (is_wp_error($data)) {
                            $error_message = $data->get_error_message();
                            $logger->critical($error_message);
                            $result = $data;
                        } else {
                            if ($data['response']['code'] == '200') {
                                $logger->info("Successfully pulled check site result.");
                                $logger->debug("Received data: " . PHP_EOL . $data['body']);

                                $parser = xml_parser_create();
                                xml_parse_into_struct($parser, $data['body'], $values, $index);
                                xml_parser_free($parser);

                                foreach ($values as $xml_child) {
                                    if ($xml_child['tag'] == "SUCCESS") {
                                        if ($xml_child['value'] === 'true') {
                                            $result = true;
                                        };
                                    }
                                }
                                if ($result) {
                                    $logger->info("Site name is existent: " . $site_name);
                                } else {
                                    $logger->info("Site name is non-existent: " . $site_name);
                                }

                                $coreGateway->set_transient(self::TRANS_CHECK_SITE_KEY, $result, $site_name);
                                $logger->info('Successfully set check site info info in mpp transient.');
                            } else {
                                $logger->error("HTTP Error: ", $data['response']);
                                $result = new \WP_Error($data['response']['code'], $data['response']['message']);
                            }
                        }
                    } catch (\Exception $e) {
                        $logger->critical($e->getMessage());
                        $logger->debug($e->getTraceAsString());
                        $result = new \WP_Error($e->getCode(), $e->getMessage());
                    }
                } else {
                    $result = $coreGateway->get_transient(self::TRANS_CHECK_SITE_KEY, $site_name);
                    $logger->debug("Check site name already in transient: ", array($result));
                }
            } else {
                $result = true;
                $logger->warning("MPP Web Address or Application ID or check site name settings not set!");
            }
        }
        return $result;
    }

}