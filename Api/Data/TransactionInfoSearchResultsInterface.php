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
namespace WeArePlanet\Payment\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for WeArePlanet transaction info search results.
 *
 * @api
 */
interface TransactionInfoSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get transaction infos list.
     *
     * @return \WeArePlanet\Payment\Api\Data\TransactionInfoInterface[]
     */
    public function getItems();

    /**
     * Set transaction infos list.
     *
     * @param \WeArePlanet\Payment\Api\Data\TransactionInfoInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}