<?php
/**
 * WeArePlanet Magento 2
 *
 * This Magento 2 extension enables to process payments with WeArePlanet (https://www.weareplanet.com//).
 *
 * @package WeArePlanet_Payment
 * @author wallee AG (http://www.wallee.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
namespace WeArePlanet\Payment\Api;

/**
 * Payment method configuration management interface.
 *
 * @api
 */
interface PaymentMethodConfigurationManagementInterface
{

    /**
     * Synchronizes the payment method configurations from WeArePlanet.
     */
    public function synchronize();

    /**
     * Updates the payment method configuration information.
     *
     * @param \WeArePlanet\Sdk\Model\PaymentMethodConfiguration $configuration
     */
    public function update(\WeArePlanet\Sdk\Model\PaymentMethodConfiguration $configuration);
}
