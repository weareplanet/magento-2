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
 * Interface for WeArePlanet token info search results.
 *
 * @api
 */
interface TokenInfoSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get token infos list.
     *
     * @return \WeArePlanet\Payment\Api\Data\TokenInfoInterface[]
     */
    public function getItems();

    /**
     * Set token infos list.
     *
     * @param \WeArePlanet\Payment\Api\Data\TokenInfoInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}