<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/23/2016
 * Time: 4:10 AM
 */
require_once("../../../../../wp-load.php");
echo '
    var MultisoftMPPADF = MultisoftMPPADF || {};';

/* @var $add_gateway \Multisoft\MPP\Distributor\Add\AddGateway */
/* @var $add_controller \Multisoft\MPP\Distributor\Add\AddController */
$add_gateway = \Multisoft\MPP\Distributor\Add\AddGateway::getInstance();
$add_controller = \Multisoft\MPP\Distributor\Add\AddController::class;

$form_config = $add_gateway->get_form_config(true);
echo 'MultisoftMPPADF.form_config=' . $form_config . ';';
unset($add_gateway, $form_config);