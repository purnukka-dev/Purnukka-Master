<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.6)
 * Description: Master Control Panel for property branding, legal details, and dynamic injection.
 * Author: Purnukka Group Oy
 * Version: 0.6
 */

if ( !defined('ABSPATH') ) exit;

/**
 * CORE LOGIC CLASS
 * Handles admin branding and basic UI cleanup.
 */
class PurnukkaStackCore {

    public function __construct() {
        // Admin-side branding
        if (is_admin()) {
            add_filter('admin_footer_text', [$this, 'customize_admin_footer']);
            add_action('wp_before_admin_bar_render', [$this, 'remove_wp_logo'], 0);
        }
    }

    public function customize_admin_footer() {
        $brand_name = get_option('p_villa_name', 'Villa Purnukka');
        return sprintf(
            '<strong>%s</strong> | <span style="color: #666;">Purnukka Stack Master Control Enabled</span>',
            esc_html($brand_name)
        );
    }

    public function remove_wp_logo() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }
}

/**
 * PHASE 4: MASTER CONTROL PANEL UI
 */

add_action('admin_menu', function() {
    add_menu_page(
        'Purnukka Settings',
        'Purnukka Stack',
        'manage_options',
        'purnukka-settings',
        'render_purnukka_settings_page',
        'dashicons-admin-generic',
        2
    );
});

function render_purnukka_settings_page() {
    ?>
    <div class="wrap">
        <h1 style="color: #c5a059; font-weight: bold;">Purnukka Stack – Property Configuration</h1>
        <p>Define global variables for branding, contact info, and legal requirements.</p>
        <hr>
        
        <form method="post" action="options.php">
            <?php 
                settings_fields('purnukka-settings-group'); 
            ?>
            
            <h2>1. Property Identity & Branding</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Master Villa Name</th>
                    <td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name', 'Villa Purnukka')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Master Villa Tagline</th>
                    <td><input type="text" name="p_villa_tagline" value="<?php echo esc_attr(get_option('p_villa_tagline')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Primary Brand Color</th>
                    <td><input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Logo Image URL</th>
                    <td>
                        <input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text" />
                        <p class="description">Paste the URL of your transparent PNG logo here.</p>
                    </td>
                </tr>
            </table>

            <h2>2. Contact & Communication</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Customer Service Email</th>
                    <td><input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Contact Phone</th>
                    <td><input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2>3. URL & SEO Slugs</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Villa Slug (e.g., villapurnukka)</th>
                    <td><input type="text" name="p_villa_slug" value="<?php echo esc_attr(get_option('p_villa_slug', 'villapurnukka')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Location Slug (e.g., kuopio)</th>
                    <td><input type="text" name="p_location_slug" value="<?php echo esc_attr(get_option('p_location_slug')); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2>4. Legal & Company Details</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Legal Company Name</th>
                    <td><input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Business ID (Y-tunnus)</th>
                    <td><input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Legal Address</th>
                    <td><input type="text" name="p_company_address" value="<?php echo esc_attr(get_option('p_company_address')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Accommodation VAT Rate (%)</th>
                    <td><input type="number" name="p_vat_rate" value="<?php echo esc_attr(get_option('p_vat_rate', '10')); ?>" class="small-text" /> %</td>
                </tr>
            </table>

            <h2>5. Maps & Location</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Latitude / Longitude</th>
                    <td>
                        <input type="text" name="p_latitude" placeholder="62.1234" value="<?php echo esc_attr(get_option('p_latitude')); ?>" class="small-text" />
                        <input type="text" name="p_longitude" placeholder="27.1234" value="<?php echo esc_attr(get_option('p_longitude')); ?>" class="small-text" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Full Map Address</th>
                    <td><input type="text" name="p_map_address" value="<?php echo esc_attr(get_option('p_map_address')); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <?php submit_button('Update Purnukka Stack'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $purnukka_settings = [
        'p_villa_name', 'p_villa_tagline', 'purnukka_primary_color', 'purnukka_logo_url',
        'p_villa_email', 'p_villa_phone', 'p_villa_slug', 'p_location_slug',
        'p_company_name', 'p_business_id', 'p_company_address', 'p_vat_rate',
        'p_latitude', 'p_longitude', 'p_map_address'
    ];
    foreach ($purnukka_settings as $setting) {
        register_setting('purnukka-settings-group', $setting);
    }
});

/**
 * PHASE 2: DYNAMIC BRANDING INJECTION
 */
add_action('wp_head', function() {
    if ( is_admin() ) return;
    $primary_color = get_option('purnukka_primary_color', '#c5a059');
    ?>
    <style id="purnukka-dynamic-branding">
        :root { --purnukka-primary: <?php echo esc_attr($primary_color); ?>; }
        .button, button, .mphb-book-button, .mphb-view-details-button, .cmplz-btn.cmplz-accept {
            background-color: var(--purnukka-primary) !important;
            border-color: var(--purnukka-primary) !important;
        }
        a, .site-title a { color: var(--purnukka-primary); }
    </style>
    <?php
}, 20);

/**
 * PHASE 4.1: DYNAMIC LOGO REPLACEMENT
 */
add_filter('get_custom_logo', function($html) {
    $master_logo = get_option('purnukka_logo_url');
    if ( !empty($master_logo) ) {
        $html = sprintf(
            '<a href="%1$s" class="custom-logo-link" rel="home"><img src="%2$s" class="custom-logo" alt="Logo" style="max-height: 80px; width: auto; display: block;"></a>',
            esc_url( home_url( '/' ) ),
            esc_url( $master_logo )
        );
    }
    return $html;
});

/**
 * PHASE 5: GLOBAL BRANDING REPLACER
 */
add_filter('the_content', 'purnukka_master_text_replacer');
add_filter('the_title', 'purnukka_master_text_replacer');

function purnukka_master_text_replacer($text) {
    if ( is_admin() ) return $text;
    $new_name = get_option('p_villa_name', 'Villa Purnukka');
    if ( $new_name !== 'Villa Purnukka' ) {
        $text = str_replace('Villa Purnukka', $new_name, $text);
    }
    return $text;
}

// Initialize the Core
new PurnukkaStackCore();