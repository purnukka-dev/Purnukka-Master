<?php
/**
 * Module: Checkout & Payment Logic (v1.5 PORT)
 * Ported from: v1.2 Core logic.
 * Function: Handles the "Purnukka Flex" dynamic quantity and cart cleanup.
 * Code standards: English variables and comments.
 */

if (!defined('ABSPATH')) exit;

/**
 * Listen for the add-to-cart trigger and manage the checkout flow.
 * Specifically designed for Product ID 276 (Purnukka Flex).
 */
add_action('template_redirect', function() {
    
    // Check if WooCommerce is active and we have our specific add-to-cart trigger
    if ( !function_exists('WC') || !isset($_GET['add-to-cart']) ) {
        return;
    }

    $target_product_id = 276; // Purnukka Flex ID
    $incoming_product_id = intval($_GET['add-to-cart']);

    // Only run this logic if the specific Flex product is being added
    if ($incoming_product_id === $target_product_id) {
        
        // 1. Clear the cart to prevent accumulation of old attempts
        WC()->cart->empty_cart();

        // 2. Get the quantity (sum calculated by the check-in UI)
        // Default to 1 if not specified, though UI always sends the sum.
        $requested_quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;

        // 3. Add the product with the dynamic quantity (1€ * quantity = Total)
        WC()->cart->add_to_cart($target_product_id, $requested_quantity);

        // 4. Force redirect directly to the checkout page
        // Skipping the cart page for a seamless "Pay Now" experience.
        wp_safe_redirect( wc_get_checkout_url() );
        exit;
    }
});