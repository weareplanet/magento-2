<?php
/**
 * WeArePlanet Magento 2
 *
 * This Magento 2 extension enables to process payments with WeArePlanet (https://www.weareplanet.com//).
 *
 * @package WeArePlanet_Payment
 * @author wallee AG (http://www.wallee.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
namespace WeArePlanet\Payment\Model\Webhook\Listener\Transaction;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Webhook listener command to handle completed transactions.
 */
class CompletedCommand extends AbstractCommand
{
    /**
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @var AuthorizedCommand
     */
    private $authorizedCommand;

    /**
     *
     * @param LoggerInterface $logger
     * @param AuthorizedCommand $authorizedCommand
     */
    public function __construct(AuthorizedCommand $authorizedCommand, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->authorizedCommand = $authorizedCommand;
    }

    /**
     *
     * @param \WeArePlanet\Sdk\Model\Transaction $entity
     * @param Order $order
     */
    public function execute($entity, Order $order)
    {
        $this->logger->debug("CompletedCommand::execute state");
        $this->authorizedCommand->execute($entity, $order);
    }
}