<?php

namespace LePlugin\Core;

use WP_REST_Server;

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
@copyright Les Coders
 */
class Rest_Server extends WP_REST_Server {

    public function serve_request($path = null) {
        $html = apply_filters('leplugin_rest_serve_as_html', false);
        if ($html == false) {
            parent::serve_request($path);
        }
    }

}
