<?php
/**
 * Module: Checkout & Payment Logic (v1.5 FINAL - GUARANTEED REMOVAL)
 * Ported from: v1.2 Core logic & Production (villapurnukka.com)
 * Function: Handles Purnukka Flex (ID 276) and adds a visible removal link.
 */

if (!defined('ABSPATH')) exit;

/**
 * Handle redirections for adding and removing guest products.
 */
add_action('template_redirect', function() {
    if (!function_exists('WC')) return;

    $target_product_id = 276; // Purnukka Flex
    $checkin_page_url = home_url('/check-in/'); // Sivusto, johon palataan korjaamaan tietoja

    // 1. ADD: Kun tullaan matkustajailmoituksesta
    if (isset($_GET['add-to-cart']) && intval($_GET['add-to-cart']) === $target_product_id) {
        WC()->cart->empty_cart();
        $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
        WC()->cart->add_to_cart($target_product_id, $quantity);
        
        // Ohjataan suoraan kassalle
        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }

    // 2. REMOVE REDIRECT: Jos kori tyhjenee kassalla (poistonapin painallus)
    // Ohjataan takaisin ilmoitussivulle, jotta asiakas voi aloittaa alusta.
    if (is_checkout() && WC()->cart->is_empty() && !isset($_GET['order-received'])) {
        wp_safe_redirect($checkin_page_url);
        exit;
    }
});

/**
 * PAKOTETTU POISTOLINKKI
 * Lisätään tuotteen nimen alle selkeä linkki, koska kassan yhteenveto piilottaa ruksit.
 */
add_filter('woocommerce_cart_item_name', function($name, $cart_item, $cart_item_key) {
    if (is_checkout() && $cart_item['product_id'] == 276) {
        $remove_url = wc_get_cart_remove_url($cart_item_key);
        // Tämä linkki ilmestyy "Lisämajoittuja (Purnukka Flex)" -tekstin alle
        $name .= '<br><a href="' . esc_url($remove_url) . '" style="color:#e74c3c; font-size:11px; text-decoration:underline; font-weight:bold; display:inline-block; margin-top:5px;">[×] Poista ja korjaa henkilömäärää</a>';
    }
    return $name;
}, 10, 3);

/**
 * LUKITAAN MÄÄRÄ (Quantity)
 * Näytetään kappalemäärä, mutta estetään sen muuttaminen tekstikentällä kassalla.
 */
add_filter('woocommerce_cart_item_quantity', function($product_quantity, $cart_item_key, $cart_item) {
    if ($cart_item['product_id'] == 276) {
        return '<strong>' . $cart_item['quantity'] . '</strong>';
    }
    return $product_quantity;
}, 10, 3);