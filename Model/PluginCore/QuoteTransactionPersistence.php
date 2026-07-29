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

use Magento\Framework\App\ResourceConnection;
use Magento\Quote\Model\Quote;
use WeArePlanet\PluginCore\Transaction\TransactionPersistenceInterface;

/**
 * Per-quote persistence strategy passed to {@see \WeArePlanet\PluginCore\Transaction\TransactionService::upsert()}.
 *
 * The plugin-core contract only forwards the transaction id; the space id is
 * supplied at construction time by the caller that already knows which space
 * the quote belongs to.
 */
class QuoteTransactionPersistence implements TransactionPersistenceInterface
{
    /**
     *
     * @param Quote $quote
     * @param int $spaceId
     * @param ResourceConnection $resource
     */
    public function __construct(
        private readonly Quote $quote,
        private readonly int $spaceId,
        private readonly ResourceConnection $resource,
    ) {
    }

    /**
     * Persists the resolved transaction id (and the owning space id) on the
     * quote model and the underlying quote table.
     *
     * @param int $transactionId
     * @return void
     */
    public function persist(int $transactionId): void
    {
        $this->quote->setWeareplanetSpaceId($this->spaceId);
        $this->quote->setWeareplanetTransactionId($transactionId);

        $this->resource->getConnection()->update(
            $this->resource->getTableName('quote'),
            [
                'weareplanet_space_id' => $this->spaceId,
                'weareplanet_transaction_id' => $transactionId,
            ],
            ['entity_id = ?' => $this->quote->getId()]
        );
    }
}
