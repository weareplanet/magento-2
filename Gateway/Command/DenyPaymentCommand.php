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
namespace WeArePlanet\Payment\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use WeArePlanet\Payment\Model\Service\Order\TransactionService;

/**
 * Payment gateway command to deny a payment.
 */
class DenyPaymentCommand implements CommandInterface
{

    /**
     *
     * @var TransactionService
     */
    private $orderTransactionService;

    /**
     *
     * @param TransactionService $orderTransactionService
     */
    public function __construct(TransactionService $orderTransactionService)
    {
        $this->orderTransactionService = $orderTransactionService;
    }

    public function execute(array $commandSubject)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = SubjectReader::readPayment($commandSubject)->getPayment();

        $this->orderTransactionService->deny($payment->getOrder());
        $payment->getOrder()->setWeareplanetInvoiceAllowManipulation(true);
    }
}