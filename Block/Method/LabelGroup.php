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
namespace WeArePlanet\Payment\Block\Method;

use WeArePlanet\Sdk\Model\LabelDescriptorGroup;

/**
 * Holds information about a label group that are needed to render the labels in the backend.
 */
class LabelGroup
{

    /**
     *
     * @var LabelDescriptorGroup
     */
    private $descriptor;

    /**
     *
     * @var Label[]
     */
    private $labels = [];

    /**
     *
     * @param LabelDescriptorGroup $descriptor
     * @param Label[] $labels
     */
    public function __construct(LabelDescriptorGroup $descriptor, array $labels)
    {
        $this->descriptor = $descriptor;
        $this->labels = $labels;
    }

    /**
     * Gets the group descriptor's ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->descriptor->getId();
    }

    /**
     * Gets the group descriptor's name.
     *
     * @return array
     */
    public function getName()
    {
        return $this->descriptor->getName();
    }

    /**
     * Gets the group descriptor's weight.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->descriptor->getWeight();
    }

    /**
     *
     * @return Label[]
     */
    public function getLabels()
    {
        return $this->labels;
    }
}