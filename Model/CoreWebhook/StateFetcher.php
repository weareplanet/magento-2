<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\CoreWebhook;

use WeArePlanet\PluginCore\Webhook\DefaultStateFetcher;
use WeArePlanet\Sdk\Service\WebhookEncryptionService;
use WeArePlanet\Sdk\Service\TransactionService;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Magento-specific wrapper for the DefaultStateFetcher.
 * Its only job is to get the spaceId from Magento's configuration.
 */
class StateFetcher extends DefaultStateFetcher
{

}
