<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * Base class for all service calls requests.
 * All request classes has setters only, getters are useless for requests.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
abstract class Praxigento_SavedCards_Model_Own_Base_Request extends stdClass
{
    /* Authentication parameters are set up in \Praxigento_Ips_Model_Own_Base_Call::__construct()*/
    public $MerchantGUID;
    public $MerchantPassword;

    protected function _formatDate(DateTime $date)
    {
        return $date->format('Y-m-d');
    }

    /**
     * Convert itself to JSON string.
     * @return string
     */
    public function jsonEncode()
    {
        $result = json_encode($this);
        return $result;
    }
}