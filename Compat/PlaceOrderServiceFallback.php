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
 * Stub base used when Hyvä Checkout module is not present.
 * WeArePlanet\Payment\Compat\PlaceOrderServiceBase is aliased
 * to this class so that PlaceOrderService can be declared and reflected
 * during DI compilation without a fatal errors when AbstractPlaceOrderService
 * from Hyvä Checkout is not isntalled.
 */
class PlaceOrderServiceFallback
{
}
