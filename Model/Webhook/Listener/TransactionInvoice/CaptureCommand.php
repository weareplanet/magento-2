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
namespace WeArePlanet\Payment\Model\Webhook\Listener\TransactionInvoice;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Email\Sender\OrderSender as OrderEmailSender;
use Magento\Sales\Model\Order\Payment\Transaction as MagentoTransaction;
use WeArePlanet\Payment\Model\Webhook\Listener\Transaction\AuthorizedCommand;
use WeArePlanet\Sdk\Model\Transaction;
use WeArePlanet\Sdk\Model\TransactionState;

/**
 * Webhook listener command to handle captured transaction invoices.
 */
class CaptureCommand extends AbstractCommand
{

    /**
     *
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     *
     * @var OrderEmailSender
     */
    private $orderEmailSender;

    /**
     *
     * @var AuthorizedCommand
     */
    private $authorizedCommand;

    /**
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderEmailSender $orderEmailSender
     * @param AuthorizedCommand $authorizedCommand
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderEmailSender $orderEmailSender,
        AuthorizedCommand $authorizedCommand)
    {
        $this->orderRepository = $orderRepository;
        $this->orderEmailSender = $orderEmailSender;
        $this->authorizedCommand = $authorizedCommand;
    }

    /**
     *
     * @param \WeArePlanet\Sdk\Model\TransactionInvoice $entity
     * @param Order $order
     */
    public function execute($entity, Order $order)
    {

        $this->authorizedCommand->execute($entity, $order);

        $transaction = $entity->getCompletion()
            ->getLineItemVersion()
            ->getTransaction();

        $isOrderInReview = ($order->getState() == Order::STATE_PAYMENT_REVIEW);
        if (!$isOrderInReview) {
            $order->setState(Order::STATE_PAYMENT_REVIEW);
            $order->addStatusToHistory('pending',
                \__('The order should not be fulfilled yet, as the payment is not guaranteed.'));
        }
        
        $invoice = $this->getInvoiceForTransaction($transaction, $order);
        if (! ($invoice instanceof InvoiceInterface) || $invoice->getState() == Invoice::STATE_OPEN) {
            $isOrderInReview = ($order->getState() == Order::STATE_PAYMENT_REVIEW);

            if (! ($invoice instanceof InvoiceInterface)) {
                $order->setWeareplanetInvoiceAllowManipulation(true);
            }

            if (! ($invoice instanceof InvoiceInterface) || $invoice->getState() == Invoice::STATE_OPEN) {
                /** @var \Magento\Sales\Model\Order\Payment $payment */
                $payment = $order->getPayment();
                $payment->setTransactionId(null);
                $payment->setParentTransactionId($payment->getTransactionId());
                $payment->setIsTransactionClosed(true);
                $payment->registerCaptureNotification($entity->getAmount());
                if (! ($invoice instanceof InvoiceInterface) && !empty($payment->getCreatedInvoice())) {
                    $invoice = $payment->getCreatedInvoice();
                    $order->addRelatedObject($invoice);
                } else {
                    // Fix an issue that invoice doesn't have the correct status after call to registerCaptureNotification
                    // see \Magento\Sales\Model\Order\Payment\Operations\RegisterCaptureNotificationOperation::registerCaptureNotification
                    foreach ($order->getRelatedObjects() as $object) {
                        if ($object instanceof InvoiceInterface) {
                            $invoice = $object;
                            break;
                        }
                    }
                }

                if ($invoice instanceof InvoiceInterface) {
                    $invoice->setWeareplanetCapturePending(false);
                } else {
                    return false;
                }
            }

            if ($transaction->getState() == TransactionState::COMPLETED) {
                $order->setStatus('processing');
            }

            if ($isOrderInReview) {
                $order->setState(Order::STATE_PAYMENT_REVIEW);
                $order->addStatusToHistory(true);
            }

            $order->setWeareplanetAuthorized(true);
            $order->setStatus('processing');
            $order->setState(Order::STATE_PROCESSING);

            $this->orderRepository->save($order);
            $this->sendOrderEmail($order);
        }
    }

    /**
     * Sends the order email if not already sent.
     *
     * @param Order $order
     * @return void
     */
    private function sendOrderEmail(Order $order)
    {
        if ($order->getStore()->getConfig('weareplanet_payment/email/order') && ! $order->getEmailSent()) {
            $this->orderEmailSender->send($order);
        }
    }

}