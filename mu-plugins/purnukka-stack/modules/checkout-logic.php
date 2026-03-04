<?php
/**
 * Module: Checkout & Payment Logic (v1.5 CLEAN)
 * Description: High-level logic for Purnukka Flex (ID 276) flow.
 * Standards: No UI-injection in logic, clean redirects.
 */

if (!defined('ABSPATH')) exit;

/**
 * Handle core redirects for the Purnukka Flex product flow.
 */
add_action('template_redirect', function() {
    if (!function_exists('WC')) return;

    $flex_product_id = 276;
    $checkin_url = home_url('/check-in/');

    // 1. ADD-TO-CART: Clear old state and add new quantity from check-in form
    if (isset($_GET['add-to-cart']) && intval($_GET['add-to-cart']) === $flex_product_id) {
        WC()->cart->empty_cart();
        
        $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
        WC()->cart->add_to_cart($flex_product_id, $quantity);
        
        // Redirect directly to checkout
        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }

    // 2. AUTO-RECOVERY: If the cart becomes empty while on checkout, return to check-in.
    // This handles the "Start over" logic if the user removes the product.
    if (is_checkout() && WC()->cart->is_empty() && !isset($_GET['order-received'])) {
        wp_safe_redirect($checkin_url);
        exit;
    }
});

/**
 * Ensure the Flex product is identifiable but its quantity is locked on checkout.
 */
add_filter('woocommerce_cart_item_quantity', function($product_quantity, $cart_item_key, $cart_item) {
    if ($cart_item['product_id'] == 276) {
        // Return static number instead of an input field to prevent price manipulation
        return $cart_item['quantity'];
    }
    return $product_quantity;
}, 10, 3);