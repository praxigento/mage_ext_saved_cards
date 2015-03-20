<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Block_Own_Payment_Method_Form extends Mage_Payment_Block_Form_Cc
{
    const DOM_ID_SELECTOR   = 'prxgt_autoship_prxgt_saved_card_id';
    const DOM_ID_WRAPPER    = 'prxgt_autoship_cc_wrapper';
    const FLD_SAVED_CARD_ID = 'prxgt_saved_card_id';
    const FLD_SAVE_FLAG     = 'prxgt_save_card';
    const VAL_NEW_CARD      = 'prxgt_autoship_new_ccard';

    /** @var Praxigento_SavedCards_Mysql4_Own_Registry_Card_Collection */
    private $_savedCards = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('prxgt/savedcards/payment/form.phtml');
//        $this->_savedCards = $this->loadCustomerSavedCards();
    }

}