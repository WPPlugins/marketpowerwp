<?php

namespace LePlugin\Core;

use WP_List_Table;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 */
class ListTable extends WP_List_Table {

    protected $notices = [];

    public function __construct($args = array()) {
        parent::__construct($args);
        $this->save_per_page();
    }

    public function save_per_page($option = null) {
        if (!$option) {
            $option = $this->_args["singular"] . "_per_page";
        }
        $value = Input::get("per_page", Input::post("per_page"));
        if ($value) {
            update_user_option(get_current_user_id(), $option, $value);
        }
    }

    public function get_items_per_page($option = null, $default = 20) {
        if (!$option) {
            $option = $this->_args["singular"] . "_per_page";
        }
        return parent::get_items_per_page($option, $default);
    }

    protected function get_input_display_wrapper($item, $input, $display) {
        $displayStyle = 'style="display:none;"';
        $inputStyle = '';
        $inputWrapper = '<div ' . $inputStyle . ' class="input_wrapper">' . $input . '</div>';
        $displayWrapper = '<div ' . $displayStyle . ' class="display_wrapper">' . $display . '</div>';
        return $inputWrapper . $displayWrapper;
    }

    protected function addNotice($message, $type = 'updated', $inline = true) {
        $this->notices[] = ["message" => $message, "type" => $type, "inline" => $inline];
    }

    public function displayNotices() {
        foreach ($this->notices as $notice) {
            echo Utils::buildNotice($notice["message"], $notice["type"], $notice["inline"]);
        }
    }

}
