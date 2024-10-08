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
use Magento\Sales\Model\Order\Invoice;
use WeArePlanet\Sdk\Model\Transaction;

/**
 * Abstract webhook listener command for order related entites.
 */
abstract class AbstractOrderRelatedCommand implements CommandInterface
{

    /**
     * Gets the invoice linked to the given transaction.
     *
     * @param Transaction $transaction
     * @param Order $order
     * @return Invoice
     */
    protected function getInvoiceForTransaction(Transaction $transaction, Order $order)
    {
        foreach ($order->getInvoiceCollection() as $invoice) {
            /** @var Invoice $invoice */
            if (\strpos($invoice->getTransactionId() ?? '', $transaction->getLinkedSpaceId() . '_' . $transaction->getId()) ===
                0 && $invoice->getState() != Invoice::STATE_CANCELED) {
                $invoice->load($invoice->getId());
                return $invoice;
            }
        }
    }
}
