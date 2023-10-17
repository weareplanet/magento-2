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
namespace WeArePlanet\Payment\Api;

use WeArePlanet\Payment\Model\TokenInfo;

/**
 * Token info management interface.
 *
 * @api
 */
interface TokenInfoManagementInterface
{

    /**
     * Fetches the token version's latest state from WeArePlanet and updates the stored information.
     *
     * @param int $spaceId
     * @param int $tokenVersionId
     */
    public function updateTokenVersion($spaceId, $tokenVersionId);

    /**
     * Fetches the token's latest state from WeArePlanet and updates the stored information.
     *
     * @param int $spaceId
     * @param int $tokenId
     */
    public function updateToken($spaceId, $tokenId);

    /**
     * Deletes the token on WeArePlanet.
     *
     * @param Data\TokenInfoInterface $token
     */
    public function deleteToken(TokenInfo $token);
}