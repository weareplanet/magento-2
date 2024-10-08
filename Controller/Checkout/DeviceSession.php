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
namespace WeArePlanet\Payment\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use WeArePlanet\Payment\Helper\Data as Helper;

/**
 * Frontend controller action to provide the device session identifier.
 */
class DeviceSession extends \Magento\Framework\App\Action\Action
{

    /**
     *
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     *
     * @var Helper
     */
    private $helper;

    /**
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Helper $helper
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, Helper $helper)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store', true);
        $resultJson->setHeader('Pragma', 'no-cache', true);
        return $resultJson->setData($this->helper->generateUUID());
    }
}