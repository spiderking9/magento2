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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column\Renderer;

use Blackbird\ContentManager\Api\Data\FlagInterface;

/**
 * Flag grid column
 *
 * Class Flag
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Widget\Grid\Column\Renderer
 */
class Flag extends AbstractRenderer
{
    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory
     */
    protected $_flagCollectionFactory;

    /**
     * Flag constructor
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory
     * @param \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory $flagCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\Content\CollectionFactory $contentCollectionFactory,
        \Blackbird\ContentManager\Model\ResourceModel\Flag\CollectionFactory $flagCollectionFactory,
        array $data = []
    ) {
        $this->_flagCollectionFactory = $flagCollectionFactory;
        parent::__construct($context, $contentCollectionFactory, $data);
    }

    /**
     * Render row store views flags
     *
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '';
        $data = $row->getData();
        $content = $this->getContent($data['entity_id']);

        if ($content) {
            $storeIds = $content->getStoreIds();

            foreach ($this->getFlags($storeIds) as $flag) {
                $html .= '<div><a href="' . $this->getUrl('contentmanager/content/edit', [
                        'id' => $content->getId(),
                        'store' => $flag->getStoreId(),
                    ]) . '">' . '<img src="' . $this->getViewFileUrl(FlagInterface::FLAG_PATH) . '/' . $flag->getValue() . '" class="store-flag-icon" alt="' . $flag->getValue() . '" />' . '</a></div>';
            }
        }

        return $html;
    }

    /**
     * Retrieves the stores flag
     *
     * @param array|int|null $storeIds
     * @return \Blackbird\ContentManager\Model\ResourceModel\Flag\Collection
     */
    protected function getFlags($storeIds = null)
    {
        $flags = $this->_flagCollectionFactory->create();

        if (!is_null($storeIds)) {
            $flags->addFieldToFilter(FlagInterface::ID, $storeIds);
        }

        return $flags;
    }
}
