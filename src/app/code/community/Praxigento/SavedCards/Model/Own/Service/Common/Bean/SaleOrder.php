<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Model_Own_Service_Common_Bean_SaleOrder extends \Praxigento_SavedCards_Model_Own_Base_Bean
{
    private $_amountShipping;
    private $_amountSubtotal;
    private $_amountTax;
    private $_amountTotal;
    private $_currency;
    private $_idEntity;
    private $_idIncremental;

    /**
     * @return mixed
     */
    public function getAmountShipping()
    {
        return $this->_amountShipping;
    }

    /**
     * @param mixed $val
     */
    public function setAmountShipping($val)
    {
        $this->_amountShipping = $val;
    }

    /**
     * @return mixed
     */
    public function getAmountSubtotal()
    {
        return $this->_amountSubtotal;
    }

    /**
     * @param mixed $val
     */
    public function setAmountSubtotal($val)
    {
        $this->_amountSubtotal = $val;
    }

    /**
     * @return mixed
     */
    public function getAmountTax()
    {
        return $this->_amountTax;
    }

    /**
     * @param mixed $val
     */
    public function setAmountTax($val)
    {
        $this->_amountTax = $val;
    }

    /**
     * @return mixed
     */
    public function getAmountTotal()
    {
        return $this->_amountTotal;
    }

    /**
     * @param mixed $val
     */
    public function setAmountTotal($val)
    {
        $this->_amountTotal = $val;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * @param mixed $val
     */
    public function setCurrency($val)
    {
        $this->_currency = $val;
    }

    /**
     * @return mixed
     */
    public function getIdEntity()
    {
        return $this->_idEntity;
    }

    /**
     * @param mixed $val
     */
    public function setIdEntity($val)
    {
        $this->_idEntity = $val;
    }

    /**
     * @return mixed
     */
    public function getIdIncremental()
    {
        return $this->_idIncremental;
    }

    /**
     * @param mixed $val
     */
    public function setIdIncremental($val)
    {
        $this->_idIncremental = $val;
    }
}