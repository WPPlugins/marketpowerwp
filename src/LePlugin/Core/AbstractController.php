<?php

namespace LePlugin\Core;

use Exception;
use LePlugin\Core\WpdbSqlRunner;

/**
 * Based on the modeler plugin. Serves as the abstract controller for all plugins extenting this class
 * This is where wordpress specific methods should be called only. If not possible, abstract in another class.
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>, Rodine Mark Paul L. Villar <dean.villar@gmail.com>
  @copyright Les Coders
 */
abstract class AbstractController
{

    protected $_config;
    protected $_plugin_dir;
    protected $_plugin_dir_url;
    protected $_plugin_file;
    private $_sql_dir;
    private $_sql_patches_dir;
    public $_views_dir;

    //following the modeler
    const SQL_FOLDER = "sql";
    const SQL_PATCHES_FOLDER = "patches";
    const SQL_PATCH_STRUCTURE = "structure";
    const SQL_PATCH_DATA = "data";
    const VIEWS_FOLDER = "views";
    const LOAD_ON_SLUG = "[none]";

    //protected static $_PLUGIN_VIEWS_FOLDERS = array();
    private $menus = [];
    private $submenus = [];
    private $menu_hooks = [];
    private $submenu_hooks = [];
    private $admin_css = [];
    private $admin_js = [];
    private $admin_js_deps = [];
    private $public_css = [];
    private $public_js = [];
    private $public_js_deps = [];
    public static $_PLUGIN_VIEWS_FOLDERS = [];

    abstract protected function setup();

    //just to avoid using the keyword new outside without assignment.
    public static function instance($plugin_dir, $plugin_file, Config $config = null)
    {

        return new static($plugin_dir, $plugin_file, $config);
    }

    public function __construct($plugin_dir, $plugin_file, Config $config = null)
    {
//        $class_info = new ReflectionClass($this);
//        $plugin_dir = dirname($class_info->getFileName());
//        $plugin_file = plugin_basename($class_info->getFileName());
        //remove trailing slashes because others may use dirname or plugin_dir to get plugin dir
        $plugin_dir = rtrim($plugin_dir, "\\");
        $plugin_dir = rtrim($plugin_dir, "/");
        $this->_plugin_dir = $plugin_dir . DIRECTORY_SEPARATOR; //we add the slash!
        $this->_plugin_file = $plugin_file;
        $this->_config = $config;
        $this->_plugin_dir_url = plugin_dir_url($this->_plugin_file);

        $this->_sql_dir = $this->_plugin_dir . self::SQL_FOLDER . DIRECTORY_SEPARATOR; //we add the slash!
        $this->_sql_patches_dir = $this->_sql_dir . self::SQL_PATCHES_FOLDER . DIRECTORY_SEPARATOR;

        $this->_views_dir = $this->_plugin_dir . self::VIEWS_FOLDER . DIRECTORY_SEPARATOR;
        $plugin_folder_name = str_replace(pathinfo($plugin_file, PATHINFO_BASENAME), "",
            $plugin_file);
        $plugin_folder_name = trim($plugin_folder_name, "\\");
        $plugin_folder_name = trim($plugin_folder_name, "/");
        self::$_PLUGIN_VIEWS_FOLDERS[$plugin_folder_name] = $this->_views_dir;
        $this->setup();
        //if menus 
        if (count($this->menus) > 0 || count($this->submenus) > 0) {
            $this->add_action("admin_menu", "admin_menu", 9);
        }
        if (count($this->admin_css) > 0 || count($this->admin_js) > 0) {
            $this->add_action('admin_enqueue_scripts', 'admin_enqueue_scripts');
        }
        if (count($this->public_css) > 0 || count($this->public_js) > 0) {
            $this->add_action('wp_enqueue_scripts', 'public_enqueue_scripts');
        }
    }

    //helper functions for setting up plugins
    //common plugin functions: activate, deactivate,
    //admin menu, submenu
    //admin js, admin css,

    public final function enable_activation_hook()
    {
        register_activation_hook($this->_plugin_file, array($this, "activate"));
    }

    public final function enable_deactivation_hook()
    {
        register_deactivation_hook($this->_plugin_file, array($this, "deactivate"));
    }

    public final function add_init($function = "init")
    {
        $this->add_action("init", $function);
    }

    public final function add_menu_page($page_title, $menu_title, $capability, $menu_slug,
                                        $function, $icon_url = '', $position = null)
    {
        $this->menus[$menu_slug] = func_get_args();
    }

    public final function add_submenu_page($parent_slug, $page_title, $menu_title, $capability,
                                           $menu_slug, $function = '')
    {
        $this->submenus[$menu_slug] = func_get_args();
    }

    public final function admin_menu()
    {
        foreach ($this->menus as $slug => $menu_args) {
            $this->menu_hooks[$slug] = call_user_func_array("add_menu_page", $menu_args);
        }
        foreach ($this->submenus as $slug => $menu_args) {
            $this->submenu_hooks[$slug] = call_user_func_array("add_submenu_page", $menu_args);
        }
    }

    protected final function add_admin_css($filename, $loadOnSlug = self::LOAD_ON_SLUG)
    {
        $this->admin_css[$filename] = $loadOnSlug;
    }

    protected final function add_admin_js($filename, $loadOnSlug = self::LOAD_ON_SLUG, $deps = [])
    {
        $this->admin_js[$filename] = $loadOnSlug;
        $this->admin_js_deps[$filename] = $deps;
    }

    protected final function add_public_css($filename)
    {
        $this->public_css[$filename] = self::LOAD_ON_SLUG;
    }

