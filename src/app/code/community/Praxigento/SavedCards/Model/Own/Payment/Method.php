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
    /**
     * @var Praxigento_SavedCards_Config
     */
    private $_cfg;

    public function __construct()
    {
        parent::__construct();
        $this->_cfg = Praxigento_SavedCards_Config::get();
        $this->_log = Praxigento_SavedCards_Logger::getLogger(__CLASS__);
        /* don't use '_construct()' to setup block, use __construct */
        $this->_formBlockType = 'prxgt_savedcards_block/own_payment_method_form';
        $this->_service = Mage::getModel('prxgt_savedcards_model/own_service_common_call');

    }

    /**
     * This payment method is always available.
     * @param null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable($quote);
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

    public function getConfigPaymentAction()
    {
        $result = parent::getConfigData('payment_action');
        if (empty($result)) {
            $result = \Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE;
        }
        return $result;
    }

    public function validate()
    {
        $result = false;
        /** @var  $info \Mage_Payment_Model_Info */
        $info = $this->getInfoInstance();
        $savedCardId = $info->getData(Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID);
        if (
            is_null($savedCardId) ||
            ($savedCardId == \Praxigento_SavedCards_Block_Own_Payment_Method_Form::VAL_NEW_CARD)
        ) {
            /** This is newly typed card */
            $result = parent::validate();
        } else {
            /** This is saved card probably. Load saved card data. */
            $customerId = null;
            if (!is_null($info->getOrder()) && !is_null($info->getOrder()->getCustomerId())) {
                $customerId = $info->getOrder()->getCustomerId();
            }
            $savedCard = $this->_loadSavedCardById($savedCardId, $customerId);
            if (!is_null($savedCard)) {
                $result = true;
            } else {
                $this->_log->error("Cannot find saved credit card. Saved card id = '$savedCardId'.");
                Mage::throwException('Cannot find saved credit card.');
            }
        }
        return $result;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        /** @var $payment \Mage_Sales_Model_Order_Payment */
        if ($this->canCapture()) {
            /* Prepare data for request. */
            $addressMage = $payment->getOrder()->getBillingAddress();
            $address = $this->_cfg->helper()->convertAddressMageToCommonService($addressMage);
            $card = $this->_initCard($payment);
            $customerMage = $payment->getOrder()->getCustomer();
            $customer = $this->_cfg->helper()->convertCustomerMageToCommonService($customerMage);
            $order = $this->_initOrder($payment, $amount);
            /* prepare service request */
            /** @var  $req Praxigento_SavedCards_Model_Own_Service_Common_Request_Capture */
            $req = Mage::getModel('prxgt_savedcards_model/own_service_common_request_capture');
            $req->setAddressBilling($address);
            $req->setCard($card);
            $req->setCustomer($customer);
            $req->setOrder($order);
            /* call operation */
            /** @var  $resp Praxigento_SavedCards_Model_Own_Service_Common_Response_Capture */
            $resp = $this->_service->capture($req);
            if ($resp->isSucceed()) {
                $transId = $resp->getTransactionId();
                $this->_addTransaction(
                    $payment,
                    $transId,
                    Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE,
                    array(),
                    $resp->getAdditionalInfo()
                );
                $this->_log->info("Credit card capture is done. Transaction ID: $transId.");
            } else {
                $err = '';
                foreach ($resp->getErrorMsg() as $one) $err .= "\n$one";
                $this->_log->fatal('Credit card capture is failed.' . $err);
                Mage::throwException(Mage::helper('payment')->__('Credit card capture is failed.') . $err);
            }
        } else {
            $this->_log->warn("Cannot perform capture action for payment (unattainable code).");
        }
        return $this;
    }

    private function _initCard(\Mage_Sales_Model_Order_Payment $payment)
    {
        $result = Mage::getModel('prxgt_savedcards_model/own_service_common_bean_creditCard');
        $address = $payment->getOrder()->getBillingAddress();
        $savedCardId = $this->getInfoInstance()->getData(Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID);
        if (!is_null($savedCardId)) {
            $result->setIsSavedCardUsed(true);
            $result->setSavedCardId($savedCardId);
        } else {
            /** newly typed card data */
            $result->setCvv($payment->getCcCid());
            $result->setExpMonth($payment->getCcExpMonth());
            $result->setExpYear($payment->getCcExpYear());
            $result->setNumber($payment->getCcNumber());
            $result->setNameFirst($address->getData(Config::ATTR_ADDR_NAME_FIRST));
            $result->setNameLast($address->getData(Config::ATTR_ADDR_NAME_LAST));
            $ccType = $this->_cfg->helper()->convertCardNumberToType($payment->getCcNumber());
            $result->setType($ccType);
        }
        return $result;
    }

    private function _initOrder(\Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $result = Mage::getModel('prxgt_savedcards_model/own_service_common_bean_saleOrder');
        $order = $payment->getOrder();
        /**
         * Retrieve values for base amounts
         */
        $shipping = $this->_cfg->helper()->formatAmount($order->getBaseShippingAmount());

        /** customer credit amount is discount too */
        $credit = $order->getBaseCustomerCreditAmount();
        /** this attribute is not universal - not for every order */
        $subtotalWithDiscount = (is_null($order->getBaseSubtotalWithDiscount())) ?
            $order->getBaseSubtotal() : $order->getBaseSubtotalWithDiscount();
        /** is discount always negative value? we will use positive discount below */
        $discount = $order->getBaseDiscountAmount();
        $discountNormalized = $discount;
        if ($discount < 0) {
            $discountNormalized *= -1;
        }
        $subtotal = $this->_cfg->helper()->formatAmount($subtotalWithDiscount - $credit - $discountNormalized);
        /* INTR-1250 */
        $taxTotal = $order->getBaseTaxAmount() + $order->getBaseHiddenTaxAmount();
        $tax = $this->_cfg->helper()->formatAmount($taxTotal);
        $total = $this->_cfg->helper()->formatAmount($amount);
        /** INTR-1003 */
        if (($subtotal < 0 || ($shipping < 0) || ($tax < 0))) {
            /* TODO should we use partial payment in public module ??? */
            /** reset values in case of partial payment by credit is used */
            $shipping = 0;
            $tax = 0;
            $subtotal = $total;
            $this->_log->info("Payment amounts for order '{$order->getIncrementId()}' are normalized: subtotal: $subtotal; shipping: $shipping; tax: $tax; total: $total");
        }
        //
        $result->setAmountShipping($shipping);
        $result->setAmountSubtotal($subtotal);
        $result->setAmountTax($tax);
        $result->setAmountTotal($amount);
        $result->setCurrency($order->getBaseCurrencyCode());
        $result->setIdIncremental($order->getIncrementId());
        return $result;
    }

    private function _addTransaction(
        Mage_Sales_Model_Order_Payment $payment,
        $transactionId,
        $transactionType,
        array $transactionDetails = array(),
        array $transactionAdditionalInfo = array(),
        $message = false
    )
    {
        $payment->setTransactionId($transactionId);
        $payment->resetTransactionAdditionalInfo();
        foreach ($transactionDetails as $key => $value) {
            $payment->setData($key, $value);
        }
        foreach ($transactionAdditionalInfo as $key => $value) {
            $payment->setTransactionAdditionalInfo($key, $value);
        }
        $transaction = $payment->addTransaction($transactionType, null, false, $message);
        foreach ($transactionDetails as $key => $value) {
            $payment->unsetData($key);
        }
        $payment->unsLastTransId();

        /**
         * It for self using
         */
        $transaction->setMessage($message);

        return $transaction;
    }
}