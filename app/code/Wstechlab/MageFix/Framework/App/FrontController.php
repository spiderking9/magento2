<?php

namespace Wstechlab\MageFix\Framework\App;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\Request\Http;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class FrontController
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var StoreResolverInterface
     */
    protected $storeResolver;

    /**
     * FrontController constructor.
     * @param StoreManagerInterface $storeManager
     * @param StoreResolverInterface $storeResolver
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StoreResolverInterface $storeResolver
    ) {

        $this->storeManager = $storeManager;
        $this->storeResolver = $storeResolver;
    }


    /**
     * Set current scope code in store resolver interface object
     *
     * @param FrontControllerInterface $frontController
     * @param Http $request
     */
    public function beforeDispatch(FrontControllerInterface $frontController, Http $request)
    {
        $scopeCode = null;
        switch ($this->storeResolver->getRunMode()) {
            case ScopeInterface::SCOPE_WEBSITE:
                $scopeCode = $this->storeManager->getWebsite()->getCode();
                break;
            case ScopeInterface::SCOPE_STORE:
                $scopeCode = $this->storeManager->getStore()->getCode();
                break;
        }
        if ($scopeCode) {
            $this->storeResolver->setScopeCode($scopeCode);
        }
    }
}