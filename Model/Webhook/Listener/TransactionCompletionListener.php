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
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Psr\Log\LoggerInterface;
use WeArePlanet\Payment\Api\TransactionInfoRepositoryInterface;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Payment\Model\Webhook\Request;
use WeArePlanet\Sdk\Service\TransactionCompletionService;

/**
 * Webhook listener to handle transaction completions.
 */
class TransactionCompletionListener extends AbstractOrderRelatedListener
{

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
     * @param ApiClient $apiClient
     */
    public function __construct(ResourceConnection $resource, LoggerInterface $logger, OrderFactory $orderFactory,
        OrderResourceModel $orderResourceModel, CommandPoolInterface $commandPool,
        TransactionInfoRepositoryInterface $transactionInfoRepository, ApiClient $apiClient)
    {
        parent::__construct($resource, $logger, $orderFactory, $orderResourceModel, $commandPool,
            $transactionInfoRepository);
        $this->apiClient = $apiClient;
    }

    /**
     * Loads the completion for the webhook request.
     *
     * @param Request $request
     * @return \WeArePlanet\Sdk\Model\TransactionCompletion
     */
    protected function loadEntity(Request $request)
    {
        return $this->apiClient->getService(TransactionCompletionService::class)->read($request->getSpaceId(),
            $request->getEntityId());
    }

    /**
     * Gets the transaction's ID.
     *
     * @param \WeArePlanet\Sdk\Model\TransactionCompletion $entity
     * @return int
     */
    protected function getTransactionId($entity)
    {
        return $entity->getLineItemVersion()
            ->getTransaction()
            ->getId();
    }
}