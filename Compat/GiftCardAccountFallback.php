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
namespace WeArePlanet\Payment\Compat;

/**
 * Stub base used when Magento_GiftCardAccount module is not present.
 * WeArePlanet\Payment\Compat\GiftCardAccountBase is aliased
 * to this class so that GiftCardAccountWrapper can be declared and reflected
 * during DI compilation without fatal errors when GiftCardAccountManagement
 * from Magento_GiftCardAccount is not isntalled.
 */
class GiftCardAccountFallback
{
}
