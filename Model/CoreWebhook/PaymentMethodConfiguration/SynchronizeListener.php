<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\CoreWebhook\PaymentMethodConfiguration;

use WeArePlanet\Payment\Api\PaymentMethodConfigurationManagementInterface;
use WeArePlanet\PluginCore\Log\LoggerInterface;
use WeArePlanet\PluginCore\Webhook\Command\WebhookCommandInterface;
use WeArePlanet\PluginCore\Webhook\Listener\WebhookListenerInterface;
use WeArePlanet\PluginCore\Webhook\WebhookContext;

class SynchronizeListener implements WebhookListenerInterface
{

    /**
     *
     * @param LoggerInterface $logger
     * @param PaymentMethodConfigurationManagementInterface $management
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly PaymentMethodConfigurationManagementInterface $management
    ) {
    }

    /**
     * Create webhook command for the given context.
     *
     * @param WebhookContext $context
     * @return WebhookCommandInterface
     */
    public function getCommand(WebhookContext $context): WebhookCommandInterface
    {
        return new SynchronizeCommand($context, $this->logger, $this->management);
    }
}
