<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * Simple representation of the address data in the common service.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Model_Own_Service_Common_Bean_Address extends \Praxigento_SavedCards_Model_Own_Base_Bean
{
    private $_address;
    private $_city;
    private $_company;
    private $_country;
    private $_nameFirst;
    private $_nameLast;
    private $_phone;
    private $_state;
    private $_zip;


    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->_address = $address;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->_city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->_city = $city;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->_company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->_company = $company;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->_country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->_country = $country;
    }

    /**
     * @return mixed
     */
    public function getNameFirst()
    {
        return $this->_nameFirst;
    }

    /**
     * @param mixed $nameFirst
     */
    public function setNameFirst($nameFirst)
    {
        $this->_nameFirst = $nameFirst;
    }

    /**
     * @return mixed
     */
    public function getNameLast()
    {
        return $this->_nameLast;
    }

    /**
     * @param mixed $nameLast
     */
    public function setNameLast($nameLast)
    {
        $this->_nameLast = $nameLast;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->_state = $state;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->_zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->_zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->_phone = $phone;
    }
}