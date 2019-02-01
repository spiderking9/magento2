<?php
 
namespace Wstechlab\Theme\Block;

class Link extends \Magento\Framework\View\Element\Html\Link
{
/**
* Render block HTML.
*
* @return string
*/
protected function _toHtml()
    {
     if (false != $this->getTemplate()) {
        return parent::_toHtml();
     }
     $html = '<li class="test '.$this->getLiclass().'"><a ' . $this->getLinkAttributes() . ' >';
     if($this->getIcon()){
         $html .= "<span class='material-icons'>".$this->getIcon()."</span>"; 
     }
     $html .= $this->escapeHtml($this->getLabel()) . '</a></li>';
     return  $html;
    }
}