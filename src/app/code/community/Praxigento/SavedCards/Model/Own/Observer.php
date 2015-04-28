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
use Praxigento_SavedCards_Model_Own_Registry_Card as RegistryCard;

class Praxigento_SavedCards_Model_Own_Observer extends Mage_Core_Model_Observer
{
    /**
     * Flag to save credit card data to registry onSalesConvertQuoteAddressToOrder
     * and restore from onSalesModelServiceQuoteSubmitSuccess.
     */
    const REGISTRY_CREDIT_CARD = 'prxgt_autoship_credit_card';

    /** @var Praxigento_SavedCards_Config */
    private $_cfg;

    function __construct()
    {
        $this->_cfg = Praxigento_SavedCards_Config::get();
    }

    /**
     * Clear the posted credit card data when new card is entered.
     *
     * @param Varien_Event_Observer $event
     */
    public function onAdminhtmlSalesOrderCreateProcessData(Varien_Event_Observer $event)
    {
        /** @var  $createModel Mage_Adminhtml_Model_Sales_Order_Create */
        $createModel = $event->getData('order_create_model');
        $quote = $createModel->getQuote();
        $payment = $quote->getPayment();
        if ($payment->getData(Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID) == PaymentForm::VAL_NEW_CARD) {
            $payment->unsetData(Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID);
        }
    }

    public function onPaymentInfoBlockPrepareSpecificInformation(Varien_Event_Observer $event)
    {
        $transport = $event->getData('transport');
        /** @var  $payment \Mage_Payment_Model_Info */
        $payment = $event->getData('payment');
        /** Transaction ID and type */
        $lastTransId = $payment->getData(Config::ATTR_ORDER_PAYMENT_LAST_TRANS_ID);
        $lbl = (strlen($lastTransId) > 14) ? 'PayPal Transaction ID' : 'AuthNet Transaction ID';
        $lbl = $this->_cfg->helper()->__($lbl);
        $transport->setData($lbl, $lastTransId);
        /** Saved card */
        $cardId = $payment->getData(Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID);
        if (!is_null($cardId)) {
            $lbl = $this->_cfg->helper()->__('Saved Card ID');
            $transport->setData($lbl, $cardId);
            $card = Config::modelOwnRegistryCard()->load($cardId);
            $type = $this->_cfg->helper()->mapMage2Ui($card->getData(RegistryCard::ATTR_CARD_TYPE));
            $transport->setData('Credit Card Type', $type);
            $transport->setData('Credit Card Number', 'xxxx-' . $card->getData(RegistryCard::ATTR_CARD_NUM_MASK));
        }
    }

    /**
     * Save payment card attributes to registry in database.
     *
     * @param Varien_Event_Observer $event
     */
    public function onSalesConvertQuoteAddressToOrder(Varien_Event_Observer $event)
    {
        if (Mage::getSingleton('core/session')->getData(Config::SESS_SAVE_CREDIT_CARD)) {
            /** @var  $address \Mage_Sales_Model_Quote_Address */
            $address = $event->getData('address');
            /** @var  $quote \Mage_Sales_Model_Quote */
            $quote = $address->getQuote();
            $payments = $quote->getPaymentsCollection();
            foreach ($payments as $one) {
                /** @var $one \Mage_Sales_Model_Quote_Payment */
                $method = $one->getMethod();
                if (
                    ($method == \Mage_Paypal_Model_Config::METHOD_WPP_DIRECT) ||
                    ($method == Config::MODULE_PAYMENT_METHOD)
                ) {
                    $this->_registerCreditCardData(
                        $one->getData('cc_number'),
                        $one->getData('cc_cid'),
                        $one->getData('cc_exp_month'),
                        $one->getData('cc_exp_year')
                    );
                }
            }
        }
    }

    /**
     * @param $number
     * @param $cvv
     * @param $expMonth
     * @param $expYear
     */
    private function _registerCreditCardData($number, $cvv, $expMonth, $expYear)
    {
        /** @var  $card Praxigento_SavedCards_Model_Own_Service_Common_Bean_CreditCard */
        $card = Mage::getModel('prxgt_savedcards_model/own_service_common_bean_creditCard');
        $card->setCvv($cvv);
        $card->setExpMonth($expMonth);
        $card->setExpYear($expYear);
        $card->setNumber($number);
        $type = $this->_cfg->helper()->convertCardNumberToType($number);
        $card->setType($type);
        /** save card to Magento registry */
        Mage::register(self::REGISTRY_CREDIT_CARD, $card);
    }

    /**
     * Unset default value from web form for saved card ID.
     * @param Varien_Event_Observer $event
     */
    public function onSalesConvertQuotePaymentToOrderPayment(Varien_Event_Observer $event)
    {
        /** @var  $paymentOrder Mage_Sales_Model_Order_Payment */
        $paymentOrder = $event->getData('order_payment');
        if ($paymentOrder->getData(Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID) == PaymentForm::VAL_NEW_CARD) {
            $paymentOrder->unsetData(Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID);
        }
        /** @var  $paymentQuote Mage_Sales_Model_Quote_Payment */
        $paymentQuote = $event->getData('quote_payment');
        if ($paymentQuote->getData(Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID) == PaymentForm::VAL_NEW_CARD) {
            $paymentQuote->unsetData(Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID);
        }

    }

    /**
     * Quote has been paid and converted to order. Save credit card data.
     *
     * @param Varien_Event_Observer $event
     */
    public function onSalesModelServiceQuoteSubmitSuccess(Varien_Event_Observer $event)
    {
        /** @var  $order Mage_Sales_Model_Order */
        $order = $event->getData('order');
        /** @var  $wrapper Nmmlm_Core_Wrapper_Sales_Order */
        $wrapper = new Nmmlm_Core_Wrapper_Sales_Order($order);
        /** Save credit card */
        $saveCreditCard = Mage::getSingleton('core/session')->getData(Config::SESS_SAVE_CREDIT_CARD);
        if ($saveCreditCard) {
//            /** @var  $card Praxigento_SavedCards_Model_Own_Service_Common_Bean_CreditCard */
//            $card = Mage::registry(self::REGISTRY_CREDIT_CARD);
//            $customerMage = Mage::getModel('customer/customer')->load($order->getCustomerId());
//            $customer = Praxigento_Autoship_Service_Type_Customer::parseMageCustomer($customerMage);
//            $addressMage = $order->getBillingAddress();
//            $address = Praxigento_Autoship_Service_Type_Address::parseMageBillingAddress($addressMage);
//            /* Prepare service request. */
//            $req = new Praxigento_Autoship_Service_CreditCard_Request_RegisterRequest();
//            $req->setAddressBilling($address);
//            $req->setCard($card);
//            $req->setCustomer($customer);
//            $srv = new Praxigento_Autoship_Service_CreditCard_Call();
//            $resp = $srv->register($req);
//            $cardId = $resp->getRegisteredCard()->getId();
//            $order->getPayment()->setData(Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID, $cardId);
//            $wrapper->setData(Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID, $cardId);
//            $wrapper->save();
//            /** reset flag after the card was saved  */
//            $this->_log->debug("Clean 'Save credit card' flag from the session.");
//            Mage::getSingleton('core/session')->setData(Config::SESS_SAVE_CREDIT_CARD, false);
        }
    }
}