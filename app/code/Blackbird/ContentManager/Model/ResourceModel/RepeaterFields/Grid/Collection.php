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
 * @package                Blackbird_ContentManager
 * @copyright           Copyright (c) 2018 Blackbird (http://black.bird.eu)
 * @author                Blackbird Team
 * @license                http://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Model\ResourceModel\RepeaterFields\Grid;

use Blackbird\ContentManager\Model\Config\Source\ContentType\Visibility;
use Blackbird\ContentManager\Model\ContentType;
use Blackbird\ContentManager\Model\ResourceModel\ContentType\Grid\AbstractCollection;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Class Collection
 */
class Collection extends AbstractCollection implements SearchResultInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldToFilter(ContentType::VISIBILITY, Visibility::REPEATER_FIELD);

        return $this;
    }
}
