<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigationPro\Model\Config\Source;

/**
 * Class Expand
 * @package Mageplaza\LayeredNavigationPro\Model\Config\Source
 */
class Expand implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return [['value' => 1, 'label' => __('Yes')], ['value' => 0, 'label' => __('No')], ['value' => 2, 'label' => __('Use Default Config')]];
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray()
	{
		return [0 => __('No'), 1 => __('Yes'), 2 => __('Use Default Config')];
	}
}