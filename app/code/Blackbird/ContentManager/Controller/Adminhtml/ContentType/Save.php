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

namespace Blackbird\ContentManager\Controller\Adminhtml\ContentType;

use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ContentType\CustomFieldset;
use Blackbird\ContentManager\Model\ContentType\Layout\AbstractModel as ItemModel;

/**
 * Class Save
 *
 * @package Blackbird\ContentManager\Controller\Adminhtml\ContentType
 */
class Save extends \Blackbird\ContentManager\Controller\Adminhtml\ContentType
{
    /**
     * Available layout item
     * (even put group in first place)
     *
     * @todo refactor
     * @var array
     */
    protected $_availableLayoutItem = [
        'group',
        'field',
        'block',
    ];

    /**
     * @var \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields
     */
    protected $customFieldsSource;

    /**
     * @var \Magento\Framework\View\Model\Layout\Update\ValidatorFactory
     */
    protected $_validatorFactory;

    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Config\Model\Config\Source\Locale
     */
    protected $_locale;

    /**
     * Save constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory
     * @param \Blackbird\ContentManager\Model\Factory $modelFactory
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldsSource
     * @param \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory
     * @param \Magento\Config\Model\Config\Source\Locale\Currency $currency
     * @param \Magento\Config\Model\Config\Source\Locale $locale
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Blackbird\ContentManager\Model\ResourceModel\ContentType\CollectionFactory $contentTypeCollectionFactory,
        \Blackbird\ContentManager\Model\Factory $modelFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Blackbird\ContentManager\Model\Config\Source\ContentType\CustomFields $customFieldsSource,
        \Magento\Framework\View\Model\Layout\Update\ValidatorFactory $validatorFactory,
        \Magento\Config\Model\Config\Source\Locale\Currency $currency,
        \Magento\Config\Model\Config\Source\Locale $locale
    ) {
        $this->customFieldsSource = $customFieldsSource;
        $this->_validatorFactory = $validatorFactory;
        $this->_currency = $currency;
        $this->_locale = $locale;
        parent::__construct($context, $coreRegistry, $datetime, $contentTypeCollectionFactory, $modelFactory,
            $cacheManager);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_initAction();
        $contentType = $this->_contentTypeModel;
        $data = $this->getRequest()->getParams();

        // FIX EXPORT CONTENT TYPE
        if ($contentType && $contentType->getId() && $this->getRequest()->getParam('back') === 'export') {
            $this->_forward('export');
        }

        //todo refactor: set before save
        if ($this->getRequest()->isPost() && !empty($data)) {

            // If we are editing an existing content type
            if ($contentType instanceof \Blackbird\ContentManager\Model\ContentType && is_numeric($contentType->getCtId())) {
                // Check if the content type identifier is unique
                if ($this->contentTypeIdentifierExists($data[$contentType::IDENTIFIER], $contentType->getCtId())) {
                    $this->messageManager->addErrorMessage(__('The content type identifier must be unique'));

                    return $this->resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                }
                $data[$contentType::UPDATE_TIME] = $this->_datetime->date();

            } else {
                // ...else we create a new content type
                $contentType = $this->_modelFactory->create(ContentType::class);

                $data[$contentType::CREATED_TIME] = $this->_datetime->date();

                // Check if the content type identifier is unique
                if ($this->contentTypeIdentifierExists($data[$contentType::IDENTIFIER])) {
                    $this->messageManager->addErrorMessage(__('The content type identifier must be unique'));

                    return $this->resultRedirect->setPath('*/*/edit');
                }

                // Init save of the content type
                try {
                    $contentType->setData($data['form_key']);

                    if (isset($data['title'])) {
                        $contentType->setData('title', $data['title']);
                    }

                    $contentType->save();
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e,
                        __('Something went wrong while saving the new content type.'));
                }
            }

            // Set ID of the content type
            $data[ContentType::ID] = $contentType->getCtId();

            /** Prepare Content Type */
            $contentType = $this->prepareContentType($contentType, $data);

            $this->_eventManager->dispatch('contentmanager_contenttype_prepare_save',
                ['post' => $contentType, 'request' => $this->getRequest()]);

            // Save content type
            try {
                $contentType->save();
                $this->messageManager->addSuccessMessage(__('The content type has been saved !'));

                // Do it only if it's a new content type or if the title has been changed
                if ($contentType->getOrigData(ContentType::TITLE) != $contentType->getData(ContentType::TITLE)) {
                    // Flush backend main menu cache
                    $this->flushBackendMainMenuCache();
                }

                $this->_getSession()->setFormData(false);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e,
                    __('Something went wrong while saving the content type: %1', $e->getMessage()));
            }

            $this->_getSession()->setFormData($data);

            /** Redirect Manager */
            switch ($this->getRequest()->getParam('back', 'edit')) {
                case 'new':
                    $path = '*/*/new';
                    $params = [];
                    break;
                case 'edit':
                    $path = '*/*/edit';
                    $params = [
                        'id' => $contentType->getId(),
                        'active_tab' => $this->getRequest()->getParam('active_tab'),
                    ];
                    break;
                case 'back':
                default:
                    $path = '*/*/';
                    $params = [];
                    break;
            }

            return $this->resultRedirect->setPath($path, $params);
        }

        return $this->resultRedirect->setPath('*/*/');
    }

    /**
     * Check if content type identifier is unique
     *
     * @param string $identifier
     * @param int $contentTypeId
     * @return bool
     */
    protected function contentTypeIdentifierExists($identifier, $contentTypeId = null)
    {
        $contentTypeCollection = $this->_contentTypeCollectionFactory->create();

        $exists = $contentTypeCollection->addFieldToFilter(ContentType::IDENTIFIER, $identifier);

        if (is_numeric($contentTypeId)) {
            $exists->addFieldToFilter(ContentType::ID, ['neq' => $contentTypeId]);
        }

        return ($exists->getSize() > 0);
    }

    /**
     * Prepare the Content Type
     *
     * @param ContentType $contentType
     * @param array $data
     * @return ContentType
     */
    protected function prepareContentType(ContentType $contentType, array $data)
    {
        // Breadcrumbs (serialize)
        $data = $this->prepareBreadcrumb($data);

        // Set data to the contenttype
        $contentType->setData($data);

        // Custom Fields
        if (!empty($data['contenttype'])) {
            // Custom Fields
            $contentType = $this->prepareCustomFields($contentType, $data['contenttype']);
        }

        // Layouts
        if (!empty($data['layout'])) {
            $contentType = $this->prepareLayouts($contentType, $data['layout']);
        }

        return $contentType;
    }

    /**
     * Prepare Breadcrumb (serialize array for saving)
     *
     * @param array $data
     * @return array
     */
    protected function prepareBreadcrumb(array $data)
    {
        if (!empty($data['breadcrumb_prev_name'])) {
            $data['breadcrumb_prev_name'] = serialize($data['breadcrumb_prev_name']);
        } else {
            $data['breadcrumb_prev_name'] = serialize('');
        }

        if (!empty($data['breadcrumb_prev_link'])) {
            $data['breadcrumb_prev_link'] = serialize($data['breadcrumb_prev_link']);
        } else {
            $data['breadcrumb_prev_link'] = serialize('');
        }

        return $data;
    }

    /**
     * Prepare fields of Custom Fields
     *
     * @param ContentType $contentType
     * @param array $data
     * @return ContentType
     */
    protected function prepareCustomFields(ContentType $contentType, array $data)
    {
        // List of identifiers already used
        $identifiersUsed = [];
        // Fieldsets to delete
        $fieldsetsToDelete = [];

        /** FIELDSETS */
        $fieldsets = !empty($data['fieldsets']) ? $data['fieldsets'] : [];
        foreach ($fieldsets as $fieldset) {

            $customFieldset = $this->initCustomFieldset($fieldset, $contentType->getCtId());

            /** FIELDS */
            $fields = !empty($fieldset['fields']) ? $fieldset['fields'] : [];
            foreach ($fields as $field) {
                // Check if the identifier is unique
                if (in_array($field['identifier'], $identifiersUsed) && $this->isIdentifierUsed($field['identifier'],
                        $field['option_id'])) {
                    $this->messageManager->addErrorMessage(__('The identifier "%1" is already used for another field or is a system identifier.',
                        $field['identifier']));
                    continue;
                }
                $identifiersUsed[] = $field['identifier'];

                // Set default values
                $field = $this->prepareCustomFieldData($field, $customFieldset->getId());

                // Create (attributes) or Update the custom field
                if (!empty($fieldset['is_delete'])) {
                    $field['is_delete'] = 1;
                }

                // Create or update the Custom Field
                $contentType->addCustomField($field);
            }

            // Delete fieldset if is no more
            if (!empty($fieldset['is_delete'])) {
                $fieldsetsToDelete[] = $customFieldset;
            }
        }

        try {
            // Save Custom Fields
            $contentType->saveCustomFields();
            // Delete fieldsets
            foreach ($fieldsetsToDelete as $fieldset) {
                $fieldset->delete();
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the custom fields.'));
        }

        return $contentType;
    }

    /**
     * Init, create or update the Custom Fieldset
     *
     * @param array $data
     * @param int $ctId
     * @return CustomFieldset
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function initCustomFieldset(array $data, $ctId)
    {
        /** @var CustomFieldset $customFieldset */
        $customFieldset = $this->_modelFactory->create(CustomFieldset::class);

        // Load custom fieldset if is existing
        if (!empty($data['fieldset_id'])) {
            $customFieldset->load($data['fieldset_id']);
        }
        // Set data to the custom fieldset
        $customFieldset->setTitle($data['title']);
        $customFieldset->setSortOrder($data['sort_order']);
        $customFieldset->setCtId($ctId);
        $customFieldset->save();

        return $customFieldset;
    }

    /**
     * Check if the identifier is not already used
     *
     * @param string $identifier
     * @param int $customFieldId
     * @return bool
     */
    protected function isIdentifierUsed($identifier, $customFieldId = null)
    {
        $exists = (bool)$this->customFieldsSource->getCustomFieldsByIdentifiers($identifier, $customFieldId)->getSize();

        if (!$exists) {
            //todo refactor move methods, remove static
            $identifiers = \Blackbird\ContentManager\Model\Config\Source\Content\Identifiers::toArray();
            // Check for content type and system identifiers
            $exists = in_array($identifier, $identifiers);
        }

        return $exists;
    }

    /**
     * Prepare and manage the custom field Data
     *
     * @param array $field
     * @param int $fieldsetId
     * @return array
     */
    protected function prepareCustomFieldData(array $field, $fieldsetId)
    {
        $typeFile = ['file', 'image'];

        // Delete temporary id
        unset($field['id']);

        // Link field with his fieldset
        $field['fieldset_id'] = $fieldsetId;

        // Fill empty data
        $field = $this->setDefaultFieldData($field);

        // Sanitize file path (if is a file type)
        if (in_array($field['type'], $typeFile)) {
            $field['file_path'] = $this->sanitizeFilePath($field['file_path']);
        }
        if ($field['type'] == 'currency') {
            $field['select'] = $this->_currency->toOptionArray();
        }
        if ($field['type'] == 'locale') {
            $field['select'] = $this->_locale->toOptionArray();
        }

        return $field;
    }

    /**
     * Set default values for image field
     *
     * @param array $field
     * @return array
     */
    protected function setDefaultFieldData(array $field)
    {
        $keys = [
            'is_searchable',
            'search_weight',
            'wysiwyg_editor',
            'keep_aspect_ratio',
            'img_alt',
            'img_url',
            'img_title',
            'crop',
        ];

        // Init default values
        foreach ($keys as $key) {
            if (!isset($field[$key])) {
                $field[$key] = 0;
            }
        }

        // Set the type if the field already exists
        $field['type'] = !isset($field['type']) ? $field['previous_type'] : $field['type'];

        return $field;
    }

    /**
     * Sanitize the file path string
     *
     * @param string $filePath
     * @return string
     */
    protected function sanitizeFilePath($filePath)
    {
        $filePath = str_replace('.', '', $filePath);
        $filePath = str_replace('//', '', $filePath);

        return $filePath;
    }

    /**
     * Prepare and save all layouts items of the current content type
     *
     * @param ContentType $contentType
     * @param array $data
     * @return ContentType
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareLayouts(ContentType $contentType, array $data)
    {
        // Parent aid associated to their real Id
        $groupItem = [];

        // Set layout configuration data
        $validatorCustomLayout = $this->_validatorFactory->create();

        // Layout update xml
        if ($validatorCustomLayout->isValid($data['xml'])) {
            $contentType->setData(ContentType::LAYOUT_UPDATE_XML, $data['xml']);
        } else {
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->messageManager->addErrorMessage($message);
            }
        }

        // Root template and layout
        $contentType->setData(ContentType::ROOT_TEMPLATE, $data['general']);
        $contentType->setData(ContentType::LAYOUT, $data['template']);

        /**
         * Custom layout configuration
         */

        // Retrieve layout items
        $data = (isset($data['item']) && is_array($data['item'])) ? $data['item'] : [];

        // Block, field and group items
        foreach ($this->_availableLayoutItem as $type) {
            // Retrieve items by type
            $items = (isset($data[$type]) && is_array($data[$type])) ? $data[$type] : [];

            // Items
            foreach ($items as $key => $item) {
                $itemModel = $this->_modelFactory->create('\Blackbird\ContentManager\Model\ContentType\Layout\\' . ucfirst(strtolower($type)));

                if (!empty($item['id'])) {
                    $itemModel->load($item['id']);

                    if (!empty($itemModel->getId())) {
                        if (!empty($item['is_delete'])) {
                            $itemModel->delete();
                            continue;
                        }
                    } else {
                        continue;
                    }
                }
                // If the item is not saved and has been deleted
                if (!empty($item['is_delete'])) {
                    continue;
                }

                // Set data to the item
                $itemModel->setData($item);
                $itemModel->setData(ItemModel::CT_ID, $contentType->getCtId());
                // Set id of the item if it exists
                if (!empty($item['id'])) {
                    $itemModel->setData($itemModel::ID, $item['id']);
                }

                // Manage format for item type of field
                if ($type === 'field') {
                    $itemModel->setFormat($this->getFormatSerialize($item));
                    $cfId = (!empty($item['custom_field_id'])) ? $item['custom_field_id'] : null;
                    $itemModel->setCustomFieldId($cfId);
                }

                // Set parent layout group item
                if (!empty($item['parent_id'])) {
                    $itemModel->setData($itemModel::PARENT_ID, $groupItem[$item['parent_id']]);
                }

                try {
                    $itemModel->save();

                    if ($type === 'group') {
                        $groupItem[$key] = $itemModel->getId();
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e,
                        __('Something went wrong while saving the layout item.'));
                }
            }
        }

        return $contentType;
    }

    /**
     * Retrieves the format for an item and serialize it
     *
     * @param array $item
     * @return string
     */
    protected function getFormatSerialize(array $item)
    {
        $result = serialize([
            'type' => isset($item['format']) ? $item['format'] : '',
            'extra' => isset($item['format_extra']) ? $item['format_extra'] : '',
            'height' => isset($item['format_height']) ? $item['format_height'] : '',
            'width' => isset($item['format_width']) ? $item['format_width'] : '',
            'link' => isset($item['link']) ? $item['link'] : '',
        ]);

        return $result;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blackbird_ContentManager::contenttype_save');
    }
}
