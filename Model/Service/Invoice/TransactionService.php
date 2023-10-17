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
namespace WeArePlanet\Payment\Model\Service\Invoice;

use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment;
use WeArePlanet\Payment\Api\PaymentMethodConfigurationManagementInterface;
use WeArePlanet\Payment\Api\TransactionInfoRepositoryInterface;
use WeArePlanet\Payment\Helper\Data as Helper;
use WeArePlanet\Payment\Helper\Locale as LocaleHelper;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Payment\Model\Service\AbstractTransactionService;
use WeArePlanet\Payment\Model\Service\Order\TransactionService as OrderTransactionService;
use WeArePlanet\Sdk\Model\TransactionCompletion;
use WeArePlanet\Sdk\Model\TransactionCompletionState;
use WeArePlanet\Sdk\Model\TransactionInvoice;
use WeArePlanet\Sdk\Model\TransactionInvoiceState;
use WeArePlanet\Sdk\Model\TransactionLineItemUpdateRequest;
use WeArePlanet\Sdk\Model\TransactionState;
use WeArePlanet\Sdk\Model\TransactionLineItemVersionCreate;
use WeArePlanet\Sdk\Service\TransactionLineItemVersionService;

/**
 * Service to handle transactions in invoice context.
 */
class TransactionService extends AbstractTransactionService
{

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @var LocaleHelper
     */
    private $localeHelper;

    /**
     *
     * @var LineItemService
     */
    private $lineItemService;

    /**
     *
     * @var TransactionInfoRepositoryInterface
     */
    private $transactionInfoRepository;

    /**
     *
     * @var OrderTransactionService
     */
    private $orderTransactionService;

    /**
     *
     * @param ResourceConnection $resource
     * @param Helper $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerRegistry $customerRegistry
     * @param CartRepositoryInterface $quoteRepository
     * @param PaymentMethodConfigurationManagementInterface $paymentMethodConfigurationManagement
     * @param ApiClient $apiClient
     * @param CookieManagerInterface $cookieManager
     * @param LocaleHelper $localeHelper
     * @param LineItemService $lineItemService
     * @param TransactionInfoRepositoryInterface $transactionInfoRepository
     * @param OrderTransactionService $orderTransactionService
     */
    public function __construct(ResourceConnection $resource, Helper $helper, ScopeConfigInterface $scopeConfig,
        CustomerRegistry $customerRegistry, CartRepositoryInterface $quoteRepository, TimezoneInterface $timezone,
        PaymentMethodConfigurationManagementInterface $paymentMethodConfigurationManagement, ApiClient $apiClient,
        CookieManagerInterface $cookieManager, LocaleHelper $localeHelper, LineItemService $lineItemService,
        TransactionInfoRepositoryInterface $transactionInfoRepository, OrderTransactionService $orderTransactionService)
    {
        parent::__construct($resource, $helper, $scopeConfig, $customerRegistry, $quoteRepository, $timezone,
            $paymentMethodConfigurationManagement, $apiClient, $cookieManager);
        $this->apiClient = $apiClient;
        $this->localeHelper = $localeHelper;
        $this->lineItemService = $lineItemService;
        $this->transactionInfoRepository = $transactionInfoRepository;
        $this->orderTransactionService = $orderTransactionService;
    }

    /**
     * Updates the transaction's line items from the given invoice.
     *
     * @param Invoice $invoice
     * @param float $expectedAmount
     */
    public function updateLineItems(Invoice $invoice, $expectedAmount)
    {
        $transactionInfo = $this->transactionInfoRepository->getByOrderId($invoice->getOrderId());
        if ($transactionInfo->getState() == TransactionState::AUTHORIZED) {
            $lineItems = $this->lineItemService->convertInvoiceLineItems($invoice, $expectedAmount);

            $data = [
              'external_id' => uniqid(),
              'line_items' => $lineItems,
              'transaction' => (int)$transactionInfo->getTransactionId()
            ];

            $lineItemsCreate = new TransactionLineItemVersionCreate($data);
            $this->apiClient->getService(TransactionLineItemVersionService::class)->create(
                $transactionInfo->getSpaceId(), $lineItemsCreate
            );
        }
    }

    /**
     * Completes the transaction linked to the given payment's and invoice's order.
     *
     * @param Payment $payment
     * @param Invoice $invoice
     * @param float $amount
     * @throws \Exception
     */
    public function complete(Payment $payment, Invoice $invoice, $amount)
    {
        $this->updateLineItems($invoice, $amount);

        $completion = $this->orderTransactionService->complete($invoice->getOrder());
        if (! ($completion instanceof TransactionCompletion) ||
            $completion->getState() == TransactionCompletionState::FAILED) {
            throw new \Magento\Framework\Exception\LocalizedException(
                \__('The capture of the invoice failed on the gateway: %1.',
                    $this->localeHelper->translate($completion->getFailureReason()
                        ->getDescription())));
        }

        try {
            $transactionInvoice = $this->orderTransactionService->getTransactionInvoice($invoice->getOrder());
            if ($transactionInvoice instanceof TransactionInvoice &&
                $transactionInvoice->getState() != TransactionInvoiceState::PAID &&
                $transactionInvoice->getState() != TransactionInvoiceState::NOT_APPLICABLE) {
                $invoice->setWeareplanetCapturePending(true);
            }
        } catch (NoSuchEntityException $e) {}

        $authorizationTransaction = $payment->getAuthorizationTransaction();
        if($authorizationTransaction) {
            $authorizationTransaction->close(false);
            $invoice->getOrder()
                ->addRelatedObject($invoice)
                ->addRelatedObject($authorizationTransaction);
        }
        else {
            throw new \Magento\Framework\Exception\LocalizedException(
                \__('The capture of the invoice failed in the store: %1.',
                    \__('The associated authorization transaction for the payment could not be found.')));
        }
    }
}