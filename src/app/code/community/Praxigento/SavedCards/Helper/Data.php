<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
use Praxigento_SavedCards_Config as Config;

class Praxigento_SavedCards_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MAGE_AMERICAN_EXPRESS = 'AE';
    const MAGE_DISCOVERY = 'DI';
    const MAGE_JCB = 'JCB';
    const MAGE_MAESTRO = 'SM';
    const MAGE_MASTER = 'MC';
    const MAGE_OTHER = 'OT';
    const MAGE_SOLO = 'SO';
    const MAGE_VISA = 'VI';

    const PP_AMEX = 'amex';
    const PP_DISCOVER = 'discover';
    const PP_MASTERCARD = 'mastercard';
    const PP_VISA = 'visa';

    /**
     * @param $mageCode
     * @return null|string
     */
    public function mapMage2PayPal($mageCode)
    {
        $result = null;
        switch ($mageCode) {
            case self::MAGE_AMERICAN_EXPRESS:
                $result = self::PP_AMEX;
                break;
            case self::MAGE_DISCOVERY:
                $result = self::PP_DISCOVER;
                break;
            case self::MAGE_MASTER:
                $result = self::PP_MASTERCARD;
                break;
            case self::MAGE_VISA:
                $result = self::PP_VISA;
                break;
        }
        return $result;
    }

    /**
     * Map Magento internal code for the card type into UI label.
     * @param $mageCode
     * @return null|string
     */
    public function mapMage2Ui($mageCode)
    {
        $result = null;
        switch ($mageCode) {
            case self::MAGE_AMERICAN_EXPRESS:
                $result = 'American Express';
                break;
            case self::MAGE_DISCOVERY:
                $result = 'Discovery';
                break;
            case self::MAGE_MASTER:
                $result = 'MasterCard';
                break;
            case self::MAGE_VISA:
                $result = 'VISA';
                break;
        }
        return $result;
    }

    /**
     * See code in \Mage_Payment_Model_Method_Cc::validate().
     * @param $ccardNum
     * @return int|string
     */
    public function convertCardNumberToType($ccardNum)
    {
        $result = self::MAGE_OTHER;
        $ccTypeRegExpList = array(
            //Solo, Switch or Maestro. International safe
            /*
            // Maestro / Solo
            'SS'  => '/^((6759[0-9]{12})|(6334|6767[0-9]{12})|(6334|6767[0-9]{14,15})'
                       . '|(5018|5020|5038|6304|6759|6761|6763[0-9]{12,19})|(49[013][1356][0-9]{12})'
                       . '|(633[34][0-9]{12})|(633110[0-9]{10})|(564182[0-9]{10}))([0-9]{2,3})?$/',
            */
            // Solo only
            self::MAGE_SOLO => '/(^(6334)[5-9](\d{11}$|\d{13,14}$))|(^(6767)(\d{12}$|\d{14,15}$))/',
            self::MAGE_MAESTRO => '/(^(5[0678])\d{11,18}$)|(^(6[^05])\d{11,18}$)|(^(601)[^1]\d{9,16}$)|(^(6011)\d{9,11}$)'
                . '|(^(6011)\d{13,16}$)|(^(65)\d{11,13}$)|(^(65)\d{15,18}$)'
                . '|(^(49030)[2-9](\d{10}$|\d{12,13}$))|(^(49033)[5-9](\d{10}$|\d{12,13}$))'
                . '|(^(49110)[1-2](\d{10}$|\d{12,13}$))|(^(49117)[4-9](\d{10}$|\d{12,13}$))'
                . '|(^(49118)[0-2](\d{10}$|\d{12,13}$))|(^(4936)(\d{12}$|\d{14,15}$))/',
            // Visa
            self::MAGE_VISA => '/^4[0-9]{12}([0-9]{3})?$/',
            // Master Card
            self::MAGE_MASTER => '/^5[1-5][0-9]{14}$/',
            // American Express
            self::MAGE_AMERICAN_EXPRESS => '/^3[47][0-9]{13}$/',
            // Discovery
            self::MAGE_DISCOVERY => '/^6011[0-9]{12}$/',
            // JCB
            self::MAGE_JCB => '/^(3[0-9]{15}|(2131|1800)[0-9]{11})$/'
        );

        foreach ($ccTypeRegExpList as $ccTypeMatch => $ccTypeRegExp) {
            if (preg_match($ccTypeRegExp, $ccardNum)) {
                $result = $ccTypeMatch;
                break;
            }
        }
        return $result;
    }

    /**
     * Convert Magento address (billing or shipping) to Common Service address bean.
     * @param Mage_Customer_Model_Address_Abstract $data
     * @return Praxigento_SavedCards_Model_Own_Service_Common_Bean_Address
     */
    public function convertAddressMageToCommonService(\Mage_Customer_Model_Address_Abstract $data)
    {
        $result = Mage::getModel('prxgt_savedcards_model/own_service_common_bean_address');
        $result->setAddress($data->getStreetFull());
        $result->setCity($data->getData(Config::ATTR_ADDR_CITY));
        $result->setCompany($data->getData(Config::ATTR_ADDR_COMPANY));
        $result->setCountry($data->getData(Config::ATTR_ADDR_COUNTRY_ID));
        $result->setNameFirst($data->getData(Config::ATTR_ADDR_NAME_FIRST));
        $result->setNameLast($data->getData(Config::ATTR_ADDR_NAME_LAST));
        $result->setPhone($data->getData(Config::ATTR_ADDR_PHONE));
        $stateId = $data->getData(Config::ATTR_ADDR_STATE_ID);
        $region = Mage::getModel('directory/region')->load($stateId);
        $result->setState($region->getCode());
        $result->setZip($data->getData(Config::ATTR_ADDR_ZIP));
        return $result;
    }

    /**
     * Convert Magento Customer to Common Service customer bean.
     * @param Mage_Customer_Model_Customer $data
     * @return Praxigento_SavedCards_Model_Own_Service_Common_Bean_Customer
     */
    public function convertCustomerMageToCommonService(\Mage_Customer_Model_Customer $data)
    {
        $result = Mage::getModel('prxgt_savedcards_model/own_service_common_bean_customer');
        $result->setAnReference($data->getData(Config::ATTR_CUST_AN_CUSTOMER_ID));
        $result->setEmail($data->getEmail());
        $result->setIdMage($data->getId());
        $result->setNameFirst($data->getData(Config::ATTR_CUST_NAME_FIRST));
        $result->setNameLast($data->getData(Config::ATTR_CUST_NAME_LAST));
        return $result;
    }

    /**
     * Module internal formatting.
     *
     * @param $val
     * @return string
     */
    public static function  formatAmount($val)
    {
        $result = number_format($val, 2, '.', '');
        return $result;
    }

    /**
     * PayPal compatible formatting.
     * Currency amount must be non-negative number, may optionally contain exactly 2 decimal places separated by '.',
     * optional thousands separator ',', limited to 7 digits before the decimal point
     *
     * @param $val
     * @return string
     */
    public static function  formatAmountPayPal($val)
    {
        $result = number_format($val, 2, '.', ',');
        return $result;
    }

    /**
     * Create masked number for credit card ('*******1234').
     * @param $num string The last4 digits of the credit card number
     * @return string
     */
    public function maskCreditCardNumber($num)
    {
        return '********' . $num;
    }
}