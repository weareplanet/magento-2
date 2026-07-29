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
namespace WeArePlanet\Payment\Controller\Order;

use WeArePlanet\Payment\Api\Data\TransactionInfoInterface;
use WeArePlanet\Payment\Controller\Order\AbstractDownloadDocument;
use WeArePlanet\PluginCore\Document\RenderedDocument;

/**
 * Frontend controller action to download a packing slip.
 */
class DownloadPackingSlip extends AbstractDownloadDocument
{
    /**
     * @inheritDoc
     */
    protected function isDocumentDownloadAllowed(TransactionInfoInterface $transaction, $storeId): bool
    {
        return $this->documentHelper->isPackingSlipDownloadAllowed($transaction, $storeId);
    }

    /**
     * @inheritDoc
     */
    protected function getDocument(int $spaceId, int $transactionId): RenderedDocument
    {
        return $this->documentService->getPackingSlip($spaceId, $transactionId);
    }
}
