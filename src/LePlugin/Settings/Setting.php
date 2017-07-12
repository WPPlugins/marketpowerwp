<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 * Wrapper for add_settings_field
 */

namespace LePlugin\Settings;

/**
 * Description of SettingsField
 *
 * @author Dexter Campos
 */
class Setting {

    public $id;
    public $isPassword = false;

    public function __construct($id, $isPassword = false) {
        $this->id = $id;
        $this->isPassword = $isPassword;
    }

}
