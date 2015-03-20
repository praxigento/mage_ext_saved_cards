/**
 * Copyright (c) 2015, Praxigento
 * All rights reserved.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

/**
 * JS to display/hide block with fields to enter credit card data.
 *
 * @param selectorId
 * @param wrapperId
 * @param selectorValueToDisplayWrapper
 */
function prxgt_savedcards_selector_changed(selectorId, wrapperId, selectorValueToDisplayWrapper) {
    var selector = document.getElementById(selectorId);
    var wrapper = document.getElementById(wrapperId);
    if ((selector != null) && (wrapper != null)) {
        var selectorValue = selector.options[selector.selectedIndex].value;
        if (selectorValue == selectorValueToDisplayWrapper) {
            wrapper.style.display = 'block';
        } else {
            wrapper.style.display = 'none';
        }
    }
}
/**
 * See class 'Praxigento_SavedCards_Block_Own_Payment_Method_Form'
 * - DOM_ID_SELECTOR
 * - DOM_ID_WRAPPER
 * - VAL_NEW_CARD
 */
prxgt_savedcards_selector_changed('prxgt_savedcards_prxgt_saved_card_id', 'prxgt_savedcards_cc_wrapper', 'prxgt_savedcards_new_ccard');
