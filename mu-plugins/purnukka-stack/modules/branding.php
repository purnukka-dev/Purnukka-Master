<?php
/**
 * Module: Branding & Business Identity (v1.5 Master)
 * Description: Fully automated scaling for PDF, Identity, and UI. No manual setup.
 */

if (!defined('ABSPATH')) exit;

/**
 * SCALABLE PDF INJECTION
 * This function handles the "ID vs URL" problem automatically.
 */
add_filter('option_wpo_wcpdf_settings_general', function($settings) {
    $config = $GLOBALS['purnukka']->config;
    if (empty($config['property_info'])) return $settings;

    $info = $config['property_info'];

    // 1. AUTOMATED LOGO INJECTION
    if (!empty($info['logo_url'])) {
        // Etsitään ID URL-osoitteen perusteella
        $logo_id = attachment_url_to_postid($info['logo_url']);
        
        if ($logo_id) {
            $settings['header_logo'] = $logo_id;
        } else {
            // Jos ID:tä ei löydy (esim. uusi saitti), käytetään URL-osoitetta suoraan.
            // Skaalautuvuuden takia emme voi olettaa, että ID on aina olemassa.
            $settings['header_logo'] = $info['logo_url'];
        }
    }

    // 2. IDENTITY INJECTION (Skaalautuvat kentät)
    $settings['shop_name']    = $info['company_name'] ?? '';
    $settings['shop_address'] = sprintf(
        "%s\n%s %s\n%s",
        $info['address'] ?? '',
        $info['postcode'] ?? '',
        $info['city'] ?? '',
        $info['country_code'] ?? 'FI'
    );

    // 3. EXTRA FIELDS (Business ID & Contact)
    $footer_parts = [];
    if (!empty($info['business_id'])) $footer_parts[] = 'Y-tunnus: ' . $info['business_id'];
    if (!empty($info['email']))       $footer_parts[] = $info['email'];
    if (!empty($info['phone']))       $footer_parts[] = $info['phone'];
    $settings['footer_text'] = implode(' | ', $footer_parts);

    // 4. THE NUCLEAR FIX (Skaalautuva array-pakotus)
    $settings['shop_address_city']     = ['default' => $info['city'] ?? ''];
    $settings['shop_address_postcode'] = ['default' => $info['postcode'] ?? ''];
    $settings['shop_address_country']  = ['default' => $info['country_code'] ?? 'FI'];

    return $settings;
}, 999);