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
namespace WeArePlanet\Payment\Model\Webhook\Listener;

use WeArePlanet\Payment\Api\TokenInfoManagementInterface;
use WeArePlanet\Payment\Model\Webhook\ListenerInterface;
use WeArePlanet\Payment\Model\Webhook\Request;

/**
 * Webhook listener to handle tokens.
 */
class TokenListener implements ListenerInterface
{

    /**
     *
     * @var TokenInfoManagementInterface
     */
    private $tokenInfoManagement;

    /**
     *
     * @param TokenInfoManagementInterface $tokenInfoManagement
     */
    public function __construct(TokenInfoManagementInterface $tokenInfoManagement)
    {
        $this->tokenInfoManagement = $tokenInfoManagement;
    }

    public function execute(Request $request)
    {
        $this->tokenInfoManagement->updateToken($request->getSpaceId(), $request->getEntityId());
    }
}