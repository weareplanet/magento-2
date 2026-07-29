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
namespace WeArePlanet\Payment\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use WeArePlanet\PluginCore\Log\LoggerInterface;
use WeArePlanet\Payment\Api\RefundJobRepositoryInterface;
use WeArePlanet\PluginCore\Refund\Exception\InvalidRefundException;
use WeArePlanet\PluginCore\Refund\Exception\RefundException;
use WeArePlanet\PluginCore\Refund\RefundService;
use WeArePlanet\PluginCore\Transaction\Exception\TransactionException;

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
     * @var RefundService
     */
    private $refundService;

    /**
     *
     * @param LoggerInterface $logger
     * @param RefundJobRepositoryInterface $refundJobRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RefundService $refundService
     */
    public function __construct(
        LoggerInterface $logger,
        RefundJobRepositoryInterface $refundJobRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RefundService $refundService
    ) {
        $this->logger = $logger;
        $this->refundJobRepository = $refundJobRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->refundService = $refundService;
    }

    /**
     * Process pending refund jobs.
     *
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
                $this->refundService->createRefund((int) $refundJob->getSpaceId(), $refundJob->getRefund());
                $this->logger->info('Refund job resubmitted to the gateway.', ['refundJobId' => $refundJob->getId()]);
            } catch (InvalidRefundException|RefundException|TransactionException $e) {
                if ($e->isRetryable()) {
                    // Transient failure: leave the job for the next run.
                    $this->logger->critical('Refund job failed; leaving it for the next run.', [
                        'refundJobId' => $refundJob->getId(),
                        'exception' => $e,
                    ]);
                } else {
                    // Terminal failure: retrying the same payload will fail again.
                    $this->logger->critical('Refund job rejected by the gateway; deleting it.', [
                        'refundJobId' => $refundJob->getId(),
                        'exception' => $e,
                    ]);
                    $this->refundJobRepository->delete($refundJob);
                }
            } catch (\Exception $e) {
                $this->logger->critical('Unexpected error processing refund job.', [
                    'refundJobId' => $refundJob->getId(),
                    'exception' => $e,
                ]);
            }
        }
    }
}
