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
 * Interface for WeArePlanet payment method configuration search results.
 *
 * @api
 */
interface PaymentMethodConfigurationSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get payment method configurations list.
     *
     * @return \WeArePlanet\Payment\Api\Data\PaymentMethodConfigurationInterface[]
     */
    public function getItems();

    /**
     * Set payment method configurations list.
     *
     * @param \WeArePlanet\Payment\Api\Data\PaymentMethodConfigurationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}