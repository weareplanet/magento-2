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
namespace WeArePlanet\Payment\Model\Config;

use Magento\Framework\ObjectManagerInterface;

/**
 * Factory to create DOM objects.
 */
class DomFactory
{

    const CLASS_NAME = Dom::class;

    /**
     * Object manager
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     *
     * @param ObjectManagerInterface $objectManger
     */
    public function __construct(ObjectManagerInterface $objectManger)
    {
        $this->objectManager = $objectManger;
    }

    /**
     * Create DOM object
     *
     * @param array $arguments
     * @return Dom
     */
    public function createDom(array $arguments = [])
    {
        return $this->objectManager->create(self::CLASS_NAME, $arguments);
    }
}