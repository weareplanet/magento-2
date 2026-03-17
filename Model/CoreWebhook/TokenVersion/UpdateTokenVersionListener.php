<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\CoreWebhook\TokenVersion;

use WeArePlanet\Payment\Api\TokenInfoManagementInterface;
use WeArePlanet\PluginCore\Log\LoggerInterface;
use WeArePlanet\PluginCore\Webhook\WebhookContext;
use WeArePlanet\PluginCore\Webhook\Command\WebhookCommandInterface;
use WeArePlanet\PluginCore\Webhook\Listener\WebhookListenerInterface;

class UpdateTokenVersionListener implements WebhookListenerInterface
{

    /**
     *
     * @param LoggerInterface $logger
     * @param TokenInfoManagementInterface $tokenInfoManagement
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TokenInfoManagementInterface $tokenInfoManagement,
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
        return new UpdateTokenVersionCommand(
            $context,
            $this->logger,
            $this->tokenInfoManagement,
        );
    }
}
