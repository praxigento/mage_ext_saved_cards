<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */
use Praxigento_SavedCards_Config as Config;
use Praxigento_SavedCards_Helper_Data as Helper;
use Praxigento_SavedCards_Model_Own_Registry_Card as RegistryCard;

/**
 * User: Alex Gusev <alex@flancer64.com>
 */

/** include function to replace old columns with new ones */
include_once('prxgt_install_func.php');

const SQL_TYPE_VARCHAR = 'VARCHAR(255) NULL';
const SQL_TYPE_BOOL = 'TINYINT( 1 ) NOT NULL';

/** @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();

/** @var $coreSetup Mage_Eav_Model_Entity_Setup */
$coreSetup = new Mage_Eav_Model_Entity_Setup('core_setup');
/** @var $conn Varien_Db_Adapter_Interface */
$conn = $this->getConnection();

/**
 * Table names.
 */
$tblCustomerEntity = $this->getTable('customer/entity');
$tblOrderPayment = $this->getTable('sales/order_payment');
$tblQuotePayment = $this->getTable('sales/quote_payment');
$tblRegistryCard = $this->getTable(Config::CFG_MODEL . '/' . Config::CFG_ENTITY_REGISTRY_CARD);


/** *******************************************************************************************************************
 * Create table columns for static attributes in the existing Mage tables.
 ******************************************************************************************************************* */

/** customer/entity */
prxgt_install_recreate_column($conn, $tblCustomerEntity,
    Config::ATTR_CUST_AN_CUSTOMER_ID,
    "varchar(255) NULL COMMENT 'Authorize.Net ID used in CIM API'");

/** sales/order_payment */
prxgt_install_recreate_column($conn, $tblOrderPayment,
    Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID,
    " int(10) UNSIGNED NULL COMMENT 'ID of the saved card (if the saved card has been used for the payment))'");

/** sales/quote_payment */
prxgt_install_recreate_column($conn, $tblQuotePayment,
    Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID,
    " int(10) UNSIGNED NULL COMMENT 'ID of the saved card (if the saved card has been used for the payment))'");


/** *******************************************************************************************************************
 * Create new EAV attributes for existing Mage entities.
 ******************************************************************************************************************* */

/** Customer */
$coreSetup->removeAttribute(Config::TYPE_ENTITY_CUSTOMER, Config::ATTR_CUST_AN_CUSTOMER_ID);
$coreSetup->addAttribute(Config::TYPE_ENTITY_CUSTOMER, Config::ATTR_CUST_AN_CUSTOMER_ID,
    array(
        'comparable' => false,
        'filterable' => false,
        'frontend_label' => "Authorize.Net ID for CIM API",
        'input' => 'text',
        'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'label' => "Authorize.Net ID for CIM API",
        'required' => false,
        'searchable' => false,
        'type' => 'static',
    )
);

/** Order Payment */
$coreSetup->removeAttribute(Config::TYPE_ENTITY_ORDER_PAYMENT, Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID);
$coreSetup->addAttribute(Config::TYPE_ENTITY_ORDER_PAYMENT, Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID,
    array(
        'comparable' => false,
        'filterable' => false,
        'frontend_label' => "Saved card ID",
        'input' => 'text',
        'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'label' => "Saved card ID",
        'required' => false,
        'searchable' => false,
        'type' => 'static',
    )
);

/** Quote Payment */
$coreSetup->removeAttribute(Config::TYPE_ENTITY_QUOTE_PAYMENT, Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID);
$coreSetup->addAttribute(Config::TYPE_ENTITY_QUOTE_PAYMENT, Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID,
    array(
        'comparable' => false,
        'filterable' => false,
        'frontend_label' => "Saved card ID",
        'input' => 'text',
        'is_global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'label' => "Saved card ID",
        'required' => false,
        'searchable' => false,
        'type' => 'static',
    )
);


/** *******************************************************************************************************************
 * Create new tables for new resources.
 ******************************************************************************************************************* */

