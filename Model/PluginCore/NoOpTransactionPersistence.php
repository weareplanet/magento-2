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
declare(strict_types=1);

namespace WeArePlanet\Payment\Model\PluginCore;

use WeArePlanet\PluginCore\Transaction\TransactionPersistenceInterface;

/**
 * Persistence strategy for {@see \WeArePlanet\PluginCore\Transaction\TransactionService::upsert()}
 * calls where the order already holds its transaction id.
 *
 * If upsert() had to create a replacement transaction, the id must not be adopted: the caller
 * compares the returned id with the order's and fails the checkout instead.
 */
class NoOpTransactionPersistence implements TransactionPersistenceInterface
{
    /**
     * @param int $transactionId
     * @return void
     */
    public function persist(int $transactionId): void
    {
    }
}
