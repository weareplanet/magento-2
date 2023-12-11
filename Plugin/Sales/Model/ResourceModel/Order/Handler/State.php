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
namespace WeArePlanet\Payment\Plugin\Sales\Model\ResourceModel\Order\Handler;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\ResourceModel\Order\Handler\State as StateHandler;
use WeArePlanet\Payment\Model\Payment\Method\Adapter;

class State
{

    /**
     * @param StateHandler $stateHandler
     * @param callable $proceed
     * @param Order $order
     * @return Order
     */
    public function aroundCheck(StateHandler $stateHandler, callable $proceed, Order $order)
    {
        if ($order->getState() == Order::STATE_PROCESSING
            && $order->getPayment()->getMethodInstance() instanceof Adapter
            && $this->hasOpenInvoices($order)) {
            if ($order->hasShipments()) {
                if ($order->getStatus() == $order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING)) {
                    $order->setState(Order::STATE_PROCESSING)->setStatus('shipped_weareplanet');
                }
                return $order;
            } else if ($order->getIsVirtual()) {
                return $order;
            } else {
                return $proceed($order);
            }
        } else {
            return $proceed($order);
        }
    }

    /**
     *
     * @param Order $order
     * @return bool
     */
    protected function hasOpenInvoices(Order $order)
    {
        if ($order->hasInvoices()) {
            /**
             *
             * @var Invoice $invoice
             */
            foreach ($order->getInvoiceCollection() as $invoice) {
                if ($invoice->getState() != Invoice::STATE_PAID) {
                    return true;
                }
            }
        }

        return false;
    }
}