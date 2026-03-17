<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\CoreWebhook\ManualTask;

use WeArePlanet\Payment\Model\Service\ManualTaskService;
use WeArePlanet\PluginCore\Webhook\Command\WebhookCommandInterface;
use WeArePlanet\PluginCore\Webhook\Listener\WebhookListenerInterface;
use WeArePlanet\PluginCore\Webhook\WebhookContext;
use WeArePlanet\PluginCore\Log\LoggerInterface;

class UpdateListener implements WebhookListenerInterface
{

    /**
     *
     * @param LoggerInterface $logger
     * @param ManualTaskService $manualTaskService
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ManualTaskService $manualTaskService,
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
        return new UpdateCommand($this->logger, $context, $this->manualTaskService);
    }
}
