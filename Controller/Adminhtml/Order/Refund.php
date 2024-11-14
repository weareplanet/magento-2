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
namespace WeArePlanet\Payment\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use WeArePlanet\Payment\Api\RefundJobRepositoryInterface;
use WeArePlanet\Payment\Helper\Locale as LocaleHelper;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Sdk\Model\RefundState;
use WeArePlanet\Sdk\Service\RefundService;

/**
 * Backend controller action to send a refund request to WeArePlanet.
 */
class Refund extends \WeArePlanet\Payment\Controller\Adminhtml\Order
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::sales_creditmemo';

    /**
     *
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     *
     * @var LocaleHelper
     */
    private $localeHelper;

    /**
     *
     * @var RefundJobRepositoryInterface
     */
    private $refundJobRepository;

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     *
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param LocaleHelper $localeHelper
     * @param RefundJobRepositoryInterface $refundJobRepository
     * @param ApiClient $apiClient
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Context $context, ForwardFactory $resultForwardFactory,
        LocaleHelper $localeHelper,
        RefundJobRepositoryInterface $refundJobRepository,
        ApiClient $apiClient,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->localeHelper = $localeHelper;
        $this->refundJobRepository = $refundJobRepository;
        $this->apiClient = $apiClient;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $isIgnorePendingRefundStatusEnabled = $this->scopeConfig->getValue('weareplanet_payment/pending_refund_status/pending_refund_status_enabled');
        if ($orderId) {
            try {
                $refundJob = $this->refundJobRepository->getByOrderId($orderId);

                try {
                    $refund = $this->apiClient->getService(RefundService::class)->refund($refundJob->getSpaceId(),
                        $refundJob->getRefund());

                    if ($refund->getState() == RefundState::FAILED) {
                        $this->messageManager->addErrorMessage(
                            $this->localeHelper->translate($refund->getFailureReason()
                                ->getDescription()));
                    } elseif ( ! $isIgnorePendingRefundStatusEnabled &&
                        ( $refund->getState() == RefundState::PENDING ||
                        $refund->getState() == RefundState::MANUAL_CHECK ) ) {
                        $this->messageManager->addErrorMessage(
                            \__('The refund was requested successfully, but is still pending on the gateway.'));
                    } else {
                        $this->messageManager->addSuccessMessage(\__('Successfully refunded.'));
                    }
                } catch (\WeArePlanet\Sdk\ApiException $e) {
                    if ($e->getResponseObject() instanceof \WeArePlanet\Sdk\Model\ClientError) {
                        $this->messageManager->addErrorMessage($e->getResponseObject()
                            ->getMessage());
                    } else {
                        $this->messageManager->addErrorMessage(
                            \__('There has been an error while sending the refund to the gateway.'));
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        \__('There has been an error while sending the refund to the gateway.'));
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(\__('For this order no refund request exists.'));
            }
            return $this->resultRedirectFactory->create()->setPath('sales/order/view', [
                'order_id' => $orderId
            ]);
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}