<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 */

namespace LePlugin\Core;

class View {

    private $_args;
    private $_filename;
    public static $_PLUGIN_VIEWS_FOLDERS = array();
    public $input;
    private $_notices = [];

    public function __get($name) {
        if (isset($this->_args[$name])) {
            return $this->_args[$name];
        }
        return null;
    }

    public function __construct(AbstractController $context, $viewFile, $pluginFolder = "") {
        $query_args = $_GET;
        $this->assign("query_args", $query_args);
        unset($query_args["tab"]);
        $this->assign("query_string", http_build_query($query_args));

        if (trim($pluginFolder)) {
            $this->_filename = $context::$_PLUGIN_VIEWS_FOLDERS[$pluginFolder] . $viewFile;
        } else {
            $this->_filename = $context->_views_dir . $viewFile;
        }
        $this->input = new Input();
    }

    public function assign($name, $value) {
        $this->_args[$name] = $value;
    }

    public function assignFromArray($array) {
        if (!is_array($array)) {
            return;
        }
        foreach ($array as $key => $value) {
            $this->assign($key, $value);
        }
    }

    public function display($displayBefore = true) {
        if (!file_exists($this->_filename)) {
            throw new \Exception("There is no such view $this->_filename!");
        }
        if ($displayBefore === true) {
            $this->displayNotices();
        }
        include_once $this->_filename;
        if ($displayBefore === false) {
            $this->displayNotices();
        }
    }

    public function getHtml($displayBefore = true) {
        ob_start();
        $this->display();
        $html = ob_get_clean();
        return $html;
    }

    public function addNotice($message, $type = 'updated', $inline = true) {
        $this->_notices[] = ["message" => $message, "type" => $type, "inline" => $inline];
    }

    public function displayNotices() {

        foreach ($this->_notices as $notice) {
            echo $this->buildNotice($notice["message"], $notice["type"], $notice["inline"]);
        }
    }

    private function buildNotice($message, $type = 'updated', $inline = true) {

        $notice = '<div class="' . $type . ' notice is-dismissible ' . ($inline ? 'inline' : '') . '">'
                . '<p><strong>' . $message . '</strong></p>'
                . '</div>';
        return $notice;
    }

}
