<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Model_Own_Service_Common_Bean_CreditCard extends \Praxigento_SavedCards_Model_Own_Base_Bean
{
    /** @var  string */
    private $_cvv;
    /** @var  string */
    private $_expMonth;
    /** @var  string */
    private $_expYear;
    /** @var  string */
    private $_nameFirst;
    /** @var  string */
    private $_nameLast;
    /** @var  string */
    private $_number;
    /** @var  integer */
    private $_savedCardId;
    /** @var bool */
    private $_savedCardUsed = false;
    /** @var  string */
    private $_type;

    /**
     * Hash code to lookup card in module's registry. It's not secure feature, it's just a hash code for data lookup.
     *
     * @param Praxigento_Autoship_Service_Type_CreditCard $card
     * @return string
     */
    public function getHash()
    {
        $in = $this->getNumber() . 'fashk' . $this->getCvv() . 'teryui' . $this->getExpYear() . 'wdljk' . $this->getExpMonth();
        $result = md5($in);
        return $result;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->_number;
    }

    /**
     * @param mixed $val
     */
    public function setNumber($val)
    {
        $this->_number = $val;
    }

    /**
     * @return mixed
     */
    public function getCvv()
    {
        return $this->_cvv;
    }

    /**
     * @param mixed $val
     */
    public function setCvv($val)
    {
        $this->_cvv = $val;
    }

    /**
     * @return mixed
     */
    public function getExpYear()
    {
        return $this->_expYear;
    }

    /**
     * @param mixed $val
     */
    public function setExpYear($val)
    {
        $this->_expYear = $val;
    }

    /**
     *
     * @return string - '01', '02', ..., '12'
     */
    public function getExpMonth()
    {
        return ($this->_expMonth < 10) ? '0' . ((int)$this->_expMonth) : $this->_expMonth;
    }

    /**
     * @param mixed $val
     */
    public function setExpMonth($val)
    {
        $this->_expMonth = $val;
    }

    /**
     * @return string
     */
    public function getNameFirst()
    {
        return $this->_nameFirst;
    }

    /**
     * @param string $val
     */
    public function setNameFirst($val)
    {
        $this->_nameFirst = $val;
    }

    /**
     * @return string
     */
    public function getNameLast()
    {
        return $this->_nameLast;
    }

    /**
     * @param string $val
     */
    public function setNameLast($val)
    {
        $this->_nameLast = $val;
    }

    /**
     * @return int
     */
    public function getSavedCardId()
    {
        return $this->_savedCardId;
    }

    /**
     * @param int $val
     */
    public function setSavedCardId($val)
    {
        $this->_savedCardId = $val;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return boolean
     */
    public function isSavedCardUsed()
    {
        return $this->_savedCardUsed;
    }

    /**
     * @param boolean $val
     */
    public function setIsSavedCardUsed($val)
    {
        $this->_savedCardUsed = $val;
    }
}