<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 */

namespace LePlugin\Settings;

use LePlugin\Core\AbstractController;
use LePlugin\Core\TabbedView;

class SettingsTabbedView extends TabbedView
{

    public function __construct(AbstractController $context)
    {
        parent::__construct($context, "tabbed_content_settings.php");
        $this->assign("container", $this);
    }

}
