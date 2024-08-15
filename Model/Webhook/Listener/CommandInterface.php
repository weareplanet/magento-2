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
namespace WeArePlanet\Payment\Model\Webhook\Listener;

use Magento\Sales\Model\Order;

/**
 * Webhook listener command interface.
 */
interface CommandInterface
{

    /**
     * @param mixed $entity
     * @param Order $order
     * @return mixed
     */
    public function execute($entity, Order $order);
}