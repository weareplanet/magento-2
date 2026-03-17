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
namespace WeArePlanet\Payment\Model;

use Magento\Framework\Model\AbstractModel;
use WeArePlanet\Payment\Api\Data\RefundJobInterface;
use WeArePlanet\Payment\Model\ResourceModel\RefundJob as ResourceModel;

/**
 * Refund job model.
 */
class RefundJob extends AbstractModel implements RefundJobInterface
{

    /**
     *
     * @var string
     */
    protected $_eventPrefix = 'weareplanet_payment_refund_job';

    /**
     *
     * @var string
     */
    protected $_eventObject = 'job';

    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get created at timestamp.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(RefundJobInterface::CREATED_AT);
    }

    /**
     * Get external id.
     *
     * @return string
     */
    public function getExternalId()
    {
        return $this->getData(RefundJobInterface::EXTERNAL_ID);
    }

    /**
     * Get order id.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(RefundJobInterface::ORDER_ID);
    }

    /**
     * Get invoice id.
     *
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->getData(RefundJobInterface::INVOICE_ID);
    }

    /**
     * Get refund.
     *
     * @return string
     */
    public function getRefund()
    {
        return $this->getData(RefundJobInterface::REFUND);
    }

    /**
     * Get space id.
     *
     * @return int
     */
    public function getSpaceId()
    {
        return $this->getData(RefundJobInterface::SPACE_ID);
    }
}
