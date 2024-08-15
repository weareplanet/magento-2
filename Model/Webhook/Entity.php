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
namespace WeArePlanet\Payment\Model\Webhook;

/**
 * Holds information about a webhook.
 */
class Entity
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array<mixed>
     */
    private $states;

    /**
     * @var bool
     */
    private $notifyEveryChange;

    /**
     *
     * @param int $id
     * @param string $name
     * @param array<mixed> $states
     * @param bool $notifyEveryChange
     */
    public function __construct($id, $name, array $states, $notifyEveryChange = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->states = $states;
        $this->notifyEveryChange = $notifyEveryChange;
    }

    /**
     * Gets the entity's ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the entity's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the entity's states.
     *
     * @return array
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Gets whether every change should be notified.
     *
     * @return bool
     */
    public function isNotifyEveryChange()
    {
        return $this->notifyEveryChange;
    }
}