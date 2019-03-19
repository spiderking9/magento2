<?php

namespace Wstechlab\LayeredNavigation\Plugin\Controller\ProductsPage;

class View
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
    }

    public function afterExecute(
        \Mageplaza\LayeredNavigationUltimate\Controller\ProductsPage\View $action,
        $result
    ) {
        $currentPage = $this->_coreRegistry->registry('current_product_page');
        if ($currentPage->getRoute()) {
            $currentCategory = $this->_coreRegistry->registry('current_category');
            $currentCategory->setCustomProductsPage($currentPage->getRoute());
        }

        return $result;
    }
}