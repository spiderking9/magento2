<?php
namespace Wstechlab\Catalog\Block\Product;

use Magento\Catalog\Block\Product\View as MagentoView;

class View extends MagentoView
{
    /**
     * @return float
     */
    public function getStockItem()
    {
        $product = $this->getProduct();

        return $this->stockRegistry->getStockItem($product->getId())->getQty();
    }

    /**
     * @return float
     */
    public function getNotifyStockQty()
    {
        $product = $this->getProduct();

        return $this->stockRegistry->getStockItem($product->getId())->getNotifyStockQty();
    }
}
