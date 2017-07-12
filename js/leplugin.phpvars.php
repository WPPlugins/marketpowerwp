<?php

/**
 * @author Dexter John R. Campos <dexterjohncampos@gmail.com>
 * @copyright Les Coders
 */
require_once("../../../../wp-load.php");
echo '
    var LePlugin = LePlugin || {};';
$currentUser = get_userdata(get_current_user_id());
$toEncode = [
    'id' => $currentUser->ID,
    'display_name' => $currentUser->display_name,
    'user_email' => $currentUser->user_email
];
echo 'LePlugin.currentUser=' . json_encode($toEncode) . ';';
