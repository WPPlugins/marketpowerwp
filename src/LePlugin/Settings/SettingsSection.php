<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */

namespace LePlugin\Settings;

class SettingsSection {

    public $id;
    public $title;
    private $displayCallback;
    private $headerCallback;
    public $settings = [];

    public function __construct($id, $title, $displayCallback, $headerCallback = "") {
        $this->id = $id;
        $this->title = $title;
        $this->displayCallback = $displayCallback;
        $this->headerCallback = $headerCallback;
    }

    public function registerSetting($id, $isPassword = false) {
        $this->settings[] = new Setting($id, $isPassword);
    }

    public function display() {
        if (is_callable($this->headerCallback)) {
            call_user_func($this->headerCallback);
        } else {
            //TODO: make view?
            echo '<h2>' . $this->title . '</h2>';
        }
        call_user_func($this->displayCallback);
    }

}
