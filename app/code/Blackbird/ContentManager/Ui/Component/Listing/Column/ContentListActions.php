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

namespace Blackbird\ContentManager\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class PageActions
 *
 * Class ContentListActions
 *
 * @package Blackbird\ContentManager\Ui\Component\Listing\Column
 */
class ContentListActions extends Column
{
    /** Url path */
    const URL_PATH_EDIT = 'contentmanager/contentlist/edit';
    const URL_PATH_DELETE = 'contentmanager/contentlist/delete';

    /** @var \Magento\Backend\Model\Url */
    protected $urlBuilder;

    /** @var \Magento\Framework\Url */
    protected $frontendUrlBuilder;

    /**
     * ContentListActions constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Backend\Model\Url $urlBuilder
     * @param \Magento\Framework\Url $frontendUrlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Backend\Model\Url $urlBuilder,
        \Magento\Framework\Url $frontendUrlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->frontendUrlBuilder = $frontendUrlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['ct_id'])) {
                    $item[$this->getName()]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_EDIT, ['id' => $item['cl_id']]),
                        'label' => __('Edit'),
                    ];
                    $item[$this->getName()]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_DELETE, ['id' => $item['cl_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.title }'),
                            'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?'),
                        ],
                    ];
                }
                if (isset($item['url_key'])) {
                    $store = isset($item['_first_store_code']) ? $item['_first_store_code'] : null;

                    $item[$this->getName()]['preview'] = [
                        'href' => $this->frontendUrlBuilder->getUrl($item['url_key'], [
                            '_current' => false,
                            '_query' => ['preview' => 1, StoreResolverInterface::PARAM_NAME => $store],
                        ]),
                        'label' => __('Preview'),
                    ];
                }
            }
        }

        return $dataSource;
    }
}
