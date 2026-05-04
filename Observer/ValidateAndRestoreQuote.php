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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Checkout\Model\Session as CheckoutSession;
use WeArePlanet\Payment\Model\Service\Order\TransactionService;
use WeArePlanet\Sdk\Model\Transaction;
use WeArePlanet\Sdk\Model\TransactionState;

/**
 * Observer to validate and control quote restoration.
 */
class ValidateAndRestoreQuote implements ObserverInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     *
     * @param CheckoutSession $checkoutSession
     * @param TransactionService $transactionService
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        TransactionService $transactionService
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->transactionService = $transactionService;
    }

    /**
     * Validate and restore the quote.
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        // After placeOrder, the session's current quote is a new empty one.
        // The quote we need to reactivate is referenced by the last real order.
        $order = $this->checkoutSession->getLastRealOrder();

        // Idempotent: if there's no last order (e.g. restoreQuote already ran
        // earlier in this request and unset lastRealOrderId), there's nothing
        // to do — silently return instead of throwing.
        if (!$order || !$order->getId()) {
            return;
        }

        // Block restore only when the WeArePlanet transaction is in a
        // terminal paid state. Magento's order state is unreliable here: the
        // order can sit in `processing` immediately after placeOrder while
        // the WeArePlanet transaction is still CONFIRMED/PROCESSING (e.g.,
        // the customer is on the 3DS page).
        $spaceId = $order->getWeareplanetSpaceId();
        $transactionId = $order->getWeareplanetTransactionId();
        if ($spaceId && $transactionId) {
            try {
                $transaction = $this->transactionService->getTransaction($spaceId, $transactionId);
                if ($transaction instanceof Transaction) {
                    $paidStates = [
                        TransactionState::AUTHORIZED,
                        TransactionState::COMPLETED,
                        TransactionState::FULFILL,
                    ];
                    if (in_array($transaction->getState(), $paidStates, true)) {
                        throw new LocalizedException(
                            __('Your cart has already been paid for and cannot be restored.')
                        );
                    }
                }
            } catch (LocalizedException $e) {
                throw $e;
            } catch (\Exception $e) {
                // If the WeArePlanet API is unreachable, fail open and let
                // the restore proceed — better than stranding the customer.
            }
        }

        // Reactivates the quote and dispatches `restore_quote`. The abandoned
        // order stays in `pending_payment` until the WeArePlanet webhook
        // reports FAILED/DECLINED and FailedCommand/DeclineCommand cancels it.
        $this->checkoutSession->restoreQuote();
    }
}
