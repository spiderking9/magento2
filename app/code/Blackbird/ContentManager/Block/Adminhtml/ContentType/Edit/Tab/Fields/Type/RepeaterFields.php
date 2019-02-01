<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package             Blackbird_ContentManager
 * @copyright           Copyright (c) 2018 Blackbird (http://black.bird.eu)
 * @author              Blackbird Team
 * @license             http://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type;

use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;
use Blackbird\ContentManager\Model\ContentType;

/**
 * Class RepeaterFields
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type
 */
class RepeaterFields extends \Blackbird\ContentManager\Block\Adminhtml\ContentType\Edit\Tab\Fields\Type\AbstractType
{
    /**
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::contenttype/edit/tab/fields/type/repeater_fields.phtml';

    /**
     * @var \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory
     */
    protected $_contentTypeCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        array $data = []
    ) {
        $this->_contentTypeCollectionFactory = $contentTypeCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve the content types options
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getContentTypesSelectHtml()
    {
        $select = $this->getLayout()
            ->createBlock(\Magento\Framework\View\Element\Html\Select::class)
            ->setData([
                'id' => $this->getFieldsetId() . '_<%- data.id %>_' . $this->getFieldId() . '_<%- data.field_id %>_content_type',
                'class' => 'select required-entry',
                'title' => 'Content Type',
            ])
            ->setName($this->getFieldsetName() . '[<%- data.id %>]' . $this->getFieldName() . '[<%- data.field_id %>][content_type]')
            ->setOptions($this->getOptionsArray());

        return $select->getHtml();
    }

    /**
     * Retrieve the options array of ContentType types of repeater fields
     *
     * @return array
     */
    protected function getOptionsArray()
    {
        $optionsArray = [];
        $contentTypeCollection = $this->_contentTypeCollectionFactory->create()
            ->addFieldToFilter(ContentType::VISIBILITY, Visibility::REPEATER_FIELD);

        foreach ($contentTypeCollection as $contentType) {
            $optionsArray[] = [
                'label' => $this->escapeHtml($contentType->getTitle()),
                'value' => $contentType->getIdentifier(),
            ];
        }

        return $optionsArray;
    }
}
