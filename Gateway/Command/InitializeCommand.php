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
namespace WeArePlanet\Payment\Gateway\Command;

use Magento\Framework\Math\Random;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use WeArePlanet\Payment\Api\TokenInfoRepositoryInterface;
use WeArePlanet\Payment\Helper\Data as Helper;
use WeArePlanet\Payment\Model\Service\Order\TransactionService;
use WeArePlanet\Sdk\Model\Token;

/**
 * Payment gateway command to initialize a payment.
 */
class InitializeCommand implements CommandInterface
{

    /**
     *
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     *
     * @var Random
     */
    private $random;

    /**
     *
     * @var Helper
     */
    private $helper;

    /**
     *
     * @var TransactionService
     */
    private $transactionService;

    /**
     *
     * @var TokenInfoRepositoryInterface
     */
    private $tokenInfoRepository;

    /**
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param Random $random
     * @param Helper $helper
     * @param TransactionService $transactionService
     * @param TokenInfoRepositoryInterface $tokenInfoRepository
     */
    public function __construct(CartRepositoryInterface $quoteRepository, Random $random, Helper $helper,
        TransactionService $transactionService, TokenInfoRepositoryInterface $tokenInfoRepository)
    {
        $this->quoteRepository = $quoteRepository;
        $this->random = $random;
        $this->helper = $helper;
        $this->transactionService = $transactionService;
        $this->tokenInfoRepository = $tokenInfoRepository;
    }

    /**
     * An invoice is created and the transaction updated to match the order and confirmed.
     * The order state is set to {@link Order::STATE_PENDING_PAYMENT}.
     *
     * @see CommandInterface::execute()
     */
    public function execute(array $commandSubject)
    {
        $stateObject = SubjectReader::readStateObject($commandSubject);

        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = SubjectReader::readPayment($commandSubject)->getPayment();

        /** @var Order $order */
        $order = $payment->getOrder();

        $order->setCanSendNewEmailFlag(false);
        $payment->setAmountAuthorized($order->getTotalDue());
        $payment->setBaseAmountAuthorized($order->getBaseTotalDue());

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($order->getQuoteId());

        if (! $quote->getWeareplanetSpaceId() || ! $quote->getWeareplanetTransactionId()) {
            throw new \InvalidArgumentException('The WeArePlanet payment transaction is not set on the quote.');
        }

        if ($order->getWeareplanetSpaceId() != null ||
            $order->getWeareplanetTransactionId() != null) {
            throw new \InvalidArgumentException(
                'The WeArePlanet payment transaction has already been set on the order.');
        }

        $order->setWeareplanetSpaceId($quote->getWeareplanetSpaceId());
        $order->setWeareplanetTransactionId($quote->getWeareplanetTransactionId());
        $order->setWeareplanetSecurityToken($this->random->getUniqueHash());

        $stateObject->setState(Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);

        if ($this->helper->isAdminArea()) {
            // Tell the order to apply the charge flow after it is saved.
            $order->setWeareplanetChargeFlow(true);
            $order->setWeareplanetToken($this->getToken($quote));
        }
    }

    private function getToken(Quote $quote)
    {
        if ($this->helper->isAdminArea()) {
            $tokenInfoId = $quote->getPayment()->getData('weareplanet_token');
            if ($tokenInfoId) {
                $tokenInfo = $this->tokenInfoRepository->get($tokenInfoId);
                $token = new Token();
                $token->setId($tokenInfo->getTokenId());
                return $token;
            }
        }
    }
}