/* Saved Cards Registry  */
$sql = "
DROP TABLE IF EXISTS $tblRegistryCard;
CREATE TABLE $tblRegistryCard (
  " . RegistryCard::ATTR_ID . "  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Magento entity id (is used in ORM)',
  " . RegistryCard::ATTR_CUSTOMER_ID . " int(10) UNSIGNED NOT NULL COMMENT 'Customer ID',
  " . RegistryCard::ATTR_DATE_REGISTERED . " datetime NOT NULL  COMMENT 'Card\'s entry registration date',
  " . RegistryCard::ATTR_CARD_HASH . " varchar(255) NOT NULL COMMENT 'MD5 hash for card attributes',
  " . RegistryCard::ATTR_CARD_IS_ACTIVE . " tinyint(1) DEFAULT NULL COMMENT 'null - card is inactive',
  " . RegistryCard::ATTR_CARD_TYPE . " enum(
        '" . Helper::MAGE_AMERICAN_EXPRESS . "',
        '" . Helper::MAGE_DISCOVERY . "',
        '" . Helper::MAGE_JCB . "',
        '" . Helper::MAGE_MAESTRO . "',
        '" . Helper::MAGE_MASTER . "',
        '" . Helper::MAGE_OTHER . "',
        '" . Helper::MAGE_SOLO . "',
        '" . Helper::MAGE_VISA . "'
  ) DEFAULT '" . Helper::MAGE_OTHER . "' COMMENT 'type of the saved credit card',
  " . RegistryCard::ATTR_CARD_NUM_MASK . " char(4) NOT NULL COMMENT 'Last 4 digitas of the credit card used in subscription',
  " . RegistryCard::ATTR_CARD_DATE_EXPIRED . " char(7) NOT NULL COMMENT 'Credit card expiration date, YYYY/MM',
  " . RegistryCard::ATTR_AN_IS_SAVED . " tinyint(1) DEFAULT NULL COMMENT 'null - card is not saved in Authorize.Net',
  " . RegistryCard::ATTR_AN_PAYMENT_ID . " varchar(255) NULL COMMENT 'Authorize.Net ID for stored payment profile with card data',
  " . RegistryCard::ATTR_PP_IS_SAVED . " tinyint(1) DEFAULT NULL COMMENT 'null - card is not saved in PayPal',
  " . RegistryCard::ATTR_PP_ID . " varchar(255) NULL COMMENT 'PayPal ID for stored card data',
  " . RegistryCard::ATTR_PP_VALID_UNTIL . " datetime DEFAULT NULL COMMENT 'PayPal data expiration date',
  PRIMARY KEY (" . RegistryCard::ATTR_ID . "),
  UNIQUE INDEX UK_prxgt_savedcards_registry ("
    . RegistryCard::ATTR_CUSTOMER_ID . ", "
    . RegistryCard::ATTR_CARD_HASH . ", "
    . RegistryCard::ATTR_CARD_IS_ACTIVE . "),
  CONSTRAINT FK_prxgt_savedcards_registry_customer_entity_entity_id FOREIGN KEY (" . RegistryCard::ATTR_CUSTOMER_ID . ")
  REFERENCES $tblCustomerEntity (" . Config::ATTR_CUST_ID . ") ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE = INNODB
COMMENT = 'Registry for credit cards saved externally(PayPal and/or Authorize.Net).';
";
$this->run($sql);


/** *******************************************************************************************************************
 * Re-create indexes on Magento table.
 ******************************************************************************************************************* */

/** Add foreign key from Order Payment entities to the saved cards registry. */
$fkName = "FK_{$tblOrderPayment}_{$tblRegistryCard}";
$conn->dropForeignKey($tblOrderPayment, $fkName);
$conn->addForeignKey(
    $fkName,
    $tblOrderPayment,
    Config::ATTR_ORDER_PAYMENT_SAVED_CARD_ID,
    $tblRegistryCard,
    RegistryCard::ATTR_ID,
    $conn::FK_ACTION_RESTRICT,
    $conn::FK_ACTION_RESTRICT
);

/** Add foreign key from Quote Payment entities to the saved cards registry. */
$fkName = "FK_{$tblQuotePayment}_{$tblRegistryCard}";
$conn->dropForeignKey($tblQuotePayment, $fkName);
$conn->addForeignKey(
    $fkName,
    $tblQuotePayment,
    Config::ATTR_QUOTE_PAYMENT_SAVED_CARD_ID,
    $tblRegistryCard,
    RegistryCard::ATTR_ID,
    $conn::FK_ACTION_RESTRICT,
    $conn::FK_ACTION_RESTRICT
);


/**
 * Post setup Mage routines.
 */
$this->endSetup();