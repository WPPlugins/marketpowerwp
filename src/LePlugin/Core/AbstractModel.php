<?php

namespace LePlugin\Core;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */
abstract class AbstractModel {

    public function __construct($array_data = array()) {
        foreach ($array_data as $key => $value) {
            $this->$key = $value;
        }
    }

}
