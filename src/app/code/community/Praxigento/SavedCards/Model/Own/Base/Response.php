<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * Base class for all services calls responses.
 * All response classes has getters only, setters are useless for responses.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
abstract class Praxigento_SavedCards_Model_Own_Base_Response
{
    public abstract function isSucceed();
}