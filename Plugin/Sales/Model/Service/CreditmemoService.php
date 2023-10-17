<?php
/**
 * WeArePlanet Magento 2
 *
 * This Magento 2 extension enables to process payments with WeArePlanet (https://www.weareplanet.com//).
 *
 * @package WeArePlanet_Payment
 * @author wallee AG (http://www.wallee.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
namespace WeArePlanet\Payment\Plugin\Sales\Model\Service;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use WeArePlanet\Payment\Api\RefundJobRepositoryInterface;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Payment\Model\RefundJobFactory;
use WeArePlanet\Payment\Model\Payment\Method\Adapter as PaymentMethodAdapter;
use WeArePlanet\Payment\Model\Service\LineItemReductionService;
use WeArePlanet\Payment\Model\Service\RefundService;
use WeArePlanet\Sdk\Service\RefundService as ApiRefundService;

/**
 * Interceptor to handle refund jobs when a refund is triggered.
 */
class CreditmemoService
{

    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var LineItemReductionService
     */
    private $lineItemReductionService;

    /**
     *
     * @var RefundJobFactory
     */
    private $refundJobFactory;

    /**
     *
     * @var RefundJobRepositoryInterface
     */
    private $refundJobRepository;

    /**
     *
     * @var RefundService
     */
    private $refundService;

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param LoggerInterface $logger
     * @param LineItemReductionService $lineItemReductionService
     * @param RefundJobFactory $refundJobFactory
     * @param RefundJobRepositoryInterface $refundJobRepository
     * @param RefundService $refundService
     * @param ApiClient $apiClient
     */
    public function __construct(LoggerInterface $logger, LineItemReductionService $lineItemReductionService,
        RefundJobFactory $refundJobFactory, RefundJobRepositoryInterface $refundJobRepository, RefundService $refundService, ApiClient $apiClient)
    {
        $this->logger = $logger;
        $this->lineItemReductionService = $lineItemReductionService;
        $this->refundJobFactory = $refundJobFactory;
        $this->refundJobRepository = $refundJobRepository;
        $this->refundService = $refundService;
        $this->apiClient = $apiClient;
    }

    public function aroundRefund(\Magento\Sales\Model\Service\CreditmemoService $subject, callable $proceed,
        \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo, $offlineRequested = false)
    {
        try {
            return $proceed($creditmemo, $offlineRequested);
        } catch (\Exception $e) {
            if ($creditmemo->getWeareplanetKeepRefundJob() !== true) {
                try {
                    $this->refundJobRepository->delete(
                        $this->refundJobRepository->getByOrderId($creditmemo->getOrderId()));
                } catch (NoSuchEntityException $exc) {}
            }
            throw $e;
        }
    }

    public function beforeRefund(\Magento\Sales\Model\Service\CreditmemoService $subject,
        \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo, $offlineRequested = false)
    {
        if ($offlineRequested || ! $creditmemo->getInvoice()) {
            return null;
        }

        if ($creditmemo->getOrder()
            ->getPayment()
            ->getMethodInstance() instanceof PaymentMethodAdapter &&
            $creditmemo->getWeareplanetExternalId() == null) {
            try {
                $this->handleExistingRefundJob($creditmemo->getOrder());

                $refundCreate = $this->refundService->createRefund($creditmemo);
                $this->refundService->createRefundJob($creditmemo->getInvoice(), $refundCreate);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(\__($e->getMessage()));
            }
        }
    }

    /**
     * Checks if there is an existing refund job for the given order and trys to send to refund to the gateway again.
     *
     * @param Order $order
     * @throws \Exception
     */
    private function handleExistingRefundJob(Order $order)
    {
        try {
            $existingRefundJob = $this->refundJobRepository->getByOrderId($order->getId());
            try {
                $this->apiClient->getService(ApiRefundService::class)->refund(
                    $order->getWeareplanetSpaceId(), $existingRefundJob->getRefund());
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }

            throw new \Magento\Framework\Exception\LocalizedException(
                \__('As long as there is an open creditmemo for the order, no new creditmemo can be created.'));
        } catch (NoSuchEntityException $e) {}
    }


}