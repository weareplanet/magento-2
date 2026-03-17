<?php

declare(strict_types=1);

namespace WeArePlanet\Payment\Model\CoreWebhook\TokenVersion;

use WeArePlanet\Payment\Api\TokenInfoManagementInterface;
use WeArePlanet\PluginCore\Log\LoggerInterface;
use WeArePlanet\PluginCore\Webhook\Command\WebhookCommand;
use WeArePlanet\PluginCore\Webhook\WebhookContext;

class UpdateTokenVersionCommand extends WebhookCommand
{

    /**
     *
     * @param WebhookContext $context
     * @param LoggerInterface $logger
     * @param TokenInfoManagementInterface $tokenInfoManagement
     */
    public function __construct(
        WebhookContext $context,
        LoggerInterface $logger,
        private readonly TokenInfoManagementInterface $tokenInfoManagement,
    ) {
        parent::__construct($context, $logger);
    }

    /**
     * Execute update token version command for the current webhook context.
     *
     * @return mixed
     */
    public function execute(): mixed
    {
        $this->logger->info(
            sprintf(
                'Running UpdateTokenVersionCommand for entity ID: %d',
                $this->context->entityId
            )
        );

        $spaceId = $this->context->spaceId;
        $tokenVersionId = $this->context->entityId;

        $this->tokenInfoManagement->updateTokenVersion($spaceId, $tokenVersionId);

        $this->logger->debug(
            sprintf(
                'Command UpdateTokenVersion for entity TokenVersion/%d completed.',
                $this->context->entityId
            )
        );
        return null;
    }
}
