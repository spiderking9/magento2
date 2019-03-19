<?php

namespace Wstechlab\DoctorRegister\Controller\Account;

use Wstechlab\BaseRegister\Controller\Account\CreatePostAbstract;

class CreatePost extends CreatePostAbstract
{
    /**
     * Add required data to customer
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Customer\Model\Data\Customer $customer
     * @return \Magento\Customer\Model\Data\Customer
     */
    protected function addRequiredData($request, $customer)
    {
        $customer->setCustomAttribute(
            'registration_number',
            $request->getParam("registration-number")
        );
        $customer->setCustomAttribute(
            'fiscal_code',
            $request->getParam("fiscal-code")
        );
        $customer->setCustomAttribute(
            'province_of_registration',
            $request->getParam("province-of-registration")
        );

        $doctorGroupId = $this->scopeConfig->getValue(
            'doctorRegister/general/wholesale',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $customer->setGroupId($doctorGroupId);

        return $customer;
    }

    /**
     * Dispatch register success events
     *
     * @param @param \Magento\Customer\Model\Data\Customer $customer
     */
    protected function dispatchRegisterSuccessEvents($customer)
    {
        $this->_eventManager->dispatch(
            'doctor_customer_register_success',
            ['account_controller' => $this, 'customer' => $customer]
        );
        $this->_eventManager->dispatch(
            'customer_register_success',
            ['account_controller' => $this, 'customer' => $customer]
        );
    }

    /**
     * Make data validation
     *
     * @var \Magento\Framework\App\Request\Http $request
     * @return bool
     */
    protected function validateDataFromRequest($request)
    {
        foreach ($request->getParams() as $key => $value) {
            $value = $this->escaper->escapeHtml($value);
            $request->setParam($key, $value);
        }

        $isValid = true;
        $notEmpty = new \Zend_Validate_NotEmpty();
        $isEmail = new \Zend_Validate_EmailAddress();

        $firstName = $request->getParam("firstname");
        $lastName = $request->getParam("lastname");
        $fiscalCode = $request->getParam("fiscal-code");
        $dob = $request->getParam("dob");
        $email = $request->getParam("email");
        $password = $request->getParam("password");
        $isTerms = $request->getParam("is_terms");
        $registrationNumber = $request->getParam("registration-number");
        $provinceOfRegistration = $request->getParam('province-of-registration');

        $street = $request->getParam('street');
        $city = $request->getParam('city');
        $countryId = $request->getParam('country_id');
        $postCode = $request->getParam('postcode');
        $defaultBilling = $request->getParam('default_billing', "0");
        $createAddress = $request->getParam('create_address');

        //This address always should be default billing address
        if ($defaultBilling !== "1") {
            $request->setParam('default_billing', "1");
        }

        if (!$notEmpty->isValid($createAddress)) {
            $isValid = false;
            $this->messageManager->addError(__('Internal Error, please contact with us'));
        }

        if (!$notEmpty->isValid($postCode)) {
            $isValid = false;
            $this->messageManager->addError(__('Post code cannot be empty'));
        }

        if (!$notEmpty->isValid($countryId)) {
            $isValid = false;
            $this->messageManager->addError(__('Country cannot be empty'));
        }

        if (!$notEmpty->isValid($city)) {
            $isValid = false;
            $this->messageManager->addError(__('City cannot be empty'));
        }

        if (count($street) === 0) {
            $isValid = false;
            $this->messageManager->addError(__('Street cannot be empty'));
        }

        if (!$notEmpty->isValid($firstName)) {
            $isValid = false;
            $this->messageManager->addError(__('First name cannot be empty'));
        }

        if (!$notEmpty->isValid($lastName)) {
            $isValid = false;
            $this->messageManager->addError(__('Last name cannot be empty'));
        }

        if (!$notEmpty->isValid($fiscalCode)) {
            $isValid = false;
            $this->messageManager->addError(__('Fiscal code cannot be empty'));
        }

        if (!$isEmail->isValid($email) || !$notEmpty->isValid($email)) {
            $isValid = false;
            $this->messageManager->addError(__('Invalid email'));
        }

        if (!$notEmpty->isValid($password) || strlen($password) < 8) {
            $isValid = false;
            $this->messageManager->addError(__('Invalid password'));
        }

        if (!$notEmpty->isValid($isTerms)) {
            $isValid = false;
            $this->messageManager->addError(__('You must accept terms of service'));
        }

        if (!$notEmpty->isValid($registrationNumber)) {
            $isValid = false;
            $this->messageManager->addError(__('Registration number cannot be empty'));
        }

        if (!$notEmpty->isValid($provinceOfRegistration)) {
            $isValid = false;
            $this->messageManager->addError(__('Province of registration cannot be empty'));
        }

        return $isValid;
    }

    /**
     * Get success message
     *
     * @return string
     */
    protected function getSuccessMessage()
    {
        return __('Thank you for registering');
    }
}