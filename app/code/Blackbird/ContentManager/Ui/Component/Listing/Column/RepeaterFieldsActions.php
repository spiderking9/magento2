<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_ContentManager
 * @copyright   Copyright (c) 2018 Blackbird (http://black.bird.eu)
 * @author      Blackbird Team
 * @license     http://www.advancedcontentmanager.com/license/
 */

namespace Blackbird\ContentManager\Ui\Component\Listing\Column;

/**
 * Class PageActions
 */
class RepeaterFieldsActions extends ContentTypeActions
{
    /** Url path */
    const URL_PATH_EDIT = 'contentmanager/repeaterfields/edit';
    const URL_PATH_DELETE = 'contentmanager/repeaterfields/delete';


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
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_EDIT, ['id' => $item['ct_id']]),
                        'label' => __('Edit'),
                    ];
                    $item[$this->getName()]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::URL_PATH_DELETE, ['id' => $item['ct_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.title }'),
                            'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?'),
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
