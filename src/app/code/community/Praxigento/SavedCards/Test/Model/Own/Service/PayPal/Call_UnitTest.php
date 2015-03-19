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

    public function test_sdk()
    {
        /** @var  $loader Composer\Autoloader\ClassLoader */
        $loader = require_once('/home/alex/work/github/ad.local/vendor/autoload.php');
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'Ab5vHQHVgenIcBeBg3ckBiGvR7EdCFimtBLTGAeD6UZ8r0F7Ly3DonUQObiKRqkgfKOlLRvwizGqo3fI',     // ClientID
                'EECmhNqRpGzPMH_sAtoyvyIX1dGxEtWJVj2aWgmVhCNMgbKucQD2_OeURD2pxQphIrm5LQ7MWix4lN5s'      // ClientSecret
            )
        );
        $creditCard = new \PayPal\Api\CreditCard();
        $creditCard->setType("visa")
            ->setNumber("4417119669820331")
            ->setExpireMonth("11")
            ->setExpireYear("2019")
            ->setCvv2("012")
            ->setFirstName("Joe")
            ->setLastName("Shopper");
        try {
            $creditCard->create($apiContext);
            echo $creditCard;
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex;
        }
    }
}