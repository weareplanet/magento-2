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
namespace WeArePlanet\Payment\Model\Webhook\Listener\Transaction;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use WeArePlanet\Sdk\Model\TransactionState;

/**
 * Webhook listener command to handle voided transactions.
 */
class VoidedCommand extends AbstractCommand
{

    /**
     *
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     *
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     *
     * @param \WeArePlanet\Sdk\Model\Transaction $entity
     * @param Order $order
     */
    public function execute($entity, Order $order)
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $order->getPayment();
        $payment->registerVoidNotification();

        $invoice = $this->getInvoiceForTransaction($entity, $order);
        if ($invoice) {
            $order->setWeareplanetInvoiceAllowManipulation(true);
            $invoice->cancel();
            $order->addRelatedObject($invoice);
        }

        if ($entity->getState() == TransactionState::VOIDED) {
            $order->setState(Order::STATE_CANCELED);
            $order->addStatusToHistory('canceled',
                \__('The order has been canceled.'));
        }
        $this->orderRepository->save($order);
    }
}