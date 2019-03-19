<?php
namespace Wstechlab\DoctorRegister\Model;

use Magento\Customer\Model\Url as CustomerUrl;

/**
 * Customer url model
 */
class Url extends CustomerUrl
{

    /**
     * Retrieve customer register form url
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->urlBuilder->getUrl('doctorregister/account/create');
    }

    /**
     * Retrieve customer register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->urlBuilder->getUrl('doctorregister/account/createpost');
    }
}
