<?php
/**
 * Module: Checkout Logic
 * Description: Manages tax rules, branding for invoices, and payment gateway overrides.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Checkout_Logic {
    public function __construct() {
        add_filter('woocommerce_email_header_image', [$this, 'set_email_logo']);
        add_filter('woocommerce_countries_tax_or_vat', [$this, 'override_vat_label']);
    }

    /**
     * Injects the brand logo from context.json into WooCommerce emails
     */
    public function set_email_logo($image) {
        $config = $GLOBALS['purnukka']->config;
        $logo = $config['design_system']['branding']['logo_url'] ?? '';
        
        return $logo ? $logo : $image;
    }

    /**
     * Overrides VAT labels based on property info
     */
    public function override_vat_label($label) {
        $config = $GLOBALS['purnukka']->config;
        $vat_number = $config['property_info']['vat_number'] ?? '';
        
        return $vat_number ? "VAT ($vat_number)" : $label;
    }
}

new Purnukka_Checkout_Logic();