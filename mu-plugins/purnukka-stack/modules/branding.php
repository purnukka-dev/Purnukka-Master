<?php
/**
 * Module: Branding & Business Logic (v1.5 Master)
 * Standards: English code, Finnish UI, Nuclear overrides.
 */

if (!defined('ABSPATH')) exit;

/**
 * NUCLEAR PDF SYNC
 * Forces business data from context.json into WooCommerce PDF Invoices.
 * This is the ONLY reliable way to ensure white-label consistency.
 */
add_filter('option_wpo_wcpdf_settings_general', function($settings) {
    $config = $GLOBALS['purnukka']->config;
    
    // Safety check: if config is missing, don't break the invoices
    if (empty($config['property_info'])) return $settings;

    $info = $config['property_info'];

    // 1. Logo Handling
    if (!empty($info['logo_url'])) {
        $logo_id = attachment_url_to_postid($info['logo_url']);
        if ($logo_id) {
            $settings['header_logo'] = $logo_id;
            $settings['header_logo_height'] = '35';
        }
    }

    // 2. Business Identity (The Nuclear Override)
    $settings['shop_name'] = $info['company_name'] ?? '';
    $settings['shop_address_line_1'] = $info['address'] ?? '';
    
    // 3. Object Key Fix (The "Debugger" fix from v1.2)
    // We use the array('default' => ...) structure to satisfy the plugin's requirements.
    $city     = $info['city'] ?? '';
    $postcode = $info['postcode'] ?? '';
    $country  = $info['country_code'] ?? 'FI';

    $settings['shop_address_city']     = array('default' => $city);
    $settings['shop_address_postcode'] = array('default' => $postcode);
    $settings['shop_address_country']  = array('default' => $country);

    return $settings;
}, 999); // High priority to win over manual database settings