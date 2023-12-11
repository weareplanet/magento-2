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
namespace WeArePlanet\Payment\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Abstract controller action to handle transaction related requests.
 */
abstract class Transaction extends \Magento\Framework\App\Action\Action
{

    /**
     *
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     *
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(Context $context, OrderRepositoryInterface $orderRepository)
    {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
    }

    /**
     * Gets the order from the request.
     *
     * @throws \Exception
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    protected function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (empty($orderId)) {
            throw new LocalizedException(\__('The order ID has been provided.'));
        }
        $order = $this->orderRepository->get($orderId);

        $token = $order->getWeareplanetSecurityToken();
        if (empty($token) || $token != $this->getRequest()->getParam('token')) {
            throw new LocalizedException(\__('The WeArePlanet security token is invalid.'));
        }

        return $order;
    }
}