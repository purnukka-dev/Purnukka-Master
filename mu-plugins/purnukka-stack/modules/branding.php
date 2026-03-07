<?php
/**
 * Module: Branding & Business Identity (v1.6.0 MASTER)
 * Description: Fully automated scaling for PDF, Identity, and UI. Smart UI locking.
 * Refactor: Constructor Injection. Poistettu riippuvuus $GLOBALS muuttujasta.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Branding {
    private $core;

    public function __construct($core) {
        if (!$core) return;
        $this->core = $core;

        // 1. LOGIC: PDF & IDENTITY INJECTION
        add_filter('option_wpo_wcpdf_settings_general', [$this, 'sync_pdf_branding'], 999);

        // 2. UI: SMART LOCKING
        add_action('admin_footer', [$this, 'lock_manual_branding_fields']);
    }

    /**
     * Syöttää yritystiedot context.jsonista PDF-laskuihin.
     */
    public function sync_pdf_branding($settings) {
        // Käytetään injektoitua core-instanssia (Consistency Refactor)
        $config = $this->core->config;
        if (empty($config['property_info'])) return $settings;

        $info = $config['property_info'];

        // Logo sync
        if (!empty($info['logo_url'])) {
            $logo_id = attachment_url_to_postid($info['logo_url']);
            $settings['header_logo'] = $logo_id ?: $info['logo_url'];
            $settings['header_logo_height'] = '35';
        }

        // Business identity
        $settings['shop_name'] = $info['company_name'] ?? '';
        $settings['shop_address'] = sprintf("%s\n%s %s\n%s", 
            $info['address'] ?? '', $info['postcode'] ?? '', $info['city'] ?? '', $info['country_code'] ?? 'FI');

        // Footer contact info
        $footer = [];
        if (!empty($info['business_id'])) $footer[] = 'Y-tunnus: ' . $info['business_id'];
        if (!empty($info['email']))       $footer[] = $info['email'];
        if (!empty($info['phone']))       $footer[] = $info['phone'];
        $settings['footer_text'] = implode(' | ', $footer);

        // Nuclear Fix (Array override)
        $settings['shop_address_city']     = ['default' => $info['city'] ?? ''];
        $settings['shop_address_postcode'] = ['default' => $info['postcode'] ?? ''];
        $settings['shop_address_country']  = ['default' => $info['country_code'] ?? 'FI'];

        return $settings;
    }

    /**
     * Lukitsee manuaaliset kentät WooCommerce PDF Settings -sivulla.
     */
    public function lock_manual_branding_fields() {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'woocommerce_page_wpo_wcpdf_options_page') return;
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Kentät jotka ovat JSON-ohjattuja
            const controlledFields = [
                'input[name*="shop_name"]',
                'textarea[name*="shop_address"]',
                'input[name*="header_logo"]',
                'textarea[name*="footer_text"]'
            ];
            
            $(controlledFields.join(', ')).prop('readonly', true).css({
                'background-color': '#f0f0f1',
                'opacity': '0.7',
                'pointer-events': 'none'
            });

            $('.wrap h2').first().after('<div class="notice notice-info is-dismissible"><p><strong>Purnukka Stack:</strong> Branding-automaatio on PÄÄLLÄ (On). Kentät on lukittu muokkaukselta.</p></div>');
        });
        </script>
        <?php
    }
}