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

namespace WeArePlanet\Payment\Plugin\Hyva\Checkout\ViewModel\Payment;

use Hyva\Checkout\ViewModel\Checkout\Payment\MethodList;
use Magento\Framework\View\Element\Template;
use Magento\Payment\Model\MethodInterface;

class MethodListPlugin
{
    /**
     * WhitelabelMachineName payment block fallback.
     *
     * This is a fallback to the generic weareplanet payment block if
     * the specific dynamically generated method block isn't found.
     *
     * @param MethodList $subject
     * @param \Magento\Framework\View\Element\AbstractBlock|false $result
     * @param Template $block
     * @param MethodInterface $method
     * @return \Magento\Framework\View\Element\AbstractBlock|false
     */
    public function afterGetMethodBlock(MethodList $subject, $result, Template $block, MethodInterface $method)
    {
        // If Hyva already found a matched layout block, return it unmodified.
        if ($result !== false) {
            return $result;
        }

        // For dynamic WhitelabelMachineName payment methods, map to our base template block.
        if (strpos($method->getCode(), 'weareplanet_payment_') === 0) {
            $child = $block->getChildBlock('checkout.payment.method.weareplanet');
            if ($child) {
                return $child->setData('method', $method);
            }
        }

        return $result;
    }
}
