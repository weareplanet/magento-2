<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\Settings;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use WeArePlanet\PluginCore\Sdk\ClientMetadata;
use WeArePlanet\PluginCore\Sdk\ClientMetadataProviderInterface;

/**
 * Identifies this Magento installation and plugin version to the portal API.
 */
class ClientMetadataProvider implements ClientMetadataProviderInterface
{
    /**
     * @var string
     */
    private const XML_PATH_PLUGIN_VERSION = 'weareplanet_payment/information/version';

    /**
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ProductMetadataInterface $productMetadata,
    ) {
    }

    /**
     * Returns the metadata identifying this Magento installation and plugin version.
     *
     * @return ClientMetadata|null
     */
    public function getClientMetadata(): ?ClientMetadata
    {
        return new ClientMetadata(
            shopSystem: 'magento',
            shopSystemVersion: $this->productMetadata->getVersion(),
            pluginVersion: '3.5.1',
        );
    }
}
