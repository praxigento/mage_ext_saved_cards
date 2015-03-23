<?php
/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 */

/**
 * User: Alex Gusev <alex@flancer64.com>
 */
class Praxigento_SavedCards_Config
{
    /** Session flag to save current credit card when card processing will succeed (boolean). */
    const SESS_SAVE_CREDIT_CARD = 'prxgt_savedcards_save_card_in_registry';
    /** model node name in module's etc/config.xml */
    const CFG_MODEL = 'prxgt_savedcards_model';
    /** ./etc/config.xml:/config/global/models/prxgt_savedcards_model_resource/entities */
    const CFG_ENTITY_REGISTRY_CARD = 'own_registry_card';
    /** ./etc/config.xml:/config/global/fieldsets/sales_convert_quote_payment */
    const CFG_FLD_PAYMENT_SAVED_CARD_ID = 'prxgt_saved_card_id';
    const ATTR_QUOTE_PAYMENT_SAVED_CARD_ID = self::CFG_FLD_PAYMENT_SAVED_CARD_ID;
    /**
     * Itself. Singleton.
     * We should not use static methods (bad testability).
     *
     * @var Praxigento_SavedCards_Config
     */
    private static $_instance;

    /**
     * Get singleton instance.
     *
     * @return Praxigento_SavedCards_Config
     */
    public static function  get()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Praxigento_SavedCards_Config();
        }
        return self::$_instance;
    }

    /**
     * Returns default helper for the module.
     * @return Praxigento_SavedCards_Helper_Data
     */
    public function helper()
    {
        return Mage::helper('prxgt_savedcards_helper');
    }

    /**
     * @return Praxigento_SavedCards_Resource_Own_Registry_Card_Collection
     */
    public function collectionOwnRegistryCard()
    {
        return $this->modelOwnRegistryCard()->getCollection();
    }

    /**
     * @param array $arguments
     * @return Praxigento_SavedCards_Model_Own_Registry_Card
     */
    public function modelOwnRegistryCard($arguments = array())
    {
        return Mage::getModel(self::CFG_MODEL . '/' . self::CFG_ENTITY_REGISTRY_CARD, $arguments);
    }

    /**
     * Include composer autoload class ('Composer\\Autoload\\ClassLoader') from file ./vendor/autoload.php
     * to use PayPal SDK (as composer dependency).
     */
    public function initComposerAutoload()
    {
        if (!class_exists('Composer\\Autoload\\ClassLoader', false)) {
            // Composer's vendor root folder
            $vendorRoot = 'vendor';
            // Autoloader file relative to the vendor folder.
            $autoloadFile = 'autoload.php';
            // Absolute path to magento root (.../mage/)
            $path = BP;
            // Clear cache for file_exists()
            clearstatcache();
            /* travers up to 32 levels to find ./vendor/autoload.php */
            for ($i = 0; $i < 32; $i++) {
                $pathToFile = $path . DS . $vendorRoot . DS . $autoloadFile;
                if (file_exists($pathToFile)) {
                    $varien = null;
                    $funcs = spl_autoload_functions();
                    /** un-register Varien_Autoload and re-register it after Composer autoload function. */
                    foreach ($funcs as $one) {
                        if (is_array($one) && $one[0] instanceof \Varien_Autoload) {
                            $varien = $one;
                            break;
                        }
                    }
                    /* un-register Varien_Autoload */
                    if ($varien) spl_autoload_unregister($varien);
                    /* register Composer autoload */
                    require_once($pathToFile);
                    /* register Varien_Autoload as first function in queue */
                    if ($varien) spl_autoload_register($varien, true, true);
                    break;
                } else {
                    $path = dirname($path);
                }
            }
        }
    }
}