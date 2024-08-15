<?php
/**
 * WeArePlanet Magento 2
 *
 * This Magento 2 extension enables to process payments with WeArePlanet (https://www.weareplanet.com).
 *
 * @package WeArePlanet_Payment
 * @author Planet Merchant Services Ltd (https://www.weareplanet.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)

 */
namespace WeArePlanet\Payment\Plugin\Customer\Model;

use Magento\Checkout\Model\Session as CheckoutSession;

class AccountManagement
{

    /**
     *
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Customer\Model\AccountManagement $subject
     * @param string $customerEmail
     * @param int $websiteId
     * @return void
     */
    public function beforeIsEmailAvailable(\Magento\Customer\Model\AccountManagement $subject, $customerEmail,
        $websiteId = null)
    {
        $this->checkoutSession->setWeArePlanetCheckoutEmailAddress($customerEmail);
    }
}