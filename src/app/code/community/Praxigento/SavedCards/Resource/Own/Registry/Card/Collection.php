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

class Praxigento_SavedCards_Resource_Own_Registry_Card_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    // TODO: remove stub
    private $_myIsLoaded = false;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(Config::CFG_MODEL . '/' . Config::CFG_ENTITY_REGISTRY_CARD);
    }

    public function load($printQuery = false, $logQuery = false)
    {
        // TODO: remove stub
        if (!$this->_myIsLoaded) {
            $item = Config::get()->modelOwnRegistryCard();
            $item->setData(RegCard::ATTR_AN_IS_SAVED, true);
            $item->setData(RegCard::ATTR_AN_PAYMENT_ID, 23);
            $item->setData(RegCard::ATTR_CARD_DATE_EXPIRED, '2015/12/31');
            $item->setData(RegCard::ATTR_CARD_HASH, 'abc');
            $item->setData(RegCard::ATTR_CARD_IS_ACTIVE, true);
            $item->setData(RegCard::ATTR_CARD_NUM_MASK, '1234');
            $item->setData(RegCard::ATTR_CARD_TYPE, Praxigento_SavedCards_Helper_Data::MAGE_MASTER);
            $item->setData(RegCard::ATTR_CUSTOMER_ID, 6);
            $item->setData(RegCard::ATTR_DATE_REGISTERED, '2015/01/01');
            $item->setData(RegCard::ATTR_ID, 1024);
            $item->setData(RegCard::ATTR_PP_ID, 100500);
            $item->setData(RegCard::ATTR_PP_IS_SAVED, true);
            $item->setData(RegCard::ATTR_PP_VALID_UNTIL, '2017/06/06');
            $this->addItem($item);
            $this->_myIsLoaded = true;
        }
        return $this;
    }

    public function getSize()
    {
        // TODO: remove stub
        $items = $this->getItems();
        $result = count($items);
        return $result;
    }


}