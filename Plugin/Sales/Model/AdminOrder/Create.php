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
namespace WeArePlanet\Payment\Plugin\Sales\Model\AdminOrder;

use WeArePlanet\Payment\Model\Payment\Method\Adapter;

class Create
{

    public function beforeCreateOrder(\Magento\Sales\Model\AdminOrder\Create $subject)
    {
        if ($subject->getQuote()
            ->getPayment()
            ->getMethodInstance() instanceof Adapter) {
            $subject->setSendConfirmation(false);
        }
    }
}