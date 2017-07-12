<?php

namespace LePlugin\Core;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 */
class Config
{

    protected $config;

    public function __construct(array $config)
    {
        if (is_array($config)) {
            $temp_config = array();
            foreach ($config as $config_item) {
                $config_item = (array)$config_item;
                $temp_config[$config_item['key']] = $config_item['value'];
            }
            $this->config = $temp_config;
        } else if (is_object($config)) {
            $this->config = $config;
        }
    }

    public function __get($name)
    {
        $value = false;
        if (array_key_exists($name, $this->config)) {
            $value = $this->config[$name];
        }

        return $value;
    }

}
