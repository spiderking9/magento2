<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category        Blackbird
 * @package         Blackbird_ContentManager
 * @copyright       Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author          Blackbird Team
 * @license         https://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Ui\Component\Listing\Column;

use Blackbird\ContentManager\Api\Data\FlagInterface;
use Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory as FlagCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Ui\Component\Listing\Columns\Column;
use Psr\Log\LoggerInterface;

/**
 * Class Flag
 *
 * @package Blackbird\ContentManager\Ui\Component\Listing\Column
 */
class Flag extends Column
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * System store
     *
     * @var SystemStore
     */
    protected $systemStore;

    /**
     * Store manager
     *
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var FlagCollectionFactory
     */
    protected $flagCollectionFactory;

    /**
     * @var AssetRepository
     */
    protected $_assetRepo;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * Flag constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AssetRepository $assetRepo
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param SystemStore $systemStore
     * @param Escaper $escaper
     * @param StoreManager $storeManager
     * @param FlagCollectionFactory $flagCollectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        AssetRepository $assetRepo,
        RequestInterface $request,
        LoggerInterface $logger,
        SystemStore $systemStore,
        Escaper $escaper,
        StoreManager $storeManager,
        FlagCollectionFactory $flagCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_logger = $logger;
        $this->_request = $request;
        $this->_assetRepo = $assetRepo;
        $this->systemStore = $systemStore;
        $this->escaper = $escaper;
        $this->storeManager = $storeManager;
        $this->flagCollectionFactory = $flagCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';
        if (!empty($item[$this->getName()])) {
            $origStores = $item[$this->getName()];
        }

        if (empty($origStores)) {
            return '';
        }
        if (!is_array($origStores)) {
            $origStores = [$origStores];
        }

        // Flag for each store view
        foreach ($this->getFlags($origStores) as $flag) {
            $content .= '<div><img src="' . $this->getViewFileUrl(FlagInterface::FLAG_PATH . $flag->getValue()) . '" class="store-flag-icon" alt="' . $flag->getValue() . '" /></div>';
        }

        return $content;
    }

    /**
     * Retrieves the stores flag
     *
     * @param array|int $storeIds
     * @return \Blackbird\ContentManager\Model\ResourceModel\Flag\Collection
     */
    protected function getFlags($storeIds)
    {
        $collection = $this->flagCollectionFactory->create();

        if ((is_array($storeIds) && !empty($storeIds)) || is_numeric($storeIds)) {
            $collection->addFieldToFilter(FlagInterface::ID, $storeIds);
        }

        return $collection;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    private function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->_request->isSecure()], $params);

            return $this->_assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);

            return $this->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->storeManager->isSingleStoreMode()) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }
}
