<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * Credit cards processing common service. This service calls to PayPal and/or Authorize.Net services (connectors)
 * to process common requests.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Model_Own_Service_Common_Call extends Praxigento_SavedCards_Model_Own_Base_Call
{
    public function capture(Praxigento_SavedCards_Model_Own_Service_Common_Request_Capture $req)
    {
        $result = Mage::getModel('prxgt_savedcards_model/own_service_common_response_capture');
        $msg = "Credit card capture for customer #{$req->getCustomer()->getIdMage()} ({$req->getCustomer()->getEmail()})";
        $this->_log->info("$msg is started.");
        $result->setTransactionId(microtime());
        $success = ($result->isSucceed()) ? 'successfully' : 'with failures';
        $this->_log->info("$msg is completed $success.");
        return $result;
    }
}