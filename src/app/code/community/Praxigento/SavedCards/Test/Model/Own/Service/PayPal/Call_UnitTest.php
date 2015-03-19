<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
include_once('../../../../phpunit_bootstrap.php');

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Test_Model_Own_Service_PayPal_Call_UnitTest extends PHPUnit_Framework_TestCase
{

    public function test_constructor()
    {
        $call = Mage::getModel('prxgt_savedcards_model/own_service_payPal_call');
        $this->assertNotNull($call);
    }
}