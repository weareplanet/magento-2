<?php
/**
 WeArePlanet Magento 2
 *
 * This Magento 2 extension enables to process payments with WeArePlanet (https://www.weareplanet.com).
 *
 * @package WeArePlanet_Payment
 * @author Planet Merchant Services Ltd (https://www.weareplanet.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)

 */
namespace WeArePlanet\Payment\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use WeArePlanet\Payment\Api\PaymentMethodConfigurationRepositoryInterface;
use WeArePlanet\Payment\Api\TokenInfoManagementInterface;
use WeArePlanet\Payment\Api\TokenInfoRepositoryInterface;
use WeArePlanet\Payment\Api\Data\TokenInfoInterface;
use WeArePlanet\Payment\Helper\Data as Helper;
use WeArePlanet\Sdk\Model\CreationEntityState;
use WeArePlanet\Sdk\Model\EntityQuery;
use WeArePlanet\Sdk\Model\EntityQueryFilter;
use WeArePlanet\Sdk\Model\EntityQueryFilterType;
use WeArePlanet\Sdk\Model\TokenVersion;
use WeArePlanet\Sdk\Model\TokenVersionState;
use WeArePlanet\Sdk\Service\TokenService;
use WeArePlanet\Sdk\Service\TokenVersionService;

/**
 * Token info management service.
 */
class TokenInfoManagement implements TokenInfoManagementInterface
{

    /**
     *
     * @var Helper
     */
    private $helper;

    /**
     *
     * @var TokenInfoRepositoryInterface
     */
    private $tokenInfoRepository;

    /**
     *
     * @var TokenInfoFactory
     */
    private $tokenInfoFactory;

    /**
     *
     * @var PaymentMethodConfigurationRepositoryInterface
     */
    private $paymentMethodConfigurationRepository;

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param Helper $helper
     * @param TokenInfoRepositoryInterface $tokenInfoRepository
     * @param TokenInfoFactory $tokenInfoFactory
     * @param PaymentMethodConfigurationRepositoryInterface $paymentMethodConfigurationRepository
     * @param ApiClient $apiClient
     */
    public function __construct(Helper $helper, TokenInfoRepositoryInterface $tokenInfoRepository,
        TokenInfoFactory $tokenInfoFactory,
        PaymentMethodConfigurationRepositoryInterface $paymentMethodConfigurationRepository, ApiClient $apiClient)
    {
        $this->helper = $helper;
        $this->tokenInfoRepository = $tokenInfoRepository;
        $this->tokenInfoFactory = $tokenInfoFactory;
        $this->paymentMethodConfigurationRepository = $paymentMethodConfigurationRepository;
        $this->apiClient = $apiClient;
    }

    /**
     * @param int $spaceId
     * @param int $tokenVersionId
     * @return void
     */
    public function updateTokenVersion($spaceId, $tokenVersionId)
    {
        $tokenVersion = $this->apiClient->getService(TokenVersionService::class)->read($spaceId, $tokenVersionId);
        $this->updateTokenVersionInfo($tokenVersion);
    }

    /**
     * @param int $spaceId
     * @param int $tokenId
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function updateToken($spaceId, $tokenId)
    {
        $query = new EntityQuery();
        $filter = new EntityQueryFilter();
        $filter->setType(EntityQueryFilterType::_AND);
        $filter->setChildren(
            [
                $this->helper->createEntityFilter('token.id', $tokenId),
                $this->helper->createEntityFilter('state', TokenVersionState::ACTIVE)
            ]);
        $query->setFilter($filter);
        $query->setNumberOfEntities(1);
        $tokenVersions = $this->apiClient->getService(TokenVersionService::class)->search($spaceId, $query);
        if (! empty($tokenVersions)) {
            $this->updateTokenVersionInfo($tokenVersions[0]);
        } else {
            try {
                $tokenInfo = $this->tokenInfoRepository->getByTokenId($spaceId, $tokenId);
                $this->tokenInfoRepository->delete($tokenInfo);
            } catch (NoSuchEntityException $e) {}
        }
    }

    /**
     * @param TokenVersion $tokenVersion
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function updateTokenVersionInfo(TokenVersion $tokenVersion)
    {
        try {
            $tokenInfo = $this->tokenInfoRepository->getByTokenId($tokenVersion->getLinkedSpaceId(),
                $tokenVersion->getToken()
                    ->getId());
        } catch (NoSuchEntityException $e) {
            $tokenInfo = $this->tokenInfoFactory->create();
        }

        if (! \in_array($tokenVersion->getToken()->getState(),
            [
                CreationEntityState::ACTIVE,
                CreationEntityState::INACTIVE
            ])) {
            if ($tokenInfo->getId()) {
                $this->tokenInfoRepository->delete($tokenInfo);
            }
        } else {
            $tokenInfo->setData(TokenInfoInterface::CUSTOMER_ID, $tokenVersion->getToken()
                ->getCustomerId());
            $tokenInfo->setData(TokenInfoInterface::NAME, $tokenVersion->getName());
            try {
                $tokenInfo->setData(TokenInfoInterface::PAYMENT_METHOD_ID,
                    $this->paymentMethodConfigurationRepository->getByConfigurationId($tokenVersion->getLinkedSpaceId(),
                        $tokenVersion->getPaymentConnectorConfiguration()
                            ->getPaymentMethodConfiguration()
                            ->getId())
                        ->getId());
                $tokenInfo->setData(TokenInfoInterface::CONNECTOR_ID,
                    $tokenVersion->getPaymentConnectorConfiguration()
                        ->getId());
            } catch (\Error $e) { //Catching, but not showing, ticket WAL-69414
                $error = $e;
            }

            $tokenInfo->setData(TokenInfoInterface::SPACE_ID, $tokenVersion->getLinkedSpaceId());
            $tokenInfo->setData(TokenInfoInterface::STATE, $tokenVersion->getToken()
                ->getState());
            $tokenInfo->setData(TokenInfoInterface::TOKEN_ID, $tokenVersion->getToken()
                ->getId());
            $this->tokenInfoRepository->save($tokenInfo);
        }
    }

    /**
     * @param TokenInfoInterface $token
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteToken(TokenInfoInterface $token)
    {
        $this->apiClient->getService(TokenService::class)->delete($token->getSpaceId(), $token->getTokenId());
        $this->tokenInfoRepository->delete($token);
    }
}