<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
use Praxigento_SavedCards_Config as Config;
use Praxigento_SavedCards_Model_Own_Registry_Card as RegCard;

class Praxigento_SavedCards_Resource_Own_Registry_Card
    extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(Config::CFG_MODEL . '/' . Config::CFG_ENTITY_REGISTRY_CARD, RegCard::ATTR_ID);
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        $result = Mage::getModel('prxgt_savedcards_model/own_registry_card');
        //return parent::load($object, $value, $field); // TODO: Change the autogenerated stub
        return $result;
    }

}