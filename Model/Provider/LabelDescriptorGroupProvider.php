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
namespace WeArePlanet\Payment\Model\Provider;

use Magento\Framework\Cache\FrontendInterface;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Sdk\Service\LabelDescriptionGroupService;

/**
 * Provider of label descriptor group information from the gateway.
 */
class LabelDescriptorGroupProvider extends AbstractProvider
{
    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param FrontendInterface $cache
     * @param ApiClient $apiClient
     */
    public function __construct(FrontendInterface $cache, ApiClient $apiClient)
    {
        parent::__construct($cache, 'weareplanet_payment_label_descriptor_groups',
            \WeArePlanet\Sdk\Model\LabelDescriptorGroup::class);
        $this->apiClient = $apiClient;
    }

    /**
     * Gets the label descriptor group by the given id.
     *
     * @param string $id
     * @return \WeArePlanet\Sdk\Model\LabelDescriptorGroup
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * Gets a list of label descriptor groups.
     *
     * @return \WeArePlanet\Sdk\Model\LabelDescriptorGroup[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    /**
     * @return mixed
     */
    protected function fetchData()
    {
        return $this->apiClient->getService(LabelDescriptionGroupService::class)->all();
    }

    protected function getId($entry)
    {
        /** @var \WeArePlanet\Sdk\Model\LabelDescriptorGroup $entry */
        return $entry->getId();
    }
}