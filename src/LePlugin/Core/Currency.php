<?php

namespace LePlugin\Core;

use LePlugin\Settings\Settings;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */
class Currency {

    const RIGHT = "RIGHT";
    const LEFT = "LEFT";
    const DEFAULT_SYMBOL = "USD";

    public static function format($amount) {
        //format based on settings
        $position = Settings::get("currency_position", self::RIGHT);
        $symbol = Settings::get("currency_symbol", self::DEFAULT_SYMBOL);
        $decimal = number_format($amount, 2);
        if ($position === self::RIGHT) {
            return "$decimal $symbol";
        } else {
            return "$symbol $decimal";
        }
    }

}
