<?php
namespace Wstechlab\SocialLogin\Controller\Social;


/**
 * Class Login 
 *
 */
class Login extends \Mageplaza\SocialLogin\Controller\Social\Login
{


    /**
     * Return redirect url by config
     *
     * @return mixed
     */
    protected function _loginPostRedirect()
    {
        $url = $this->urlBuilder->getUrl('customer/account');

        return $url;
    }
}