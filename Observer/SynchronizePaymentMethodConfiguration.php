<?php
/**
 WeArePlanet Magento 2
 *
 * This Magento 2 extension enables to process payments with WeArePlanet (https://www.weareplanet.com).
 *
 * @package WeArePlanet_Payment
 * @author Planet Merchant Services Ltd (https://www.weareplanet.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)

 */
namespace WeArePlanet\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use WeArePlanet\Payment\Api\PaymentMethodConfigurationManagementInterface;

/**
 * Observer to synchronize the payment method configurations.
 */
class SynchronizePaymentMethodConfiguration implements ObserverInterface
{

    /**
     *
     * @var PaymentMethodConfigurationManagementInterface
     */
    private $paymentMethodConfigurationManagement;

    /**
     *
     * @param PaymentMethodConfigurationManagementInterface $paymentMethodConfigurationManagement
     */
    public function __construct(PaymentMethodConfigurationManagementInterface $paymentMethodConfigurationManagement)
    {
        $this->paymentMethodConfigurationManagement = $paymentMethodConfigurationManagement;
    }

    public function execute(Observer $observer)
    {
        $this->paymentMethodConfigurationManagement->synchronize();
    }
}