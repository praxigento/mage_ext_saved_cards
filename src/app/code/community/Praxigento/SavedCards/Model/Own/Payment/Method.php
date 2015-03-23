<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
use Praxigento_SavedCards_Block_Own_Payment_Method_Form as PaymentForm;
use Praxigento_SavedCards_Config as Config;
use Praxigento_SavedCards_Model_Own_Registry_Card as RegCard;

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
    /**
     * Service to call to PayPal and/or Authorize.Net connectors.
     *
     * @var Praxigento_SavedCards_Model_Own_Service_Common_Call
     */
    private $_service;
    /** @var Praxigento_SavedCards_Logger */
    private $_log;
    /**
     * Cache for saved credit cards. entity_id is the key for the entry.
     * @var array
     */
    private $_cachedCards = array();

    public function __construct()
    {
        parent::__construct();
        /* don't use '_construct()' to setup block, use __construct */
        $this->_formBlockType = 'prxgt_savedcards_block/own_payment_method_form';
        $this->_log = Praxigento_SavedCards_Logger::getLogger(__CLASS__);
        $this->_service = Mage::getModel('prxgt_savedcards_model/own_service_common_call');
    }

    /**
     * This payment method is always available.
     * @param null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        //   return parent::isAvailable($quote);
        return true;
    }

    /**
     * Parse POSTed $data and place it into the Payment model.
     * @param mixed $data
     * @return Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        /** Overwrite parent assignment */
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        if ($data->getData(PaymentForm::FLD_SAVED_CARD_ID) == PaymentForm::VAL_NEW_CARD) {
            /** new card is entered */
            $ccType = Config::get()->helper()->convertCardNumberToType($data->getCcNumber());
            $info->setCcType($ccType);
            $info->setCcLast4(substr($data->getCcNumber(), -4));
            $info->setCcNumber($data->getCcNumber());
            $info->setCcCid($data->getCcCid());
            $info->setCcExpMonth($data->getCcExpMonth());
            $info->setCcExpYear($data->getCcExpYear());
            /** INTR-964 : clear saved credit card ID for info-object */
            $info->setData(Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID, null);
            /** Set or clear "Save card to registry" flag */
            if ($data->getData(PaymentForm::FLD_SAVE_FLAG)) {
                Mage::getSingleton('core/session')->setData(Config::SESS_SAVE_CREDIT_CARD, true);
            } else {
                // TODO: uncomment or cleanup the code
//                $customer = Mage::getSingleton('customer/session')->getCustomer();
//                if (
//                    ($customer->getGroupId() == Praxigento_Innutra_MapCustomer::GROUP_MAGE_ASSOC) ||
//                    Praxigento_Innutra_Util::isQuoteQualified()
//                ) {
//                    /** always save credit cards for the Associates and qualified quotes */
//                    Mage::getSingleton('core/session')->setData(Config::SESS_SAVE_CREDIT_CARD, true);
//                } else {
//                    Mage::getSingleton('core/session')->setData(Config::SESS_SAVE_CREDIT_CARD, false);
//                }
            }
        } else {
            /** saved card is used */
            $card = $this->_loadSavedCardById($data->getData(PaymentForm::FLD_SAVED_CARD_ID));
            $expDate = explode(RegCard::DEF_DATE_DELIMITER, $card->getData(RegCard::ATTR_CARD_DATE_EXPIRED));
            $info->setCcType($card->getData(RegCard::ATTR_CARD_TYPE));
            $info->setCcLast4($card->getData(RegCard::ATTR_CARD_NUM_MASK));
            $info->setCcExpMonth($expDate[1]);
            $info->setCcExpYear($expDate[0]);
            $info->setData(Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID, $card->getData(RegCard::ATTR_ID));
            /** don't save saved cards */
            Mage::getSingleton('core/session')->setData(Config::SESS_SAVE_CREDIT_CARD, false);
        }
        return $this;
    }

    /**
     * Lookup for saved credit card data by entity_id. Verify customer rights to this card.
     *
     * @param $id
     * @return Mage_Core_Model_Abstract|null
     */
    private function _loadSavedCardById($entityId, $customerId = null)
    {
        $result = null;
        /** load current customer */
        if (is_null($customerId)) {
            /* frontend mode */
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerId = $customer->getId();
            if (is_null($customerId)) {
                /* adminhtml mode  */
                $sessionQuote = Mage::getSingleton('adminhtml/session_quote');
                $customerId = $sessionQuote->getCustomerId();
            }
        }
        /** load and cache saved card by entity_id */
        if (!isset($this->_cachedCards[$entityId])) {
            $this->_cachedCards[$entityId] = Config::get()->modelOwnRegistryCard()->load($entityId);
        }
        $savedCard = $this->_cachedCards[$entityId];
        /** validate customer's access rights to the card */
        if (
            ($savedCard->getId() == $entityId) &&
            ($savedCard->getData(RegCard::ATTR_CUSTOMER_ID) == $customerId)
        ) {
            $result = $savedCard;
        } else {
            $msg = "Cannot load previously saved card '$entityId' for customer '$customerId'.";
            $this->_log->error($msg);
            Mage::throwException($msg);
        }
        return $result;
    }
}