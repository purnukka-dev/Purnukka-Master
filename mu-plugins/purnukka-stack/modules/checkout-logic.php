<?php
/**
 * Module: Purnukka Checkout Logic (v1.6.0)
 * Description: Dynaaminen hinta- ja tuotehallinta. Alkuperäinen logiikka säilytetty, kovakoodattu ID poistettu.
 * File: purnukka-checkout-logic.php
 */

if (!defined('ABSPATH')) exit;

/**
 * Ohjaukset ja ostoskoriin lisäys.
 */
add_action('template_redirect', function() {
    if (!function_exists('WC')) return;

    // Dynaaminen haku: haetaan kaikki tuotteet, jotka on linkitetty villoihin
    $args = [
        'post_type' => 'villa',
        'posts_per_page' => -1,
        'fields' => 'ids'
    ];
    $villas = get_posts($args);
    $linked_product_ids = [];
    
    foreach($villas as $vid) {
        $pid = get_post_meta($vid, '_linked_product_id', true);
        if($pid) {
            $linked_product_ids[] = intval($pid);
        }
    }

    $current_add_id = isset($_GET['add-to-cart']) ? intval($_GET['add-to-cart']) : 0;

    // 1. ADD-TO-CART FLOW (Sama logiikka kuin ennen, mutta dynaamisilla ID:illä)
    if ($current_add_id && in_array($current_add_id, $linked_product_ids)) {
        WC()->cart->empty_cart();
        $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
        WC()->cart->add_to_cart($current_add_id, $quantity);
        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }

    // 2. AUTO-RECOVERY (Alkuperäinen toiminnallisuus)
    if (is_checkout() && WC()->cart->is_empty() && !isset($_GET['order-received'])) {
        wp_safe_redirect(home_url('/check-in/'));
        exit;
    }
});

/**
 * Lukitaan määrät dynaamisille tuotteille (Alkuperäinen toiminnallisuus).
 */
add_filter('woocommerce_cart_item_quantity', function($product_quantity, $cart_item_key, $cart_item) {
    // Tarkistetaan, onko tuote linkitetty johonkin villaan
    $is_villa_product = false;
    
    // Haetaan villat ja niiden linkitetyt tuotteet
    $args = ['post_type' => 'villa', 'posts_per_page' => -1, 'fields' => 'ids'];
    $villas = get_posts($args);
    
    foreach($villas as $vid) {
        $pid = get_post_meta($vid, '_linked_product_id', true);
        if(intval($pid) === intval($cart_item['product_id'])) {
            $is_villa_product = true;
            break;
        }
    }

    if ($is_villa_product) {
        return $cart_item['quantity'];
    }
    
    return $product_quantity;
}, 10, 3);