<?php
/**
 * Module: Checkout & Payment Logic (v1.5 PRODUCTION REFINED)
 * Function: Handles Purnukka Flex (ID 276). 
 * Added: Smart removal logic not present in current production for better UX.
 */

if (!defined('ABSPATH')) exit;

add_action('template_redirect', function() {
    if (!function_exists('WC')) return;

    $target_product_id = 276; 
    $checkin_page_url = home_url('/check-in/'); 

    // 1. ADD: Tullaan check-in sivulta
    if (isset($_GET['add-to-cart']) && intval($_GET['add-to-cart']) === $target_product_id) {
        WC()->cart->empty_cart();
        $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
        WC()->cart->add_to_cart($target_product_id, $quantity);
        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }

    // 2. REMOVE: Jos kori tyhjenee kassalla, lennätä takaisin täyttämään ilmoitusta
    if (is_checkout() && WC()->cart->is_empty() && !isset($_GET['order-received'])) {
        wp_safe_redirect($checkin_page_url);
        exit;
    }
});

/**
 * PAKOTETAAN POISTOLINKKI NÄKYVIIN (Tätä ei tuotannossa ole, mutta lisätään nyt)
 */
add_filter('woocommerce_cart_item_remove_link', function($link, $cart_item_key) {
    return $link; // Varmistaa, että rasti/linkki ilmestyy myös Flex-tuotteelle
}, 10, 2);

/**
 * LUKITAAN MÄÄRÄ (Quantity)
 */
add_filter('woocommerce_cart_item_quantity', function($product_quantity, $cart_item_key, $cart_item) {
    if ($cart_item['product_id'] == 276) {
        return $cart_item['quantity']; // Ei muokkauslaatikkoa, vain luku
    }
    return $product_quantity;
}, 10, 3);