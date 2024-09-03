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
namespace WeArePlanet\Payment\Model\Webhook\Listener\Transaction;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender as OrderEmailSender;
use Magento\Sales\Model\Order\Payment\Transaction;
use WeArePlanet\Payment\Model\Webhook\Listener\Operation\AbstractOperation;
use WeArePlanet\Sdk\Model\TransactionState;

/**
 * Webhook listener command to handle authorized transactions.
 */
class AuthorizedCommand extends AbstractCommand
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
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderEmailSender $orderEmailSender
     */
    public function __construct(OrderRepositoryInterface $orderRepository, OrderEmailSender $orderEmailSender)
    {
        $this->orderRepository = $orderRepository;
        $this->orderEmailSender = $orderEmailSender;
    }

    /**
     *
     * @param \WeArePlanet\Sdk\Model\Transaction $entity
     * @param Order $order
     */
    public function execute($entity, Order $order)
    {
        if ($order->getWeareplanetAuthorized()) {
            // In case the order is already authorized.
            return;
        }

        $payment = $order->getPayment();
        $payment->setTransactionId($entity->getLinkedSpaceId() . '_' . $entity->getId());
        $payment->setIsTransactionClosed(false);
        if ($order->getState() == Order::STATE_PROCESSING) {
            // In case the order is already processing. Potentially the webhooks arriving out of order
            $order->setWeareplanetAuthorized(true);
            $order->setState(Order::STATE_PROCESSING);

            $payment->registerAuthorizationNotification($payment->getAmountAuthorized());

        } else {
            $payment->registerAuthorizationNotification($payment->getAmountAuthorized());
            if ($entity->getState() != TransactionState::FULFILL) {

                $order->setState(Order::STATE_PAYMENT_REVIEW);
                $order->addStatusToHistory('pending',
                    \__('The order should not be fulfilled yet, as the payment is not guaranteed.')
                );
            }

            $order->setWeareplanetAuthorized(true);
        }

        $this->orderRepository->save($order);

        $this->sendOrderEmail($order);
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
