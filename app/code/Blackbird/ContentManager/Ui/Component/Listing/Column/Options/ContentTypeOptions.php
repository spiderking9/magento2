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

namespace Blackbird\ContentManager\Ui\Component\Listing\Column\Options;

use Blackbird\ContentManager\Model\Config\Source\ContentTypes;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ContentTypeOptions
 *
 * @package Blackbird\ContentManager\Ui\Component\Listing\Column\Options
 */
class ContentTypeOptions extends ContentTypes implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($default = false)
    {
        if (empty($this->options)) {
            $options = [];

            foreach ($this->getOptions() as $value => $label) {
                $options[] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }
}
