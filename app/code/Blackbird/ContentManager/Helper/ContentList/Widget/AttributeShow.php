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

namespace Blackbird\ContentManager\Helper\ContentList\Widget;

use Blackbird\ContentManager\Model\ContentType\CustomField;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\CustomField\CollectionFactory as CustomFieldCollectionFactory;

/**
 * Attribute to Show Helper for the ContentList widget
 *
 * Class AttributeShow
 *
 * @package Blackbird\ContentManager\Helper\ContentList\Widget
 */
class AttributeShow
{
    /**
     * @var CustomFieldCollectionFactory
     */
    protected $_customFieldCollectionFactory;

    /**
     * @var array
     */
    protected $_customFields = [];

    /**
     * @param CustomFieldCollectionFactory $customFieldCollectionFactory
     */
    public function __construct(CustomFieldCollectionFactory $customFieldCollectionFactory)
    {
        $this->_customFieldCollectionFactory = $customFieldCollectionFactory;
    }

    /**
     * Decode the attribute to show data from the widget into an assoc array:
     * (format used in order to render the element in the frontend view)
     * [
     *      attribute => $identifier,
     *      params => [
     *          custom_field => $customField,
     *          params => [
     *              label => $label,
     *              html_label_tag => $htmlLabelTag,
     *              html_tag => $htmlTag,
     *              html_id => $htmlId,
     *              html_class => $htmlClass,
     *              ...
     *          ],
     *      ],
     * ]
     *
     * @param string $data
     * @return array
     * @throws \Exception
     */
    public function decode($data)
    {
        if (!is_string($data)) {
            throw new \Exception('Decode requires a string as parameter !');
        }

        // Prepare vars
        $customFieldsArray = [];
        $attributeShow = $this->decodeJson($data);
        $customFieldIds = $this->getCustomFieldIds($attributeShow);

        // Load CustomFields
        $this->loadCustomFields($customFieldIds);

        foreach ($attributeShow as $attribute) {
            $customField = $this->getCustomField($attribute['custom_field_id']);
            $identifier = 'title';

            if ($customField !== null) {
                $identifier = $customField->getIdentifier();
            }

            $customFieldsArray[] = [
                'attribute' => $identifier,
                'params' => $attribute,
            ];
        }

        return $customFieldsArray;
    }

    /**
     * Json decode
     *
     * @param string $data
     * @return array
     */
    protected function decodeJson($data)
    {
        $attributes = [];
        $data = str_replace('\\"', '\'', str_replace('`', '"', $data));
        $data = json_decode(str_replace('as_parameters[attributes_show]', '', $data));

        foreach ($data as $c => $value) {
            $keys = explode('[', str_replace(']', '', $value->name));
            if (count($keys) < 3) {
                continue;
            }

            list(, $key, $type) = $keys;

            if (!isset($attributes[$key])) {
                $attributes[$key] = [];
            }

            $attributes[$key][$type] = $value->value;
        }

        return $attributes;
    }

    /**
     * Retrieves the custom field ids
     *
     * @param array $data
     * @return array
     */
    protected function getCustomFieldIds(array $data)
    {
        $ids = [];

        foreach ($data as $values) {
            if (isset($values['custom_field_id'])) {
                $ids[] = $values['custom_field_id'];
            }
        }

        return $ids;
    }

    /**
     * Get the custom fields collection for the given ids
     *
     * @param array $ids
     * @return $this
     */
    protected function loadCustomFields(array $ids)
    {
        if (!empty($ids)) {
            $customFieldsArray = [];
            $customFields = $this->_customFieldCollectionFactory->create();
            $customFields->removeAllFieldsFromSelect()
                ->addFieldToSelect(CustomField::IDENTIFIER)
                ->addFieldToFilter(CustomField::ID, $ids);

            foreach ($customFields as $customField) {
                $customFieldsArray[$customField->getId()] = $customField;
            }

            $this->_customFields = $customFieldsArray;
        }

        return $this;
    }

    /**
     * Retrieve the given custom field if it exists
     *
     * @param int $customFieldId
     * @return CustomField
     */
    protected function getCustomField($customFieldId)
    {
        $result = null;

        if (isset($this->_customFields[$customFieldId])) {
            $result = $this->_customFields[$customFieldId];
        }

        return $result;
    }
}
