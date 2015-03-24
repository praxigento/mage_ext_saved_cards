<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Model_Own_Service_Common_Response_Capture
    extends Praxigento_SavedCards_Model_Own_Base_Response
{
    const RESULT_AN_CAPTURE_FAILED = 'AN_CAPTURE_FAILED';
    const RESULT_NEW_CARD_IS_NOT_REGISTERED = 'NEW_CARD_IS_NOT_REGISTERED';
    /** @var array */
    private $_additionalInfo = array();
    /** @var  string */
    private $_transactionId;

    /**
     * @return array
     */
    public function getAdditionalInfo()
    {
        return $this->_additionalInfo;
    }

    /**
     * @param array $val
     */
    public function setAdditionalInfo($val)
    {
        $this->_additionalInfo = $val;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->_transactionId;
    }

    /**
     * @param string $val
     */
    public function setTransactionId($val)
    {
        $this->_transactionId = $val;
    }

    public function isSucceed()
    {
        // TODO: Implement isSucceed() method.
        $result = true;
        return $result;
    }
}