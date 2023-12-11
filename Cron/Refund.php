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
namespace WeArePlanet\Payment\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
use WeArePlanet\Payment\Api\RefundJobRepositoryInterface;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Sdk\Service\RefundService;

/**
 * Class to handle pending refund jobs.
 */
class Refund
{

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var RefundJobRepositoryInterface
     */
    private $refundJobRepository;

    /**
     *
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param LoggerInterface $logger
     * @param RefundJobRepositoryInterface $refundJobRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ApiClient $apiClient
     */
    public function __construct(LoggerInterface $logger, RefundJobRepositoryInterface $refundJobRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder, ApiClient $apiClient)
    {
        $this->logger = $logger;
        $this->refundJobRepository = $refundJobRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->apiClient = $apiClient;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute()
    {
        $searchCriteria = $this->searchCriteriaBuilder->setPageSize(100)->create();
        $refundJobs = $this->refundJobRepository->getList($searchCriteria)->getItems();
        foreach ($refundJobs as $refundJob) {
            try {
                $this->apiClient->getService(RefundService::class)->refund($refundJob->getSpaceId(),
                    $refundJob->getRefund());
            } catch (\WeArePlanet\Sdk\ApiException $e) {
                if ($e->getResponseObject() instanceof \WeArePlanet\Sdk\Model\ClientError) {
                    $this->refundJobRepository->delete($refundJob);
                } else {
                    $this->logger->critical($e);
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}