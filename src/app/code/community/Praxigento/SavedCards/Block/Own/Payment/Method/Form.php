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
    /**
     * ID for DOM element: payment method selector (radio button).
     */
    const DOM_ID_SELECTOR = 'prxgt_savedcards_method_prxgt_saved_card_id';
    const DOM_ID_WRAPPER = 'prxgt_savedcards_cc_wrapper';
    const FLD_SAVED_CARD_ID = 'prxgt_saved_card_id';
    const FLD_SAVE_FLAG = 'prxgt_saved_card_flag';
    const VAL_NEW_CARD = 'prxgt_saved_card_flag_new';

    /** @var Praxigento_SavedCards_Mysql4_Own_Registry_Card_Collection */
    private $_savedCards = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('prxgt/savedcards/payment/form.phtml');
//        $this->_savedCards = $this->loadCustomerSavedCards();
        $this->_savedCards = array('q', 'w', 'e', 'r');
    }

    public function canDisplaySaveCardCheckbox()
    {
        $result = true;
        return $result;
    }

    public function hasSavedCreditCards()
    {
        $result = true;
        return $result;
    }

    public function getSavedCards()
    {
        return $this->_savedCards;
    }

    public function uiCardId($card)
    {
        $result = "ID";
        echo $result;
    }

    public function uiCardValue($card)
    {
        $result = "card number";
        echo $result;
    }
}