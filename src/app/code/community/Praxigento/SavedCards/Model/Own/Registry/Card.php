<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
use Praxigento_SavedCards_Config as Config;

class Praxigento_SavedCards_Model_Own_Registry_Card extends Mage_Core_Model_Abstract
{
    const ATTR_AN_IS_SAVED = 'authnet_saved';
    const ATTR_AN_PAYMENT_ID = 'authnet_payment_id';
    /** format: YYYY/MM */
    const ATTR_CARD_DATE_EXPIRED = 'card_date_exp';
    const ATTR_CARD_HASH = 'card_hash';
    const ATTR_CARD_IS_ACTIVE = 'card_is_active';
    /** last 4 digits */
    const ATTR_CARD_NUM_MASK = 'card_num';
    const ATTR_CARD_TYPE = 'card_type';
    const ATTR_CUSTOMER_ID = 'customer_id';
    const ATTR_DATE_REGISTERED = 'date_registered';
    const ATTR_ID = Mage_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD;
    const ATTR_PP_ID = 'paypal_id';
    const ATTR_PP_IS_SAVED = 'paypal_saved';
    const ATTR_PP_VALID_UNTIL = 'paypal_valid_until';
    const DEF_DATE_DELIMITER = '/';

    protected function _construct()
    {
        $this->_init(Config::CFG_MODEL . '/' . Config::CFG_ENTITY_REGISTRY_CARD);
    }
}