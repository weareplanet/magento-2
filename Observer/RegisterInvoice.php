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
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use WeArePlanet\Payment\Model\Payment\Method\Adapter;
use WeArePlanet\Payment\Model\Service\Invoice\TransactionService;
use WeArePlanet\Sdk\Model\TransactionState;

/**
 * Observer to validate and handle the registration of an invoice.
 */
class RegisterInvoice implements ObserverInterface
{
    /**
     *
     * @var TransactionService
     */
    private $transactionService;

    /**
     *
     * @param TransactionService $transactionService
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getInvoice();
        $order = $invoice->getOrder();

        if ($order->getPayment()->getMethodInstance() instanceof Adapter) {
            // Allow creating the invoice if there is no existing one for the order.
            if ($order->getInvoiceCollection()->count() > 1) {
                // Only allow to create a new invoice if all previous invoices of the order have been cancelled.
                if (! $this->canCreateInvoice($order)) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        \__('Only one invoice is allowed. To change the invoice, cancel the existing one first.'));
                }

                if (! $invoice->getWeareplanetCapturePending()) {
                    $invoice->setTransactionId(
                        $order->getWeareplanetSpaceId() . '_' .
                        $order->getWeareplanetTransactionId());

                    if (! $order->getWeareplanetInvoiceAllowManipulation()) {
                        // The invoice can only be created by the merchant if the transaction is in state 'AUTHORIZED'.
                        $transaction = $this->transactionService->getTransaction(
                            $order->getWeareplanetSpaceId(),
                            $order->getWeareplanetTransactionId());
                        if ($transaction->getState() != TransactionState::AUTHORIZED) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                \__('The invoice cannot be created.'));
                        }

                        $this->transactionService->updateLineItems($invoice, $invoice->getGrandTotal());
                    }
                }
            }
        }
    }

    /**
     * Returns whether an invoice can be created for the given order, i.e.
     * there is no existing uncancelled invoice.
     *
     * @param Order $order
     * @return boolean
     */
    private function canCreateInvoice(Order $order)
    {
        foreach ($order->getInvoiceCollection() as $invoice) {
            if ($invoice->getId() && $invoice->getState() != Invoice::STATE_CANCELED) {
                return false;
            }
        }

        return true;
    }
}