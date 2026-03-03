<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.14)
 * Description: Ultimate SPoT Control: Branding, SMTP, Legal, Full PDF Sync, and Full-Screen Curtain.
 * Author: Purnukka Group Oy
 * Version: 0.14
 */

if ( !defined('ABSPATH') ) exit;

class PurnukkaStackCore {

    public function __construct() {
        if (is_admin()) {
            add_filter('admin_footer_text', [$this, 'customize_admin_footer']);
            add_action('wp_before_admin_bar_render', [$this, 'remove_wp_logo'], 0);
            add_action('admin_head', [$this, 'admin_declutter_css']);
            add_action('admin_menu', [$this, 'remove_all_plugin_notices'], 999);
        } else {
            add_action('template_redirect', [$this, 'purnukka_maintenance_mode']);
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

        $logo    = get_option('purnukka_logo_url');
        $name    = get_option('p_villa_name', 'Our Website');
        $primary = get_option('purnukka_primary_color', '#c5a059');
        $dark    = get_option('purnukka_dark_color', '#1a1a1a');

        header('HTTP/1.1 503 Service Temporarily Unavailable');
        ?>
        <!DOCTYPE html>
        <html lang="en" style="background-color: <?php echo esc_attr($dark); ?>;">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html($name); ?> - Coming Soon</title>
            <style>
                html, body { background-color: <?php echo esc_attr($dark); ?> !important; margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; }
                body { display: flex; align-items: center; justify-content: center; color: #ffffff; font-family: sans-serif; text-align: center; }
                .curtain-content { animation: fadeIn 1.5s ease-out; padding: 20px; }
                img.logo { max-height: 100px; margin-bottom: 30px; }
                h1 { color: <?php echo esc_attr($primary); ?>; font-size: 3rem; margin: 0 0 15px 0; }
                hr { border: 0; height: 1px; background: linear-gradient(to right, transparent, <?php echo esc_attr($primary); ?>, transparent); margin: 25px auto; width: 50%; }
                p { font-size: 1.1rem; color: rgba(255,255,255,0.7); }
                @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
            </style>
        </head>
        <body>
            <div class="curtain-content">
                <?php if ($logo) : ?>
                    <img src="<?php echo esc_url($logo); ?>" class="logo">
                <?php endif; ?>
                <h1><?php echo esc_html($name); ?></h1>
                <hr>
                <p>Our site is currently under construction.<br>Opening soon!</p>
            </div>
        </body>
        </html>
        <?php
        exit; 
    }

    private function register_shortcodes() {
        add_shortcode('p_villa_name', function() { return get_option('p_villa_name'); });
        add_shortcode('p_address', function() { 
            return get_option('p_legal_address') . ', ' . get_option('p_legal_postcode') . ' ' . get_option('p_legal_city'); 
        });
    }
}

/**
 * SETTINGS PAGE & SYNC
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
        <h1>Purnukka Stack Configuration</h1>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            
            <h2>1. Branding</h2>
            <table class="form-table">
                <tr valign="top"><th>Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th>Primary & Dark Color</th><td>
                    <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>" />
                    <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>" />
                </td></tr>
                <tr valign="top"><th>Logo URL</th><td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text" /></td></tr>
            </table>

            <h2>2. SMTP & Legal (PDF Sync)</h2>
            <table class="form-table">
                <tr valign="top"><th>Customer Email</th><td><input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th>Legal Company Name</th><td><input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th>Street Address</th><td><input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th>Postcode & City</th><td>
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:70px;" />
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:200px;" />
                </td></tr>
                <tr valign="top"><th>Business ID</th><td><input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" class="regular-text" /></td></tr>
            </table>

            <h2>3. Maintenance</h2>
            <table class="form-table">
                <tr valign="top"><th>Curtain Mode</th><td>
                    <input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?> /> Enable Full-Screen Curtain
                </td></tr>
            </table>
            <?php submit_button('Save & Force Sync SPoT Data'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $settings = ['p_villa_name', 'purnukka_primary_color', 'purnukka_dark_color', 'purnukka_logo_url', 'p_villa_email', 'p_company_name', 'p_business_id', 'p_legal_address', 'p_legal_postcode', 'p_legal_city', 'p_maintenance_mode'];
    foreach ($settings as $s) { register_setting('purnukka-settings-group', $s); }
});

function purnukka_sync_master_data() {
    // 1. Force Sync PDF Invoice Settings
    $inv = get_option('wpo_wcpdf_settings_general', []);
    $full_address = get_option('p_company_name') . "\n" . 
                    get_option('p_legal_address') . "\n" . 
                    get_option('p_legal_postcode') . " " . get_option('p_legal_city') . "\n" .
                    "Y-tunnus: " . get_option('p_business_id');
    
    $inv['shop_name']    = get_option('p_company_name');
    $inv['shop_address'] = $full_address;
    update_option('wpo_wcpdf_settings_general', $inv);
}

new PurnukkaStackCore();