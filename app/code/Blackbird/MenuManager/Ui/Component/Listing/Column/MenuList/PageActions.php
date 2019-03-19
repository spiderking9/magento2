<?php
/**
 * Blackbird MenuManager Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category            Blackbird
 * @package		Blackbird_MenuManager
 * @copyright           Copyright (c) 2016 Blackbird (http://black.bird.eu)
 * @author		Blackbird Team
 */
namespace Blackbird\MenuManager\Ui\Component\Listing\Column\MenuList;

use Magento\Ui\Component\Listing\Columns\Column;

class PageActions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                $id = "X";
                if (isset($item["menu_id"])) {
                    $id = $item["menu_id"];
                }
                $item[$name]["view"] = [
                    "href"  => $this->getContext()->getUrl(
                        "menumanager/menu/edit",
                        ["id" => $id]
                    ),
                    "label" => __("Edit"),
                ];
            }
        }

        return $dataSource;
    }

}
