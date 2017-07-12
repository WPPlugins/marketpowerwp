<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */

namespace LePlugin\Settings;

use LePlugin\Core\Tab;

class SettingsTab extends Tab {

    public $sections = [];

    public function addSection(SettingsSection $section) {
        $this->sections[$section->id] = $section;
    }

    public function hasSection($sectionId) {
        return isset($this->sections[$sectionId]);
    }

    public function display() {
        foreach ($this->sections as $section) {
            $section->display();
        }
    }

}
