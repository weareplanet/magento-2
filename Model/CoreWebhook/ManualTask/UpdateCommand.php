<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\CoreWebhook\ManualTask;

use WeArePlanet\Payment\Model\Service\ManualTaskService;
use WeArePlanet\PluginCore\Log\LoggerInterface;
use WeArePlanet\PluginCore\Webhook\Command\WebhookCommand;
use WeArePlanet\PluginCore\Webhook\WebhookContext;

class UpdateCommand extends WebhookCommand
{

    /**
     *
     * @param LoggerInterface $logger
     * @param WebhookContext $context
     * @param ManualTaskService $manualTaskService
     */
    public function __construct(
        LoggerInterface $logger,
        WebhookContext $context,
        private readonly ManualTaskService $manualTaskService
    ) {
        parent::__construct($context, $logger);
    }

    /**
     * Execute update command for the current webhook context.
     *
     * @return mixed
     */
    public function execute(): mixed
    {
        $this->logger->info('Running UpdateCommand');

        $this->manualTaskService->update();

        $this->logger->debug('Command Update for ManualTask completed.');

        return null;
    }
}
