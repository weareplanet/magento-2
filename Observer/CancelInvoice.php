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
use WeArePlanet\Payment\Model\Payment\Method\Adapter;
use WeArePlanet\Payment\Model\Service\Order\TransactionService;
use WeArePlanet\Sdk\Model\TransactionState;

/**
 * Observer to validate the cancellation of an invoice.
 */
class CancelInvoice implements ObserverInterface
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
            if ($invoice->getWeareplanetCapturePending()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    \__('The invoice cannot be cancelled as its capture has already been requested.'));
            }

            if (! $order->getWeareplanetInvoiceAllowManipulation() &&
                ! $invoice->getWeareplanetDerecognized()) {
                // The invoice can only be cancelled by the merchant if the transaction is in state 'AUTHORIZED'.
                $transaction = $this->transactionService->getTransaction($order->getWeareplanetSpaceId(),
                    $order->getWeareplanetTransactionId());
                if ($transaction->getState() != TransactionState::AUTHORIZED) {
                    throw new \Magento\Framework\Exception\LocalizedException(\__('The invoice cannot be cancelled.'));
                }
            }
        }
    }
}