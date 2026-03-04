<?php
/**
 * Module: Checkout & Payment Logic (v1.5 PRODUCTION READY)
 * Ported from: v1.2 Core logic & Production (villapurnukka.com)
 * Function: Handles Purnukka Flex (ID 276) addition, quantity sync, and smart removal.
 */

if (!defined('ABSPATH')) exit;

/**
 * Main logic for handling the payment flow and cart synchronization.
 */
add_action('template_redirect', function() {
    if (!function_exists('WC')) return;

    // Configuration
    $target_product_id = 276; // Purnukka Flex
    $checkin_page_url = home_url('/check-in/'); // Sivusto, jossa henkilömäärä lasketaan

    // 1. ADDITION LOGIC: Tullaan matkustajailmoituksesta URL-parametreilla
    if (isset($_GET['add-to-cart']) && intval($_GET['add-to-cart']) === $target_product_id) {
        
        // Tyhjennetään kori ennen uutta yritystä (kuten tuotannossa)
        WC()->cart->empty_cart();

        // Haetaan dynaaminen hinta (määrä)
        $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;

        // Lisätään tuote oikealla summalla
        WC()->cart->add_to_cart($target_product_id, $quantity);

        // Ohjataan suoraan kassalle (Checkout)
        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }

    // 2. REMOVAL LOGIC: Jos asiakas on kassalla ja poistaa Flex-tuotteen (kori tyhjenee)
    // Tämä vastaa toivettasi: ei jätetä asiakasta tyhjälle kassalle, vaan palautetaan alkuun.
    if (is_checkout() && WC()->cart->is_empty() && !isset($_GET['order-received'])) {
        wp_safe_redirect($checkin_page_url);
        exit;
    }
});

/**
 * Varmistetaan, että kassa näyttää poistolinkin tuotannon tavoin.
 * Käytetään WooCommerce-vakiolinkkiä, joka palauttaa item keyn poistoa varten.
 */
add_filter('woocommerce_cart_item_remove_link', function($link, $cart_item_key) {
    // Tässä emme muuta linkkiä, varmistamme vain että se on olemassa ja toimii.
    return $link;
}, 10, 2);

/**
 * Estetään kappalemäärän muokkaus kassalla (Purnukka Flexille),
 * jotta asiakas ei voi muuttaa laskettua loppusummaa manuaalisesti.
 */
add_filter('woocommerce_cart_item_quantity', function($product_quantity, $cart_item_key, $cart_item) {
    if ($cart_item['product_id'] == 276) {
        return sprintf('%s', $cart_item['quantity']); // Näytetään vain numero, ei valitsinta
    }
    return $product_quantity;
}, 10, 3);