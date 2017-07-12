<?php

namespace LePlugin\Core;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
  @copyright Les Coders
 */
class CoreController extends AbstractController {

    protected function setup() {
        $this->add_admin_js("leplugin.phpvars.php", self::LOAD_ON_SLUG);
        $this->add_admin_js("leplugin.js", self::LOAD_ON_SLUG, ["jquery-ui-dialog"]);
        $this->add_admin_css("leplugin.css");
        //TODO: select theme from settings
        wp_enqueue_style("leplugin-jquery-ui-css",
                "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css");
        wp_enqueue_style("leplugin-font-awesome-css",
                "https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css");
    }

}
