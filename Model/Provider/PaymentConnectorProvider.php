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
namespace WeArePlanet\Payment\Model\Provider;

use Magento\Framework\Cache\FrontendInterface;
use WeArePlanet\Payment\Model\ApiClient;
use WeArePlanet\Sdk\Service\PaymentConnectorService;

/**
 * Provider of payment connector information from the gateway.
 */
class PaymentConnectorProvider extends AbstractProvider
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
        parent::__construct($cache, 'weareplanet_payment_connectors',
            \WeArePlanet\Sdk\Model\PaymentConnector::class);
        $this->apiClient = $apiClient;
    }

    /**
     * Gets the payment connector by the given id.
     *
     * @param string $id
     * @return \WeArePlanet\Sdk\Model\PaymentConnector
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * Gets a list of payment connectors.
     *
     * @return \WeArePlanet\Sdk\Model\PaymentConnector[]
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
        return $this->apiClient->getService(PaymentConnectorService::class)->all();
    }

    protected function getId($entry)
    {
        /** @var \WeArePlanet\Sdk\Model\PaymentConnector $entry */
        return $entry->getId();
    }
}