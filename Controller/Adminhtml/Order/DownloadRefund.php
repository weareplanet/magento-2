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
use Magento\Backend\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\CreditmemoRepository;
use WeArePlanet\Payment\Api\TransactionInfoRepositoryInterface;
use WeArePlanet\Payment\Helper\Data as Helper;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Sdk\Model\EntityQuery;
use WeArePlanet\Sdk\Service\RefundService;

/**
 * Backend controller action to download a refund document.
 */
class DownloadRefund extends \WeArePlanet\Payment\Controller\Adminhtml\Order
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
     * @var FileFactory
     */
    private $fileFactory;

    /**
     *
     * @var Helper
     */
    private $helper;

    /**
     *
     * @var TransactionInfoRepositoryInterface
     */
    private $transactionInfoRepository;

    /**
     *
     * @var CreditmemoRepository
     */
    private $creditmemoRepository;

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param FileFactory $fileFactory
     * @param Helper $helper
     * @param TransactionInfoRepositoryInterface $transactionInfoRepository
     * @param ApiClient $apiClient
     * @param CreditmemoRepository $creditmemoRepository
     */
    public function __construct(Context $context, ForwardFactory $resultForwardFactory, FileFactory $fileFactory,
        Helper $helper, TransactionInfoRepositoryInterface $transactionInfoRepository, ApiClient $apiClient,
        CreditmemoRepository $creditmemoRepository)
    {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->fileFactory = $fileFactory;
        $this->helper = $helper;
        $this->transactionInfoRepository = $transactionInfoRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->apiClient = $apiClient;
    }

    public function execute()
    {
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = $this->creditmemoRepository->get($creditmemoId);
            if ($creditmemo->getWeareplanetExternalId() == null) {
                return $this->resultForwardFactory->create()->forward('noroute');
            }

            $transaction = $this->transactionInfoRepository->getByOrderId($creditmemo->getOrderId());
            $refund = $this->getRefundByExternalId($transaction->getSpaceId(),
                $creditmemo->getWeareplanetExternalId());
            $document = $this->apiClient->getService(RefundService::class)->getRefundDocument(
                $transaction->getSpaceId(), $refund->getId());
            return $this->fileFactory->create($document->getTitle() . '.pdf', \base64_decode($document->getData()),
                DirectoryList::VAR_DIR, 'application/pdf');
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }

    /**
     * Fetches the refund's latest state from WeArePlanet by its external ID.
     *
     * @param int $spaceId
     * @param string $externalId
     * @throws \Exception
     * @return \WeArePlanet\Sdk\Model\Refund
     */
    private function getRefundByExternalId($spaceId, $externalId)
    {
        $query = new EntityQuery();
        $query->setFilter($this->helper->createEntityFilter('externalId', $externalId));
        $query->setNumberOfEntities(1);
        $result = $this->apiClient->getService(RefundService::class)->search($spaceId, $query);
        if (! empty($result)) {
            return \current($result);
        } else {
            throw new LocalizedException(\__('The refund could not be found.'));
        }
    }
}