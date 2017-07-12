<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/3/2016
 * Time: 10:54 PM
 */

namespace Multisoft\MPP\Core;

use LePlugin\Core\GatewayInterface;
use Multisoft\MPP\Settings\SettingsGateway;

class CoreGateway implements GatewayInterface
{
    const TRANS_EXPIRE = 3600; //1 hour
    private static $instance;

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function set_transient($key, $value, $index = false)
    {
        $map = get_transient($key);
        $map = $map ? $map : array();
        if ($index) {
            $map[$index] = $value;
        } else {
            $map = $value;
        }
        return set_transient($key, $map, self::TRANS_EXPIRE);
    }

    public function isset_transient($key, $index = false)
    {
        $map = get_transient($key);
        if ($map) {
            if ($index) {
                return isset($map[$index]);
            } else {
                return true;
            }
        }
        return false;
    }

    public function get_transient($key, $index = false)
    {
        if ($this->isset_transient($key, $index)) {
            $map = get_transient($key);
            if ($index) {
                return $map[$index];
            } else {
                return $map;
            }
        }
        return false;
    }

    public function delete_transient($key, $index = false)
    {
        if ($this->isset_transient($key, $index)) {
            if ($index) {
                $map = get_transient($key);
                unset($map[$index]);
                $this->set_transient($key, $map, $index);
            } else {
                delete_transient($key);
            }
        }
    }

    public function getResourceUrl($key)
    {
        /* @var $settingsGateway SettingsGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $base_mpp_address = $settingsGateway->get(
            CoreController::OPTION_WEB_ADDRESS
        );
        $mpp_web_path = $settingsGateway->get(
            CoreController::OPTION_WEB_PATH
        );
        $keyValue = $settingsGateway->get(
            $key
        );
        return $base_mpp_address . $mpp_web_path . $keyValue;
    }
}