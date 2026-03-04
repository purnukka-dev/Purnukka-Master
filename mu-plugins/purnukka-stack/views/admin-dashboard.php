<?php
/**
 * Module: Admin Dashboard (v1.5 Master)
 * Description: Technical overview of the current stack state and context data.
 * Standards: English code, Finnish UI for the dashboard content.
 */

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function() {
    add_menu_page(
        'Purnukka Stack',
        'Purnukka Stack',
        'manage_options',
        'purnukka-stack',
        'purnukka_stack_dashboard_view',
        'dashicons-layout',
        2
    );
});

function purnukka_stack_dashboard_view() {
    $config = $GLOBALS['purnukka']->config;
    $info   = $config['property_info'] ?? [];
    $tier   = $config['product']['tier'] ?? 'Solo';
    $limits = $config['limits'] ?? [];

    // Technical check for Logo ID vs URL
    $logo_url = $info['logo_url'] ?? '';
    $logo_id  = attachment_url_to_postid($logo_url);
    $logo_status = $logo_id ? "ID: $logo_id (Kunnossa)" : "Käytetään suoraa URL-osoitetta (Fallback)";

    ?>
    <div class="wrap">
        <h1>Purnukka Stack <span style="font-size: 14px; color: #666;">v1.5 Master</span></h1>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
            
            <div class="card" style="max-width: 100%;">
                <h2>Brändäys & PDF-tiedot</h2>
                <table class="widefat striped">
                    <tr><td><strong>Kohde:</strong></td><td><?php echo esc_html($info['name'] ?? '-'); ?></td></tr>
                    <tr><td><strong>Yritys:</strong></td><td><?php echo esc_html($info['company_name'] ?? '-'); ?></td></tr>
                    <tr><td><strong>Y-tunnus:</strong></td><td><?php echo esc_html($info['business_id'] ?? '-'); ?></td></tr>
                    <tr><td><strong>Osoite:</strong></td><td><?php echo esc_html($info['address'] ?? ''); ?>, <?php echo esc_html($info['postcode'] ?? ''); ?> <?php echo esc_html($info['city'] ?? ''); ?></td></tr>
                    <tr><td><strong>Email:</strong></td><td><?php echo esc_html($info['email'] ?? '-'); ?></td></tr>
                    <tr><td><strong>Puhelin:</strong></td><td><?php echo esc_html($info['phone'] ?? '-'); ?></td></tr>
                    <tr><td><strong>Logo:</strong></td><td><small><?php echo esc_html($logo_status); ?></small></td></tr>
                </table>
            </div>

            <div class="card" style="max-width: 100%;">
                <h2>Tilaustaso & Rajoitukset</h2>
                <p>Aktiivinen taso: <strong><?php echo esc_html($tier); ?></strong></p>
                <table class="widefat striped">
                    <tr><td><strong>Max majoitusyksiköt:</strong></td><td><?php echo esc_html($limits['max_locations'] ?? '1'); ?></td></tr>
                    <tr><td><strong>Max vieraat (AI):</strong></td><td><?php echo esc_html($limits['max_guests'] ?? '-'); ?></td></tr>
                </table>

                <h2 style="margin-top:20px;">AI Connector</h2>
                <table class="widefat striped">
                    <tr><td><strong>Rooli:</strong></td><td><?php echo esc_html($config['ai_config']['role'] ?? '-'); ?></td></tr>
                    <tr><td><strong>Sävy:</strong></td><td><?php echo esc_html($config['ai_config']['tone'] ?? '-'); ?></td></tr>
                </table>
            </div>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4;">
            <h3>Synkronointi</h3>
            <p>Tiedot ladataan tiedostosta: <code>/wp-content/purnukka-config/context.json</code></p>
            <button class="button button-secondary" disabled>Päivitä Ohjaamosta (Tuleva ominaisuus)</button>
        </div>
    </div>
    <?php
}