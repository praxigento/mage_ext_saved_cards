<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Model_Own_Payment_Method extends \Mage_Payment_Model_Method_Cc
{
    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'prxgt_savedcards_method';
    /**
     * @var bool
     */
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout = true;
    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal = true;


    public function __construct()
    {
        /* don'n use '_construct()' to setup block */
        parent::__construct();
        $this->_formBlockType = 'prxgt_savedcards_block/own_payment_method_form';
    }

}