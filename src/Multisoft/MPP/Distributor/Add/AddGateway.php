<?php
/**
 * User: Rodine Mark Paul L. Villar <dean.villar@gmail.com>
 * Date: 6/23/2016
 * Time: 3:21 AM
 */

namespace Multisoft\MPP\Distributor\Add;

use LePlugin\Core\GatewayInterface;
use Katzgrau\KLogger\Logger;
use Multisoft\MPP\Core\CoreGateway;
use Multisoft\MPP\Settings\SettingsGateway;
use Multisoft\MPP\Core\CoreController;
use Multisoft\MPP\Replication\ReplicationGateway;
use Multisoft\MPP\Distributor\DistributorController;

class AddGateway implements GatewayInterface
{
    private static $instance;

    private function __construct(Logger $logger = null)
    {
        if ($logger) {
            $this->logger = $logger;
        }
    }

    public static function getInstance($logger = null)
    {
        if (null === static::$instance) {
            static::$instance = new static($logger);
        }
        return static::$instance;
    }

    public function get_form_config($json = false)
    {
        /* @var $settingsGateway SettingsGateway */
        /* @var $replicationGateway ReplicationGateway */
        /* @var $coreGateway CoreGateway */
        $settingsGateway = SettingsGateway::getInstance();
        $replicationGateway = ReplicationGateway::getInstance();
        $coreGateway = CoreGateway::getInstance();

        $config = array(
            "domain" => $settingsGateway->get(
                CoreController::OPTION_WEB_ADDRESS
            ),
            "webPath" => $settingsGateway->get(
                CoreController::OPTION_WEB_PATH
            ),
            "applicationID" => $settingsGateway->get(
                CoreController::OPTION_APPLICATION_ID
            ),
            "replicationInfo" => $replicationGateway->get_replication_info(),
            "addDistributorUrlResource" => array(
                "countries" => $coreGateway->getResourceUrl(
                    DistributorController::OPTION_COUNTRIES_PATH
                ),
                "regions" => $coreGateway->getResourceUrl(
                    DistributorController::OPTION_REGIONS_PATH
                ),
                "commissionPaymentMethods" => $coreGateway->getResourceUrl(
                    DistributorController::OPTION_COMMISSION_PAYMENT_PATH
                ),
                "addDistributorEndpoint" => $coreGateway->getResourceUrl(
                    AddController::OPTION_ADD_DISTRIBUTOR_PATH
                ),
                "binarySpillingDirections" => $coreGateway->getResourceUrl(
                    DistributorController::OPTION_BINARY_SIDE_LIST
                )
            ),
            "addDistributorConfigFormValues" => $settingsGateway->get(
                AddController::OPTION_ADD_DISTRIBUTOR_FORM_CONFIG
            ),
            "addDistributorFormSeparator" => $settingsGateway->get(
                AddController::OPTION_ADD_DISTRIBUTOR_FORM_SEPARATOR
            ),
            "addDistributorFormFields" => $settingsGateway->get(
                AddController::OPTION_ADD_DISTRIBUTOR_FORM_FIELDS
            )
        );
        if ($json) {
            $config = json_encode($config);
        }

        return $config;
    }
}