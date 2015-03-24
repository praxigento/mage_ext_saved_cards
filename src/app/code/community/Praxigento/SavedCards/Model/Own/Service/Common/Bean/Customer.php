<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Model_Own_Service_Common_Bean_Customer extends \Praxigento_SavedCards_Model_Own_Base_Bean
{
    /**
     * Authorize.Net reference for customer profile.
     *
     * @var  integer
     */
    private $_anReference;
    /** @var  string */
    private $_email;
    /** @var  integer */
    private $_idMage;
    /** @var  string */
    private $_nameFirst;
    /** @var  string */
    private $_nameLast;

    /**
     * Parse Magento entity to convert to service entity.
     *
     * @param Mage_Customer_Model_Customer $data
     * @return Praxigento_Autoship_Service_Type_Customer
     */
    public static function parseMageCustomer(\Mage_Customer_Model_Customer $data)
    {
        $result = new Praxigento_Autoship_Service_Type_Customer();
        $result->setAnReference($data->getData(Praxigento_Autoship_Config::ATTR_CUST_AN_CUSTOMER_ID));
        $result->setEmail($data->getEmail());
        $result->setIdMage($data->getId());
        $result->setNameFirst($data->getData(Praxigento_Autoship_Config::ATTR_CUST_NAME_FIRST));
        $result->setNameLast($data->getData(Praxigento_Autoship_Config::ATTR_CUST_NAME_LAST));
        return $result;
    }

    /**
     * @return int
     */
    public function getAnReference()
    {
        return $this->_anReference;
    }

    /**
     * @param int $anReference
     */
    public function setAnReference($anReference)
    {
        $this->_anReference = $anReference;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * @return int
     */
    public function getIdMage()
    {
        return $this->_idMage;
    }

    /**
     * @param int $val
     */
    public function setIdMage($val)
    {
        $this->_idMage = $val;
    }

    /**
     * @return string
     */
    public function getNameFirst()
    {
        return $this->_nameFirst;
    }

    /**
     * @param string $nameFirst
     */
    public function setNameFirst($nameFirst)
    {
        $this->_nameFirst = $nameFirst;
    }

    /**
     * @return string
     */
    public function getNameFull()
    {
        return $this->_nameFirst . ' ' . $this->_nameLast;
    }

    /**
     * @return string
     */
    public function getNameLast()
    {
        return $this->_nameLast;
    }

    /**
     * @param string $nameLast
     */
    public function setNameLast($nameLast)
    {
        $this->_nameLast = $nameLast;
    }

    /**
     * @param string $nameFull
     */
    public function setNameFull($nameFull)
    {
        $this->_nameFull = $nameFull;
    }
}