    protected final function add_public_js($filename, $deps = [])
    {
        $this->public_js[$filename] = self::LOAD_ON_SLUG;
        $this->public_js_deps[$filename] = $deps;
    }

    public final function admin_enqueue_scripts($hook)
    {
        foreach ($this->admin_css as $filename => $loadOnSlug) {

            if ($loadOnSlug === self::LOAD_ON_SLUG) {
                $this->enqueue_style($filename, $filename);
                continue;
            }
            $loadOnHook = "";
            if (isset($this->menu_hooks[$loadOnSlug])) {
                $loadOnHook = $this->menu_hooks[$loadOnSlug];
            } else if (isset($this->submenu_hooks[$loadOnSlug])) {
                $loadOnHook = $this->submenu_hooks[$loadOnSlug];
            }
            if ($hook == $loadOnHook) {

                $this->enqueue_style($filename, $filename);
            }
        }
        foreach ($this->admin_js as $filename => $loadOnSlug) {
            if ($loadOnSlug === self::LOAD_ON_SLUG) {
                $this->enqueue_script($filename, $filename, $this->admin_js_deps[$filename]);
                continue;
            }
            $loadOnHook = "";
            if (isset($this->menu_hooks[$loadOnSlug])) {
                $loadOnHook = $this->menu_hooks[$loadOnSlug];
            } else if (isset($this->submenu_hooks[$loadOnSlug])) {
                $loadOnHook = $this->submenu_hooks[$loadOnSlug];
            }
            if ($hook == $loadOnHook) {
                $this->enqueue_script($filename, $filename, $this->admin_js_deps[$filename]);
            }
        }
    }

    public final function public_enqueue_scripts()
    {
        foreach ($this->public_css as $filename => $loadOnSlug) {
            $this->enqueue_style($filename, $filename);
        }
        foreach ($this->public_js as $filename => $loadOnSlug) {
            $this->enqueue_script($filename, $filename, $this->public_js_deps[$filename]);
        }
    }

    public function activate()
    {
        throw new Exception("Function activate should be implemented!");
    }

    public function deactivate()
    {
        throw new Exception("Function deactivate should be implemented!");
    }

    //HELPER FUNCTIONS
    protected final function add_cron($function = '', $priority = 10, $accepted_args = 1)
    {
        $this->add_action("le_cron", $function, $priority, $accepted_args);
    }

    protected final function add_action($action, $function = '', $priority = 10, $accepted_args = 1)
    {
        add_action($action, array($this, $function == '' ? $action : $function), $priority,
            $accepted_args);
    }

    protected final function add_filter($filter, $function, $priority = 10, $accepted_args = 1)
    {
        add_filter($filter, array($this, $function == '' ? $filter : $function), $priority,
            $accepted_args);
    }

    protected final function remove_filter($filter, $function)
    {
        remove_filter($filter, array($this, $function == '' ? $filter : $function));
    }

    protected final function add_shortcode($tag, $function)
    {
        add_shortcode($tag, array($this, $function));
    }

    protected final function enqueue_style($name, $file_name)
    {
        wp_register_style($name, $this->_plugin_dir_url . "css/" . $file_name);
        wp_enqueue_style($name);
    }

    protected final function enqueue_script($name, $file_name, $deps = [])
    {
        wp_enqueue_script($name,
            $this->_plugin_dir_url . DIRECTORY_SEPARATOR . "js/" . DIRECTORY_SEPARATOR . $file_name,
            $deps);
    }

    protected final function run_sql($sql_filename)
    {
        $filename = $this->_sql_dir . $sql_filename;
        $sqlRunner = new WpdbSqlRunner();
        $sqlRunner->execute($filename);
    }

    protected final function run_patches()
    {
        //TODO
    }

    protected final function add_role($role, $display_name, $capabilities = array()) {
        return add_role($role, $display_name, $capabilities);
    }

    protected final function add_capability($role, $cap, $grant = true) {
        $r = get_role($role);
        if ($r === null) {
            throw new Exception("There is no such role $role!");
        }
        $r->add_cap($cap, $grant);
    }

    protected final function remove_capability($role, $cap)
    {
        $r = get_role($role);
        if ($r === null) {
            throw new Exception("There is no such role $role!");
        }
        $r->remove_cap($cap);
    }

    protected final function display_view($view, $args = array())
    {
        $filename_variable_to_avoid_collision_from_extracting_args__hopefully = $this->_views_dir . $view;
        if (!file_exists($filename_variable_to_avoid_collision_from_extracting_args__hopefully)) {
            throw new Exception("There is no such view $filename_variable_to_avoid_collision_from_extracting_args__hopefully!");
        }
        extract($args);
        include_once $filename_variable_to_avoid_collision_from_extracting_args__hopefully;
    }

    protected final function display_view_from($from_folder, $view, $args = array())
    {
        if (!isset(self::$_PLUGIN_VIEWS_FOLDERS[$from_folder])) {
            throw new Exception("There is no such folder view $from_folder");
        }
        $filename_variable_to_avoid_collision_from_extracting_args__hopefully = self::$_PLUGIN_VIEWS_FOLDERS[$from_folder] . $view;
        if (!file_exists($filename_variable_to_avoid_collision_from_extracting_args__hopefully)) {
            throw new Exception("There is no such view $filename_variable_to_avoid_collision_from_extracting_args__hopefully!");
        }
        extract($args);
        include_once $filename_variable_to_avoid_collision_from_extracting_args__hopefully;
    }

}
