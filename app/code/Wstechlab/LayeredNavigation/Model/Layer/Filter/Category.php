<?php

namespace Wstechlab\LayeredNavigation\Model\Layer\Filter;

use Mageplaza\LayeredNavigation\Helper\Data as LayerHelper;

class Category extends \Mageplaza\LayeredNavigation\Model\Layer\Filter\Category
{
    /** @var \Magento\Framework\Escaper */
    private $escaper;

    /** @var  \Magento\Catalog\Model\Layer\Filter\DataProvider\Category */
    private $dataProvider;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        LayerHelper $moduleHelper,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $escaper, $categoryDataProviderFactory, $moduleHelper, $data);
        $this->escaper = $escaper;
        $this->dataProvider  = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
    }

    /**
     * @inheritdoc
     */
    protected function _getItemsData()
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return parent::_getItemsData();
        }

        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();

        if ($this->_isFilter) {
            $productCollection = $productCollection->getCollectionClone()
                ->removeAttributeSearch('category_ids');
        }

        $optionsFacetedData = $productCollection->getFacetedData('category');
        $category           = $this->dataProvider->getCategory();
        $categories         = $category->getChildrenCategories();

        $collectionSize = $productCollection->getSize();

        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                $count = isset($optionsFacetedData[$category->getId()]) ? $optionsFacetedData[$category->getId()]['count'] : 0;
                if ($category->getIsActive()
                    && $this->_moduleHelper->getFilterModel()->isOptionReducesResults($this, $count, $collectionSize)
                ) {
                    $this->itemDataBuilder->addItemData(
                        $this->escaper->escapeHtml($category->getName()),
                        $category->getId(),
                        $count
                    );

                    // start adding subcategories
                    $subcategories = $category->getChildrenCategories();
                    foreach ($subcategories as $subcategory) {
                        $count = isset($optionsFacetedData[$subcategory->getId()]) ? $optionsFacetedData[$subcategory->getId()]['count'] : 0;
                        if (
                            $subcategory->getIsActive() &&
                            $this->_moduleHelper->getFilterModel()->isOptionReducesResults($this, $count, $collectionSize)
                        ) {
                            $this->itemDataBuilder->addItemData(
                                $this->escaper->escapeHtml($subcategory->getName()),
                                $subcategory->getId(),
                                $count
                            );
                        }
                    }
                    // end adding subcategories

                }
            }
        }
        return $this->itemDataBuilder->build();
    }
}