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

namespace Blackbird\ContentManager\Block\View\Field;

/**
 * Class Product
 *
 * @package Blackbird\ContentManager\Block\View\Field
 */
class Product extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productCollection;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockHelper;

    /**
     * Product constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->stockHelper = $stockHelper;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * Get the product collection
     *
     * @param array $attributes
     * @param boolean $checkStatus
     * @param boolean $checkVisibility
     * @param boolean $checkStock
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection(
        array $attributes,
        $checkStatus = true,
        $checkVisibility = true,
        $checkStock = false
    ) {
        $collection = $this->getContent()
            ->getProductCollection($this->getIdentifier(),
                array_merge($attributes, ['sku', 'price', 'final_price', 'url_key']));

        if ($checkStatus) {
            $collection->addAttributeToFilter('status', 1);
        }
        if ($checkVisibility) {
            $collection->setVisibility($this->productVisibility->getVisibleInSearchIds());
        }
        if ($checkStock) {
            $this->stockHelper->addInStockFilterToCollection($collection);
        }

        return $collection;
    }

    /**
     * Retrieve loaded product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->getProductCollection(['name', 'small_image', 'special_price']);
        }

        return $this->_productCollection;
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array|string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);

        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ],
        ];
    }

    /**
     * Retrieve the product price render
     *
     * @return \Magento\Framework\Pricing\Render|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getPriceRender()
    {
        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()
                ->createBlock('Magento\Framework\Pricing\Render', 'product.price.render.default',
                    ['data' => ['price_render_handle' => 'catalog_product_prices']]);
        }

        return $priceRender;
    }

    /**
     * Render detail list
     *
     * @return \Magento\Framework\View\Element\BlockInterface|\Magento\Framework\View\Element\RendererList
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getDetailsRendererList()
    {
        $renderer = parent::getDetailsRendererList();

        if (!$renderer instanceof BlockInterface) {
            $renderer = $this->getLayout()->createBlock('Magento\Framework\View\Element\RendererList');
            $renderer->setChild('default', $this->getLayout()->createBlock('Magento\Framework\View\Element\Template'));
            $configurable = $this->getLayout()
                ->createBlock('Magento\Swatches\Block\Product\Renderer\Listing\Configurable');
            $configurable->setTemplate('Magento_Swatches::product/listing/renderer.phtml');
            $renderer->setChild('configurable', $configurable);
            $this->setChild('details.renderers', $renderer);
        }

        return $renderer;
    }

    /**
     * @todo move to abstract generic class
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $content = $this->getContent();
        $contentType = $content->getContentType();
        $type = $this->getType();

        // Test applying content/view/"content type"/field/product/"product type"-"ID".phtml
        $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/product/' . $type . '-' . $content->getId() . '.phtml');

        if (!$this->getTemplateFile()) {
            // Test applying content/view/"content type"/field/product/"product type"-"content type".phtml
            $this->setTemplate('Blackbird_ContentManager::content/view/' . $contentType->getIdentifier() . '/view/field/product/' . $type . '.phtml');

            if (!$this->getTemplateFile()) {
                // Applying default content/view/default/field/product/type.phtml
                $this->setTemplate('Blackbird_ContentManager::content/view/default/view/field/product/' . $type . '.phtml');
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        if ($this->getType() !== 'product_all') {
            return $this;
        }

        $toolbar = $this->getToolbarBlock();
        $pager = $this->getLayout()
            ->createBlock(\Magento\Theme\Block\Html\Pager::class,
                'product_list_toolbar_pager_' . $this->getNameInLayout());
        $toolbar->setChild('product_list_toolbar_pager', $pager);

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch('catalog_block_product_list_collection',
            ['collection' => $this->_getProductCollection()]);

        $this->_getProductCollection()->load();

        return $this;
    }
}
