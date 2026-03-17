<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\CoreWebhook\TransactionInvoice;

use WeArePlanet\PluginCore\Webhook\Command\WebhookCommandInterface;
use WeArePlanet\PluginCore\Webhook\Listener\WebhookListenerInterface;
use WeArePlanet\PluginCore\Webhook\WebhookContext;
use WeArePlanet\PluginCore\Log\LoggerInterface;
use WeArePlanet\PluginCore\Sdk\SdkProvider;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender as OrderEmailSender;
use WeArePlanet\Payment\Api\TransactionInfoRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel; // 1. Import
use Magento\Sales\Model\OrderFactory; // 2. Import

class CaptureListener implements WebhookListenerInterface
{
    /**
     *
     * @param LoggerInterface $logger
     * @param SdkProvider $sdkProvider
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderEmailSender $orderEmailSender
     * @param TransactionInfoRepositoryInterface $transactionInfoRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderResourceModel $orderResourceModel
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SdkProvider $sdkProvider,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderEmailSender $orderEmailSender,
        private readonly TransactionInfoRepositoryInterface $transactionInfoRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly OrderResourceModel $orderResourceModel, // 3. Inject
        private readonly OrderFactory $orderFactory // 4. Inject
    ) {
    }

    /**
     * Create webhook command for the given context.
     *
     * @param WebhookContext $context
     * @return WebhookCommandInterface
     */
    public function getCommand(WebhookContext $context): WebhookCommandInterface
    {
        return new CaptureCommand(
            $context,
            $this->logger,
            $this->orderRepository,
            $this->orderEmailSender,
            $this->transactionInfoRepository,
            $this->searchCriteriaBuilder,
            $this->sdkProvider,
            $this->orderResourceModel, // 5. Pass
            $this->orderFactory // 6. Pass
        );
    }
}
