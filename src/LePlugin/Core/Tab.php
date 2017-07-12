<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 */

namespace LePlugin\Core;

class Tab {

    public $name;
    public $display;
    private $contentCallback;
    private $callbackArgs;

    public function __construct($name, $display, $contentCallback = false, $callbackArgs = []) {
        $this->name = $name;
        $this->display = $display;
        $this->contentCallback = $contentCallback;
        $this->callbackArgs = $callbackArgs;
    }

    public function display() {
        if ($this->contentCallback !== false) {
            call_user_func($this->contentCallback, $this->callbackArgs);
        }
    }

}
