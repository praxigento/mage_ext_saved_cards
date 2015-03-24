<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Model_Own_Service_Common_Request_Capture
    extends Praxigento_SavedCards_Model_Own_Base_Request
{
    /** @var  \Praxigento_SavedCards_Model_Own_Service_Common_Bean_Address */
    private $_addressBilling;
    /** @var  \Praxigento_SavedCards_Model_Own_Service_Common_Bean_CreditCard */
    private $_card;
    /** @var   \Praxigento_SavedCards_Model_Own_Service_Common_Bean_Customer */
    private $_customer;
    /** @var  \Praxigento_SavedCards_Model_Own_Service_Common_Bean_SaleOrder */
    private $_order;
    /** @var  integer */
    private $_subscriptionId;

    /**
     * @return \Praxigento_SavedCards_Model_Own_Service_Common_Bean_Address
     */
    public function getAddressBilling()
    {
        return $this->_addressBilling;
    }

    /**
     * @param \Praxigento_SavedCards_Model_Own_Service_Common_Bean_Address $val
     */
    public function setAddressBilling(\Praxigento_SavedCards_Model_Own_Service_Common_Bean_Address $val)
    {
        $this->_addressBilling = $val;
    }

    /**
     * @return \Praxigento_SavedCards_Model_Own_Service_Common_Bean_CreditCard
     */
    public function getCard()
    {
        return $this->_card;
    }

    /**
     * @param \Praxigento_SavedCards_Model_Own_Service_Common_Bean_CreditCard $val
     */
    public function setCard(\Praxigento_SavedCards_Model_Own_Service_Common_Bean_CreditCard $val)
    {
        $this->_card = $val;
    }

    /**
     * @return \Praxigento_SavedCards_Model_Own_Service_Common_Bean_Customer
     */
    public function getCustomer()
    {
        return $this->_customer;
    }

    /**
     * @param \Praxigento_SavedCards_Model_Own_Service_Common_Bean_Customer $val
     */
    public function setCustomer(\Praxigento_SavedCards_Model_Own_Service_Common_Bean_Customer $val)
    {
        $this->_customer = $val;
    }

    /**
     * @return \Praxigento_SavedCards_Model_Own_Service_Common_Bean_SaleOrder
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @param \Praxigento_SavedCards_Model_Own_Service_Common_Bean_SaleOrder $order
     */
    public function setOrder(\Praxigento_SavedCards_Model_Own_Service_Common_Bean_SaleOrder $order)
    {
        $this->_order = $order;
    }

    /**
     * @return int
     */
    public function getSubscriptionId()
    {
        return $this->_subscriptionId;
    }

    /**
     * @param int $val
     */
    public function setSubscriptionId($val)
    {
        $this->_subscriptionId = $val;
    }
}