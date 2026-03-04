<?php
/**
 * Module: Branding & Business Identity (v1.5 Master)
 * Description: Nuclear sync for PDF, Identity, and UI. Ported from v1.2 Core.
 */

if (!defined('ABSPATH')) exit;

/**
 * 1. NUCLEAR PDF SYNC
 * Injects business info and logo directly into the PDF engine.
 */
add_filter('option_wpo_wcpdf_settings_general', function($settings) {
    $config = $GLOBALS['purnukka']->config;
    if (empty($config['property_info'])) return $settings;

    $info = $config['property_info'];

    // LOGO-FIKSI: PDF Invoices haluaa ID:n. 
    // Jos URL on tuotu UI:n kautta, attachment_url_to_postid löytää sen.
    if (!empty($info['logo_url'])) {
        $logo_id = attachment_url_to_postid($info['logo_url']);
        
        if ($logo_id) {
            $settings['header_logo'] = $logo_id; // Ideaalitilanne (ID löytyi)
        } else {
            // Jos ID:tä ei löydy (esim. ulkoinen URL), pakotetaan URL.
            // Jotkut PDF-templatet osaavat lukea tämän, jos ID on tyhjä.
            $settings['header_logo'] = $info['logo_url'];
        }
        $settings['header_logo_height'] = '35';
    }

    // YRITYSTIEDOT (Kaikki 10+ kenttää)
    $settings['shop_name']    = $info['company_name'] ?? '';
    $settings['shop_address'] = sprintf(
        "%s\n%s %s\n%s",
        $info['address'] ?? '',
        $info['postcode'] ?? '',
        $info['city'] ?? '',
        $info['country_code'] ?? 'FI'
    );

    // ALATUNNISTE (Footer) - Email, Puhelin ja Y-tunnus
    $footer_parts = [];
    if (!empty($info['business_id'])) $footer_parts[] = 'Y-tunnus: ' . $info['business_id'];
    if (!empty($info['email']))       $footer_parts[] = $info['email'];
    if (!empty($info['phone']))       $footer_parts[] = $info['phone'];
    
    $settings['footer_text'] = implode(' | ', $footer_parts);

    // DEBUGGER-KORJAUS (Array-pakotus kaupungille ja maalle)
    $settings['shop_address_city']     = ['default' => $info['city'] ?? ''];
    $settings['shop_address_postcode'] = ['default' => $info['postcode'] ?? ''];
    $settings['shop_address_country']  = ['default' => $info['country_code'] ?? 'FI'];

    return $settings;
}, 999);

/**
 * 2. WHITE LABEL ENGINE
 * Global text replacement for "Villa Purnukka" -> Master Name
 */
add_filter('the_content', 'purnukka_master_branding_replacer');
add_filter('the_title', 'purnukka_master_branding_replacer');

function purnukka_master_branding_replacer($text) {
    if (is_admin() || empty($text)) return $text;
    $name = $GLOBALS['purnukka']->config['property_info']['name'] ?? 'Villa Purnukka';
    return str_replace(['Villa Purnukka', 'Purnukka Group'], $name, $text);
}