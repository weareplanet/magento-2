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
namespace WeArePlanet\Payment\Model\Config\Structure;

use Magento\Config\Model\Config\SchemaLocator;
use Magento\Config\Model\Config\Structure\Converter;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\View\TemplateEngine\Xhtml\CompilerInterface;
use WeArePlanet\Payment\Model\Config\Dom;

/**
 * Reader to retrieve system configuration from system.xml files.
 * Merges configuration and caches it.
 */
class Reader extends \Magento\Config\Model\Config\Structure\Reader
{
    /**
     * Create configuration merger instance.
     *
     * @return Dom
     */
    public function createConfigMerger()
    {
        return $this->_createConfigMerger(Dom::class, Dom::SYSTEM_INITIAL_CONTENT);
    }

    /**
     * Process configuration document content.
     *
     * @param string $content
     * @return string
     */
    public function processDocument($content)
    {
        return $this->processingDocument($content);
    }
}
