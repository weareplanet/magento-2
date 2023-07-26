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
namespace WeArePlanet\Payment\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for WeArePlanet refund job search results.
 *
 * @api
 */
interface RefundJobSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get refund jobs list.
     *
     * @return \WeArePlanet\Payment\Api\Data\RefundJobInterface[]
     */
    public function getItems();

    /**
     * Set refund jobs list.
     *
     * @param \WeArePlanet\Payment\Api\Data\RefundJobInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}