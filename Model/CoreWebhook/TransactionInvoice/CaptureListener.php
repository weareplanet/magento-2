<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\CoreWebhook\TransactionInvoice;

use WeArePlanet\PluginCore\Webhook\Command\WebhookCommandInterface;
use WeArePlanet\PluginCore\Webhook\Listener\WebhookListenerInterface;
use WeArePlanet\PluginCore\Webhook\WebhookContext;
use WeArePlanet\PluginCore\Log\LoggerInterface;
use WeArePlanet\PluginCore\Transaction\Invoice\InvoiceGatewayInterface;
use WeArePlanet\PluginCore\Transaction\TransactionGatewayInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender as OrderEmailSender;
use WeArePlanet\Payment\Api\TransactionInfoRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Magento\Sales\Model\OrderFactory;

class CaptureListener implements WebhookListenerInterface
{
    /**
     *
     * @param LoggerInterface $logger
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderEmailSender $orderEmailSender
     * @param TransactionInfoRepositoryInterface $transactionInfoRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param InvoiceGatewayInterface $invoiceGateway
     * @param TransactionGatewayInterface $transactionGateway
     * @param OrderResourceModel $orderResourceModel
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderEmailSender $orderEmailSender,
        private readonly TransactionInfoRepositoryInterface $transactionInfoRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly InvoiceGatewayInterface $invoiceGateway,
        private readonly TransactionGatewayInterface $transactionGateway,
        private readonly OrderResourceModel $orderResourceModel,
        private readonly OrderFactory $orderFactory
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
            $this->invoiceGateway,
            $this->transactionGateway,
            $this->orderResourceModel,
            $this->orderFactory
        );
    }
}
