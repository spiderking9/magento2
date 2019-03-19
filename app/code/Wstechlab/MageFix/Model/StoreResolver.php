<?php

namespace Wstechlab\MageFix\Model;

 use Magento\Store\Model\StoreResolver as MagentoStoreResolver;

 class StoreResolver extends MagentoStoreResolver
 {
     /**
      * @return string
      */
     public function getRunMode()
     {
         return $this->runMode;
     }

     /**
      * @param $scopeCode
      * @return $this
      */
     public function setScopeCode($scopeCode)
     {
         $this->scopeCode = $scopeCode;
         return $this;
     }
 }