<?php

namespace Wstechlab\LayeredNavigation\Block\Type;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Mageplaza\LayeredNavigationPro\Helper\Data as LayerHelper;

/**
 * Class OptionList
 * @package Wstechlab\LayeredNavigation\Block\Type
 */
class OptionList extends \Mageplaza\LayeredNavigationPro\Block\Type\OptionList
{
    const CATEGORY_ATTRIBUTE_CODE = 'cat';
	
    private $subcategoryLevel;
    private $subcategories = [];

    /** @var string Path to template file. */
    protected $_template = 'Mageplaza_LayeredNavigationPro::type/list.phtml';

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    public function __construct(
        Template\Context $context,
        LayerHelper $helper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $helper, $data);
        $this->categoryRepository = $categoryRepository;
    }

    private function addChildCategory($parentId, $item)
    {
        $this->subcategories[$parentId][] = $item;
        return $this;
    }

    public function rebuildItems()
    {
        if ($this->getFilter()->getRequestVar() !== self::CATEGORY_ATTRIBUTE_CODE) {
            return $this->getItems();
        }
	$items = [];
        foreach ($this->getItems() as $item) {
            $category = $this->categoryRepository->get($item->getValue());
	    if (!$this->subcategoryLevel) {
                 $this->subcategoryLevel = $category->getLevel() + 1;
            }
            if ($this->isSubcategory($item)) {
                $this->addChildCategory($category->getParentId(), $item);
            } else {
		$items[] = $item;
	    }
        }
        return $items;
    }

    public function getSubcategories($categoryId)
    {
	if ($this->getFilter()->getRequestVar() !== self::CATEGORY_ATTRIBUTE_CODE) {
            return;
        }

        return isset($this->subcategories[$categoryId]) ? $this->subcategories[$categoryId] : [];
    }	

    /**
     * Check if category could be qualified as subcategory
     * @param $category
     * @return bool
     */
    public function isSubcategory($category)
    {
        try {
            $category = $this->categoryRepository->get($category->getValue());
        } catch (NoSuchEntityException $noSuchEntityException) {
            return false;
        }
        return ($this->subcategoryLevel <= $category->getLevel());
    }
}
