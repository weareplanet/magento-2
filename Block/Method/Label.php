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
namespace WeArePlanet\Payment\Block\Method;

use WeArePlanet\Sdk\Model\LabelDescriptor;

/**
 * Holds the information about a label that are needed to render the label in the backend.
 */
class Label
{

    /**
     *
     * @var LabelDescriptor
     */
    private $descriptor;

    /**
     *
     * @var string
     */
    private $value;

    /**
     *
     * @param LabelDescriptor $descriptor
     * @param string $value
     */
    public function __construct(LabelDescriptor $descriptor, $value)
    {
        $this->descriptor = $descriptor;
        $this->value = $value;
    }

    /**
     * Gets the label descriptor's ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->descriptor->getId();
    }

    /**
     * Gets the label descriptor's name.
     *
     * @return array
     */
    public function getName()
    {
        return $this->descriptor->getName();
    }

    /**
     * Gets the label descriptor's weight.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->descriptor->getWeight();
    }

    /**
     * Gets the label's value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}