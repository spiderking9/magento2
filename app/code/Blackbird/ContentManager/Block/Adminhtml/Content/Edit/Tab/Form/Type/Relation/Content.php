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

namespace Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\Relation;

use Blackbird\ContentManager\Model\ContentType\CustomField;

/**
 * Class Content
 *
 * @package Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\Relation
 */
class Content extends \Blackbird\ContentManager\Block\Adminhtml\Content\Edit\Tab\Form\Type\AbstractType
{
    /**
     * Default template for content field
     *
     * @var string
     */
    protected $_template = 'Blackbird_ContentManager::content/edit/tab/form/type/relation/content.phtml';

    /**
     * Get field type code
     *
     * @return string
     */
    public function getFieldType()
    {
        return 'content';
    }

    /**
     * Url for the ajax chooser grid
     *
     * @return string
     */
    public function getUrlSource($form = false)
    {
        if ($form) {
            return $this->getUrl('contentmanager/content_widget/chooser',
                [
                    'ct_identifier' => $this->getCustomField()->getData(CustomField::CT_IDENTIFIER)
                ]
            );
        }

        return $this->getUrl(
            'contentmanager/content_widget/chooser',
            [
                'ct_identifier' => $this->getCustomField()->getData(CustomField::CT_IDENTIFIER),
                'form' => $this->getElement()->getHtmlId()
            ]
        );
    }
}
