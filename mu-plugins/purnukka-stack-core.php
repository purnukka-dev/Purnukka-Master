<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.12)
 * Description: SPoT Master Control: Branding, SMTP, Legal, and PDF Invoice Sync.
 * Author: Purnukka Group Oy
 * Version: 0.12
 */

if ( !defined('ABSPATH') ) exit;

/**
 * CORE LOGIC CLASS
 */
class PurnukkaStackCore {

    public function __construct() {
        if (is_admin()) {
            add_filter('admin_footer_text', [$this, 'customize_admin_footer']);
            add_action('wp_before_admin_bar_render', [$this, 'remove_wp_logo'], 0);
            add_action('admin_head', [$this, 'admin_declutter_css']);
            add_action('admin_menu', [$this, 'remove_all_plugin_notices'], 999);
        }
        $this->register_shortcodes();
    }

    public function customize_admin_footer() {
        $brand_name = get_option('p_villa_name', 'Villa Purnukka');
        return sprintf('<strong>%s</strong> | Purnukka Stack Master Control Enabled', esc_html($brand_name));
    }

    public function remove_wp_logo() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }

    public function admin_declutter_css() {
        echo '<style>
            .toplevel_page_purnukka-settings .notice, .toplevel_page_purnukka-settings .update-nag { display: block !important; }
            .notice-info, .notice-warning:not(.error), .update-nag, .wp-mail-smtp-review-notice, .mphb-notice, #footer-upgrade, .wp-mail-smtp-upgrade-bar, .mphb-upgrade-notice { display: none !important; }
            #wp-admin-bar-wp-logo { display: none !important; }
        </style>';
    }

    public function remove_all_plugin_notices() {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }

    private function register_shortcodes() {
        add_shortcode('p_villa_name', function() { return get_option('p_villa_name'); });
        add_shortcode('p_company_name', function() { return get_option('p_company_name'); });
        add_shortcode('p_business_id', function() { return get_option('p_business_id'); });
        add_shortcode('p_address', function() { 
            return get_option('p_legal_address') . ', ' . get_option('p_legal_postcode') . ' ' . get_option('p_legal_city'); 
        });
    }
}

/**
 * MASTER CONTROL PANEL UI
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
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        purnukka_sync_master_data();
    }
    ?>
    <div class="wrap">
        <h1 style="color: #c5a059; font-weight: bold;">Purnukka Stack – Configuration</h1>
        <p>The <strong>Single Point of Truth</strong> for this property.</p>
        <hr>
        <form method="post" action="options.php" id="purnukka-settings-form">
            <?php settings_fields('purnukka-settings-group'); ?>
            
            <h2>1. Property Identity & Branding</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Master Villa Name</th>
                    <td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name', 'Villa Purnukka')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Colors (P / S / A / D)</th>
                    <td>
                        <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>" />
                        <input type="color" name="purnukka_secondary_color" value="<?php echo esc_attr(get_option('purnukka_secondary_color', '#a3844a')); ?>" />
                        <input type="color" name="purnukka_accent_color" value="<?php echo esc_attr(get_option('purnukka_accent_color', '#f1c40f')); ?>" />
                        <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Logo Image URL</th>
                    <td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2>2. Communication & SMTP</h2>
            <table class="form-table">
                <tr valign="top"><th scope="row">Public Email</th><td><input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th scope="row">Contact Phone</th><td><input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" class="regular-text" /></td></tr>
                <tr valign="top">
                    <th scope="row">SMTP (Host/User/Pass)</th>
                    <td>
                        <input type="text" name="p_smtp_host" value="<?php echo esc_attr(get_option('p_smtp_host')); ?>" placeholder="Host" style="width:140px;"/>
                        <input type="text" name="p_smtp_user" value="<?php echo esc_attr(get_option('p_smtp_user')); ?>" placeholder="User" style="width:140px;"/>
                        <input type="password" name="p_smtp_pass" value="<?php echo esc_attr(get_option('p_smtp_pass')); ?>" placeholder="Pass" style="width:140px;"/>
                    </td>
                </tr>
            </table>

            <h2>3. Legal & PDF Invoice Data (SPoT)</h2>
            <table class="form-table">
                <tr valign="top"><th scope="row">Legal Company Name</th><td><input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th scope="row">Business ID (Y-tunnus)</th><td><input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th scope="row">Street Address</th><td><input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" class="regular-text" /></td></tr>
                <tr valign="top">
                    <th scope="row">Postcode & City</th>
                    <td>
                        <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" placeholder="00100" style="width:80px;" />
                        <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" placeholder="City" style="width:315px;" />
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Save & Sync Master Data'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $settings = [
        'p_villa_name', 'purnukka_primary_color', 'purnukka_secondary_color', 'purnukka_accent_color', 'purnukka_dark_color', 'purnukka_logo_url',
        'p_villa_email', 'p_villa_phone', 'p_smtp_host', 'p_smtp_user', 'p_smtp_pass', 'p_smtp_port',
        'p_company_name', 'p_business_id', 'p_legal_address', 'p_legal_postcode', 'p_legal_city'
    ];
    foreach ($settings as $s) { register_setting('purnukka-settings-group', $s); }
});

/**
 * MASTER DATA SYNC (SMTP & PDF INVOICE)
 */
function purnukka_sync_master_data() {
    // 1. Sync SMTP
    $smtp = get_option('wp_mail_smtp', []);
    $smtp['mail']['from_email'] = get_option('p_villa_email');
    $smtp['mail']['from_name']  = get_option('p_villa_name');
    $smtp['smtp']['host']       = get_option('p_smtp_host');
    $smtp['smtp']['user']       = get_option('p_smtp_user');
    $smtp['smtp']['pass']       = get_option('p_smtp_pass');
    $smtp['mail']['mailer']     = 'smtp';
    update_option('wp_mail_smtp', $smtp);

    // 2. Sync PDF Invoice Settings
    $invoice_settings = get_option('wpo_wcpdf_settings_general', []);
    $full_address = get_option('p_company_name') . "\n" . 
                    get_option('p_legal_address') . "\n" . 
                    get_option('p_legal_postcode') . " " . get_option('p_legal_city') . "\n" .
                    "Business ID: " . get_option('p_business_id');
    
    $invoice_settings['shop_name']    = get_option('p_company_name');
    $invoice_settings['shop_address'] = $full_address;
    update_option('wpo_wcpdf_settings_general', $invoice_settings);
}

/**
 * BRANDING & LOGO
 */
add_action('wp_head', function() {
    if (is_admin()) return;
    $c1 = get_option('purnukka_primary_color', '#c5a059');
    $c4 = get_option('purnukka_dark_color', '#1a1a1a');
    echo "<style>:root { --p-primary: $c1; --p-dark: $c4; }
    .button, button, .mphb-book-button { background-color: var(--p-primary) !important; color: #fff !important; }
    h1, h2, h3 { color: var(--p-dark) !important; }</style>";
}, 20);

add_filter('get_custom_logo', function($html) {
    $logo = get_option('purnukka_logo_url');
    if (!$logo) return $html;
    return sprintf('<a href="%s" class="custom-logo-link"><img src="%s" style="max-height:80px;"></a>', home_url('/'), esc_url($logo));
});

new PurnukkaStackCore();