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
namespace WeArePlanet\Payment\Model\ResourceModel\TransactionInfo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WeArePlanet\Payment\Model\TransactionInfo;
use WeArePlanet\Payment\Model\ResourceModel\TransactionInfo as ResourceModel;

/**
 * Transaction info resource collection.
 */
class Collection extends AbstractCollection
{

    /**
     *
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'weareplanet_payment_transaction_info_resource_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'info_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TransactionInfo::class, ResourceModel::class);
    }

    /**
     * Filters the collection by space.
     *
     * @param int $spaceId
     * @return $this
     */
    public function addSpaceFilter($spaceId)
    {
        $this->addFieldToFilter('main_table.space_id', $spaceId);
        return $this;
    }
}