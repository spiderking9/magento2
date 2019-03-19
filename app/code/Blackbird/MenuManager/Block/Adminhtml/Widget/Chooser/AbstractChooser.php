<?php
namespace Blackbird\MenuManager\Block\Adminhtml\Widget\Chooser;

abstract class AbstractChooser extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_selectedEntities = [];


    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        $js = '';

        if ($this->getUseMassaction()) {
            $js = 'function (grid, element) {
                $(grid.containerId).fire("content:changed", {element: element});
            }';

            if ($form = $this->getJsFormObject()) {
                $js = "{$form}.chooserGridCheckboxCheck.bind({$form})";
            }
        }

        return $js;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $js = '';

        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            $js = '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var contentId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                    var contentTitle = trElement.down("td").next().next().innerHTML;
                    ' .
                $chooserJsObject .
                '.setElementValue(contentId);
                    ' .
                $chooserJsObject .
                '.setElementLabel(contentTitle);
                    ' .
                $chooserJsObject .
                '.close();
                }
            ';
        } else {
            if ($form = $this->getJsFormObject()) {
                $js = "{$form}.chooserGridRowClick.bind({$form})";
            }
        }

        return $js;
    }


    /**
     * Setter
     *
     * @param array $selectedEntities
     * @return $this
     */
    public function setSelectedEntities(array $selectedEntities)
    {
        $this->_selectedEntities = $selectedEntities;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedEntities()
    {
        if ($selectedEntities = $this->getRequest()->getParam('selected', [])) {
            $this->setSelectedEntities($selectedEntities);
        }

        return $this->_selectedEntities;
    }

    /**
     * Convet an array to options
     *
     * @param array $array
     * @return array
     */
    protected function toOptions(array $array)
    {
        $options = [];

        foreach ($array as $line) {
            $options[$line['value']] = $line['label'];
        }

        return $options;
    }
}