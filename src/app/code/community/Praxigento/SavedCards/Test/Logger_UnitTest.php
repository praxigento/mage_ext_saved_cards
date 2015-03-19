<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
include_once('phpunit_bootstrap.php');

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Test_Logger_UnitTest extends PHPUnit_Framework_TestCase {

    public function test_logger() {
        $log = Praxigento_SavedCards_Logger::getLogger('test logger');
        $this->assertNotNull($log);
    }
}