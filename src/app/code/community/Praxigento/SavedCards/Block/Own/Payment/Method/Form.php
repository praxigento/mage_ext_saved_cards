<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
use Praxigento_SavedCards_Config as Config;
use Praxigento_SavedCards_Model_Own_Registry_Card as RegCard;

class Praxigento_SavedCards_Block_Own_Payment_Method_Form extends Mage_Payment_Block_Form_Cc
{
    /**
     * ID for DOM element: payment method selector (radio button).
     * Value is equal to "{Config::MODULE_PAYMENT_METHOD}_{self::FLD_SAVED_CARD_ID}
     */
    const DOM_ID_SELECTOR = 'prxgt_savedcards_method_prxgt_savedcards_id';
    const DOM_ID_WRAPPER = 'prxgt_savedcards_cc_wrapper';
    const FLD_SAVED_CARD_ID = Config::CFG_FLD_PAYMENT_SAVED_CARD_ID;
    const FLD_SAVE_FLAG = 'prxgt_savedcards_flag';
    const VAL_NEW_CARD = 'prxgt_savedcards_flag_new';
    /**
     *Cards are the same for all block instances, so collection is static.
     *
     * @var Praxigento_SavedCards_Resource_Own_Registry_Card_Collection
     */
    private static $_savedCards = null;
    /** @var  Praxigento_SavedCards_Config */
    private $_cfg;

    protected function _construct()
    {
        parent::_construct();
        $this->_cfg = Praxigento_SavedCards_Config::get();
        $this->setTemplate('prxgt/savedcards/payment/form.phtml');
        $this->_loadCustomerSavedCards();
    }

    /**
     * @return Praxigento_SavedCards_Resource_Own_Registry_Card_Collection
     */
    private function _loadCustomerSavedCards()
    {
        if (is_null(self::$_savedCards)) {
            /** @var  $cards Praxigento_SavedCards_Resource_Own_Registry_Card_Collection */
            $cards = Praxigento_SavedCards_Config::get()->collectionOwnRegistryCard();
            /** @var  $customer Mage_Customer_Model_Customer */
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerId = $customer->getId();
            if ($customerId <= 0) {
                /* adminhtml mode */
                $sessionQuote = Mage::getSingleton('adminhtml/session_quote');
                $customerId = $sessionQuote->getCustomerId();
            }
            $cards->addFieldToFilter(RegCard::ATTR_CUSTOMER_ID, $customerId);
            $cards->addFieldToFilter(RegCard::ATTR_CARD_IS_ACTIVE, true);
            $cards->load();
            self::$_savedCards = $cards;
        }
    }

    public function canDisplaySaveCardCheckbox()
    {
        $result = true;
        return $result;
    }

    public function hasSavedCreditCards()
    {
        $result = !is_null(self::$_savedCards) && (self::$_savedCards->getSize() > 0);
        return $result;
    }

    public function getSavedCards()
    {
        return self::$_savedCards;
    }

    /**
     * Print out formatted card ID.
     * @param $card
     */
    public function uiCardId($card)
    {
        $result = $card->getData(RegCard::ATTR_ID);
        echo $result;
    }

    /**
     * Print out formatted card number ('******1234 (2015/12/31) - MasterCard').
     *
     * @param $card
     */
    public function uiCardValue($card)
    {
        $result = $this->_cfg->helper()->maskCreditCardNumber($card->getData(RegCard::ATTR_CARD_NUM_MASK));
        $result .= ' (' . $card->getData(RegCard::ATTR_CARD_DATE_EXPIRED) . ')';
        $result .= ' - ' . $this->_cfg->helper()->mapMage2Ui($card->getData(RegCard::ATTR_CARD_TYPE));
        echo $result;

    }
}