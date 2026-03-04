<?php
/**
 * Module: Checkout & Payment Logic (v1.5 ENHANCED)
 * Ported from: v1.2 Core logic.
 * Function: Handles "Purnukka Flex" (ID 276) and smart redirects for corrections.
 */

if (!defined('ABSPATH')) exit;

add_action('template_redirect', function() {
    if (!function_exists('WC')) return;

    $target_product_id = 276; // Purnukka Flex
    $checkin_page_url = home_url('/check-in/'); // Palautussivu

    // 1. LISÄYS: Kun tullaan matkustajailmoituksesta
    if (isset($_GET['add-to-cart']) && intval($_GET['add-to-cart']) === $target_product_id) {
        WC()->cart->empty_cart();
        $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
        WC()->cart->add_to_cart($target_product_id, $quantity);
        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }

    // 2. POISTO/TYHJENNYS: Jos asiakas on kassalla ja poistaa tuotteen (kori tyhjenee)
    // Ohjataan hänet takaisin ilmoitussivulle, jotta hän voi aloittaa alusta.
    if (is_checkout() && WC()->cart->is_empty() && !isset($_GET['order-received'])) {
        wp_safe_redirect($checkin_page_url);
        exit;
    }
});

/**
 * Varmistetaan, että Flex-tuotteella on aina näkyvä poistonappi kassalla.
 */
add_filter('woocommerce_cart_item_remove_link', function($link, $cart_item_key) {
    return $link; 
}, 10, 2);