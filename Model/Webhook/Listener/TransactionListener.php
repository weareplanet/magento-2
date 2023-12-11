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
namespace WeArePlanet\Payment\Model\Webhook\Listener;

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Psr\Log\LoggerInterface;
use WeArePlanet\Payment\Api\TransactionInfoManagementInterface;
use WeArePlanet\Payment\Api\TransactionInfoRepositoryInterface;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Payment\Model\Webhook\Request;
use WeArePlanet\Sdk\Service\TransactionService;

/**
 * Webhook listener to handle transactions.
 */
class TransactionListener extends AbstractOrderRelatedListener
{
    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var TransactionInfoRepositoryInterface
     */
    private $transactionInfoRepository;

    /**
     *
     * @var TransactionInfoManagementInterface
     */
    private $transactionInfoManagement;

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     * @param OrderFactory $orderFactory
     * @param OrderResourceModel $orderResourceModel
     * @param CommandPoolInterface $commandPool
     * @param TransactionInfoRepositoryInterface $transactionInfoRepository
     * @param TransactionInfoManagementInterface $transactionInfoManagement
     * @param ApiClient $apiClient
     * @param LoggerInterface $logger
     */
    public function __construct(ResourceConnection $resource, LoggerInterface $logger, OrderFactory $orderFactory,
        OrderResourceModel $orderResourceModel, CommandPoolInterface $commandPool,
        TransactionInfoRepositoryInterface $transactionInfoRepository,
        TransactionInfoManagementInterface $transactionInfoManagement, ApiClient $apiClient)
    {
        parent::__construct($resource, $logger, $orderFactory, $orderResourceModel, $commandPool,
            $transactionInfoRepository);
        $this->transactionInfoRepository = $transactionInfoRepository;
        $this->transactionInfoManagement = $transactionInfoManagement;
        $this->apiClient = $apiClient;
        $this->logger=$logger;
    }

    /**
     * Actually processes the order related webhook request.
     *
     * @param \WeArePlanet\Sdk\Model\Transaction $entity
     * @param Order $order
     * @return void
     */
    protected function process($entity, Order $order)
    {
        $this->logger->debug("TRANSACTION-LISTENER::process");
        $transactionInfo = $this->transactionInfoRepository->getByOrderId($order->getId());
        if ((string)$transactionInfo->getState() != $entity->getState()) {
            parent::process($entity, $order);
        }
        $this->transactionInfoManagement->update($entity, $order);
    }

    /**
     * Loads the transaction for the webhook request.
     *
     * @param Request $request
     * @return \WeArePlanet\Sdk\Model\Transaction
     */
    protected function loadEntity(Request $request)
    {
        return $this->apiClient->getService(TransactionService::class)->read($request->getSpaceId(),
            $request->getEntityId());
    }

    /**
     * Gets the transaction's ID.
     *
     * @param \WeArePlanet\Sdk\Model\Transaction $entity
     * @return int
     */
    protected function getTransactionId($entity)
    {
        return $entity->getId();
    }
}