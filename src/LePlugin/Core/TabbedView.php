<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 */

namespace LePlugin\Core;

class TabbedView extends View {

    private $tabs = array();
    private $activeTab;

    public function __construct(AbstractController $context, $viewFile = "tabbed_content.php",
            $pluginFolder = "") {
        parent::__construct($context, $viewFile, $pluginFolder);
        $this->assign("container", $this);
        $this->setActive(Input::get("tab"));
    }

    public function addTab(Tab $tab) {
        $this->tabs[$tab->name] = $tab;
    }

    public function getTabs() {
        return $this->tabs;
    }

    public function setActive($name) {
        $this->activeTab = $name;
    }

    public function getActive() {
        if ($this->activeTab) {
            return $this->tabs[$this->activeTab];
        } else {
            return current($this->tabs);
        }
    }

    public function displayActive() {
        if ($this->activeTab) {
            $this->tabs[$this->activeTab]->display();
        } else {
            current($this->tabs)->display();
        }
    }

}
