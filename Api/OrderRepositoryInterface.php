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
namespace WeArePlanet\Payment\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface for WeArePlanet order data.
 *
 * @api
 */
interface OrderRepositoryInterface
{

	/**
	 * Get order by Order Increment Id
	 *
	 * @param $incrementId
	 * @return OrderInterface|null
	 */
    public function getOrderByIncrementId($incrementId);

	/**
	 * Get order by Id
	 *
	 * @param string $id
	 * @return OrderInterface|null
	 */
    public function getOrderById($id);
}
