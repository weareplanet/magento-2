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
namespace WeArePlanet\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use WeArePlanet\PluginCore\Refund\LineItem\RefundLineItem;
use WeArePlanet\PluginCore\Refund\RefundCalculator;

/**
 * Helper to provide line item reduction related functionality.
 */
class LineItemReduction extends AbstractHelper
{

    /**
     *
     * @var Data
     */
    private $helper;

    /**
     *
     * @param Context $context
     * @param Data $helper
     */
    public function __construct(Context $context, Data $helper)
    {
        parent::__construct($context);
        $this->helper = $helper;
    }

    /**
     * Gets the amount of the line item's reductions.
     *
     * @param \WeArePlanet\PluginCore\LineItem\LineItem[] $lineItems
     * @param array $reductions Each entry: ['uniqueId'=>?string,'quantityReduction'=>float,'unitPriceReduction'=>float]
     * @param string $currency
     * @return float
     */
    public function getReducedAmount(array $lineItems, array $reductions, $currency)
    {
        $lineItemMap = [];
        foreach ($lineItems as $lineItem) {
            $lineItemMap[$lineItem->uniqueId] = $lineItem;
        }

        $amount = 0;
        foreach ($reductions as $reduction) {
            if (! isset($lineItemMap[$reduction['uniqueId']])) {
                throw new LocalizedException(
                    \__("The refund cannot be executed as the transaction's line items do not match the order's.")
                );
            }

            $lineItem = $lineItemMap[$reduction['uniqueId']];
            $amount += RefundCalculator::calculateReduction(
                $lineItem,
                new RefundLineItem(
                    (string) $reduction['uniqueId'],
                    (float) $reduction['quantityReduction'],
                    (float) $reduction['unitPriceReduction']
                )
            );
        }

        return $this->helper->roundAmount($amount, $currency);
    }
}
