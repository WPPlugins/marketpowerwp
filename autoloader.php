<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */
if (!function_exists('get_plugins')) {
    require ABSPATH . 'wp-admin/includes/plugin.php';
}
spl_autoload_register('le_plugin_src_autoloader');

function le_plugin_src_autoloader($className) {
    $include_dir = "src";
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $plugin_files = array_keys(get_plugins());
    foreach ($plugin_files as $plugin_file) {
        //search lecode init files
        $lecode_file = realpath(plugin_dir_path(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_file)) . DIRECTORY_SEPARATOR . "leplugin.php";
        if (file_exists($lecode_file)) {
            $realpath = realpath(plugin_dir_path(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_file)) . DIRECTORY_SEPARATOR . $include_dir . DIRECTORY_SEPARATOR;
            if (file_exists($realpath . $fileName)) {
                require $realpath . $fileName;
                return true;
            }
        }
    }
    return false;
}
