<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.13)
 * Description: SPoT Master Control: Branding, SMTP, Legal, PDF Sync, Map, and Maintenance Mode.
 * Author: Purnukka Group Oy
 * Version: 0.13
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
        } else {
            add_action('get_header', [$this, 'purnukka_maintenance_mode']);
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

    public function purnukka_maintenance_mode() {
        if (get_option('p_maintenance_mode') !== 'on' || current_user_can('manage_options')) {
            return;
        }

        $logo = get_option('purnukka_logo_url');
        $name = get_option('p_villa_name', 'Our Website');
        $primary = get_option('purnukka_primary_color', '#c5a059');
        $dark = get_option('purnukka_dark_color', '#1a1a1a');

        wp_die(
            '<html><head><title>Under Construction - ' . esc_html($name) . '</title>
            <style>
                body { background: ' . $dark . '; color: #fff; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
                .container { padding: 20px; }
                img { max-height: 120px; margin-bottom: 30px; }
                h1 { color: ' . $primary . '; font-size: 2.5rem; margin-bottom: 10px; }
                p { font-size: 1.2rem; opacity: 0.8; }
            </style></head>
            <body><div class="container">' . 
            ($logo ? '<img src="' . esc_url($logo) . '">' : '') . 
            '<h1>' . esc_html($name) . '</h1>
            <p>Our new booking site is currently under construction.<br>We will be opening soon!</p>
            </div></body></html>',
            'Maintenance Mode',
            ['response' => 503]
        );
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
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'purnukka-settings', 'render_purnukka_settings_page', 'dashicons-admin-generic', 2);
});

function render_purnukka_settings_page() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        purnukka_sync_master_data();
    }
    ?>
    <div class="wrap">
        <h1 style="color: #c5a059; font-weight: bold;">Purnukka Stack – Configuration</h1>
        <hr>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            
            <h2>1. Property Identity & Branding</h2>
            <table class="form-table">
                <tr valign="top"><th scope="row">Master Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name', 'Villa Purnukka')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th scope="row">Colors (P/S/A/D)</th><td>
                    <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>" />
                    <input type="color" name="purnukka_secondary_color" value="<?php echo esc_attr(get_option('purnukka_secondary_color', '#a3844a')); ?>" />
                    <input type="color" name="purnukka_accent_color" value="<?php echo esc_attr(get_option('purnukka_accent_color', '#f1c40f')); ?>" />
                    <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>" />
                </td></tr>
                <tr valign="top"><th scope="row">Logo Image URL</th><td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text" /></td></tr>
            </table>

            <h2>2. Communication & SMTP</h2>
            <table class="form-table">
                <tr valign="top"><th scope="row">Customer Service Email</th><td><input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th scope="row">SMTP (Host/User/Pass)</th><td>
                    <input type="text" name="p_smtp_host" value="<?php echo esc_attr(get_option('p_smtp_host')); ?>" placeholder="Host" style="width:140px;"/>
                    <input type="text" name="p_smtp_user" value="<?php echo esc_attr(get_option('p_smtp_user')); ?>" placeholder="User" style="width:140px;"/>
                    <input type="password" name="p_smtp_pass" value="<?php echo esc_attr(get_option('p_smtp_pass')); ?>" placeholder="Pass" style="width:140px;"/>
                </td></tr>
            </table>

            <h2>3. Legal & PDF Invoice Data (SPoT)</h2>
            <table class="form-table">
                <tr valign="top"><th scope="row">Legal Company Name</th><td><input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th scope="row">Business ID & VAT %</th><td>
                    <input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" placeholder="Y-ID" style="width:150px;" />
                    <input type="number" name="p_vat_rate" value="<?php echo esc_attr(get_option('p_vat_rate', '10')); ?>" style="width:60px;" /> %
                </td></tr>
                <tr valign="top"><th scope="row">Address / Postcode / City</th><td>
                    <input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" placeholder="Street" style="width:180px;" />
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" placeholder="00100" style="width:70px;" />
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" placeholder="City" style="width:130px;" />
                </td></tr>
            </table>

            <h2>4. Maps & Maintenance</h2>
            <table class="form-table">
                <tr valign="top"><th scope="row">Lat / Long Coordinates</th><td>
                    <input type="text" name="p_latitude" value="<?php echo esc_attr(get_option('p_latitude')); ?>" style="width:150px;" />
                    <input type="text" name="p_longitude" value="<?php echo esc_attr(get_option('p_longitude')); ?>" style="width:150px;" />
                </td></tr>
                <tr valign="top"><th scope="row" style="color:red;">Enable Maintenance Mode</th><td>
                    <input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?> />
                    <span class="description">Hide website from visitors. Admins can still see it.</span>
                </td></tr>
            </table>
            
            <?php submit_button('Save & Sync Master Data'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $settings = [
        'p_villa_name', 'purnukka_primary_color', 'purnukka_secondary_color', 'purnukka_accent_color', 'purnukka_dark_color', 'purnukka_logo_url',
        'p_villa_email', 'p_smtp_host', 'p_smtp_user', 'p_smtp_pass', 'p_business_id', 'p_vat_rate',
        'p_legal_address', 'p_legal_postcode', 'p_legal_city', 'p_latitude', 'p_longitude', 'p_maintenance_mode'
    ];
    foreach ($settings as $s) { register_setting('purnukka-settings-group', $s); }
});

function purnukka_sync_master_data() {
    $smtp = get_option('wp_mail_smtp', []);
    $smtp['mail']['from_email'] = get_option('p_villa_email');
    $smtp['mail']['from_name']  = get_option('p_villa_name');
    $smtp['smtp']['host'] = get_option('p_smtp_host');
    $smtp['smtp']['user'] = get_option('p_smtp_user');
    $smtp['smtp']['pass'] = get_option('p_smtp_pass');
    $smtp['mail']['mailer'] = 'smtp';
    update_option('wp_mail_smtp', $smtp);

    $inv = get_option('wpo_wcpdf_settings_general', []);
    $inv['shop_name'] = get_option('p_company_name');
    $inv['shop_address'] = get_option('p_company_name') . "\n" . get_option('p_legal_address') . "\n" . get_option('p_legal_postcode') . " " . get_option('p_legal_city');
    update_option('wpo_wcpdf_settings_general', $inv);
}

add_action('wp_head', function() {
    if (is_admin()) return;
    $c1 = get_option('purnukka_primary_color', '#c5a059');
    $c4 = get_option('purnukka_dark_color', '#1a1a1a');
    echo "<style>:root { --p-primary: $c1; --p-dark: $c4; }
    .button, button, .mphb-book-button { background-color: var(--p-primary) !important; color: #fff !important; }
    h1, h2, h3 { color: var(--p-dark) !important; }</style>";
}, 20);

new PurnukkaStackCore();