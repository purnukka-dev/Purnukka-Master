<?php
/**
 * Module: Checkout Logic
 */
if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_master_checkout', function() {
    if (!function_exists('WC') || WC()->cart->is_empty()) return '';
    
    $config = $GLOBALS['purnukka']->config;
    $accent = $config['design_system']['colors']['accent'] ?? '#b89b5e';

    return "<div style='border: 2px solid $accent; padding: 20px;'>Checkout Enhancements Active</div>";
});