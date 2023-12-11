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
namespace WeArePlanet\Payment\Plugin\Config\Model\Config\Structure;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use WeArePlanet\Payment\Api\PaymentMethodConfigurationRepositoryInterface;
use WeArePlanet\Payment\Api\Data\PaymentMethodConfigurationInterface;
use WeArePlanet\Payment\Model\PaymentMethodConfiguration;
use WeArePlanet\Payment\Model\Config\Structure\Reader;

/**
 * Interceptor to dynamically extend config structure with the WeArePlanet payment method data.
 */
class Converter
{

    /**
     *
     * @var PaymentMethodConfigurationRepositoryInterface
     */
    private $paymentMethodConfigurationRepository;

    /**
     *
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     *
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     *
     * @var Reader
     */
    private $reader;

    /**
     *
     * @var ModuleDirReader
     */
    private $moduleReader;

    /**
     *
     * @var DriverPool
     */
    private $driverPool;

    /**
     *
     * @var string
     */
    private $template;

    /**
     *
     * @param PaymentMethodConfigurationRepositoryInterface $paymentMethodConfigurationRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ResourceConnection $resourceConnection
     * @param Reader $reader
     * @param ModuleDirReader $moduleReader
     * @param DriverPool $driverPool
     */
    public function __construct(PaymentMethodConfigurationRepositoryInterface $paymentMethodConfigurationRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder, ResourceConnection $resourceConnection,
        Reader $reader, ModuleDirReader $moduleReader, DriverPool $driverPool)
    {
        $this->paymentMethodConfigurationRepository = $paymentMethodConfigurationRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->reader = $reader;
        $this->moduleReader = $moduleReader;
        $this->driverPool = $driverPool;
    }

    /**
     * @param \Magento\Config\Model\Config\Structure\Converter $subject
     * @param mixed $source
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeConvert(\Magento\Config\Model\Config\Structure\Converter $subject, $source)
    {
        if (! $this->isTableExists()) {
            return [
                $source
            ];
        }

        $configMerger = $this->reader->createConfigMerger();
        $configMerger->setDom($source);

        $searchCriteria = $this->searchCriteriaBuilder->addFilter(PaymentMethodConfigurationInterface::STATE,
            [
                PaymentMethodConfiguration::STATE_ACTIVE,
                PaymentMethodConfiguration::STATE_INACTIVE
            ], 'in')->create();

        $configurations = $this->paymentMethodConfigurationRepository->getList($searchCriteria)->getItems();
        foreach ($configurations as $configuration) {
            $configMerger->merge($this->reader->processDocument($this->generateXml($configuration)));
        }

        return [
            $configMerger->getDom()
        ];
    }

    /**
     * @param PaymentMethodConfigurationInterface $configuration
     * @return array|string|string[]
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function generateXml(PaymentMethodConfigurationInterface $configuration)
    {
        return str_replace([
            '{id}',
            '{name}'
        ], [
            $configuration->getEntityId(),
            $configuration->getConfigurationName()
        ], $this->getTemplate());
    }

    /**
     * Gets whether the payment method configuration database table exists.
     *
     * @return boolean
     */
    private function isTableExists()
    {
        return $this->resourceConnection->getConnection()->isTableExists(
            $this->resourceConnection->getTableName('weareplanet_payment_method_configuration'));
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getTemplate()
    {
        if ($this->template == null) {
            $templatePath = $this->moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_ETC_DIR,
                'WeArePlanet_Payment') . '/adminhtml/system-method-template.xml';
            $this->template = $this->driverPool->getDriver(DriverPool::FILE)->fileGetContents($templatePath);
        }
        return $this->template;
    }
}