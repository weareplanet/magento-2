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
namespace WeArePlanet\Payment\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class DeviceSession extends \Magento\Framework\View\Element\Template
{

    /**
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(Context $context, ScopeConfigInterface $scopeConfig, array $data = [])
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     *
     * @return string
     */
    public function getSessionIdentifierUrl()
    {
        return $this->getUrl('weareplanet_payment/checkout/deviceSession', ['_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     *
     * @return string
     */
    public function getScriptUrl()
    {
        $device = $this->scopeConfig->getValue('weareplanet_payment/checkout/fingerprint', ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore());
        if ($device!=1) {
            return false;
        }

        $baseUrl = \rtrim($this->scopeConfig->getValue('weareplanet_payment/general/base_gateway_url'), '/');
        $spaceId = $this->scopeConfig->getValue('weareplanet_payment/general/space_id',
            ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore());

        if (! empty($spaceId)) {
            return $baseUrl . '/s/' . $spaceId . '/payment/device.js?sessionIdentifier=';
        }
    }
}