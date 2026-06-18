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
namespace WeArePlanet\Payment\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\ScopeInterface;
use WeArePlanet\Payment\Model\Config\Source\IntegrationMethod;

/**
 * Observer to set the order place redirect URL so that alternative checkouts (like Hyva)
 * correctly redirect the customer to the payment page.
 */
class SetOrderPlaceRedirectUrl implements ObserverInterface
{

    /**
     * Configuration interface for retrieving the integration mode selected by the merchant.
     *
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Constructor for the SetOrderPlaceRedirectUrl observer.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Sets the order place redirect URL on the quote's payment object.
     *
     * We do this to ensure compatibility with alternative checkouts like Hyva Checkout,
     * which read this property natively to handle the redirect to the payment page after the order is placed.
     * Standard Magento checkout ignores this property if handled via custom validators in JavaScript.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var Quote|null $quote */
        $quote = $observer->getEvent()->getQuote();

        if (!$quote) {
            return;
        }

        $payment = $quote->getPayment();

        if (!$payment) {
            return;
        }

        $paymentMethod = $payment->getMethod();

        // Check if the payment method belongs to our plugin before attempting to change the redirect URL.
        if (\strpos((string) $paymentMethod, 'weareplanet_payment_') === 0) {
            $integrationMethod = $this->scopeConfig->getValue(
                'weareplanet_payment/checkout/integration_method',
                ScopeInterface::SCOPE_STORE,
                $quote->getStoreId(),
            );

            // Since both standard and Hyva checkouts respect this fallback, we only set it for payment page integration
            if ($integrationMethod == IntegrationMethod::PAYMENT_PAGE) {
                // Generate the absolute URL to our redirect controller.
                $redirectUrl = $quote->getStore()->getUrl(
                    'weareplanet_payment/checkout/paymentPageUrl',
                    ['_secure' => true,],
                );

                $payment->setOrderPlaceRedirectUrl(
                    $redirectUrl,
                );
            }
        }
    }
}
