<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.11)
 * Description: Master Control Panel with Physical SMTP Sync & Admin Cleanup.
 * Author: Purnukka Group Oy
 * Version: 0.11
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
    // Force sync on page load if settings were just saved
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
        purnukka_sync_smtp_to_plugin();
    }
    ?>
    <div class="wrap">
        <h1 style="color: #c5a059; font-weight: bold;">Purnukka Stack – Configuration</h1>
        <p>Global variables for branding, SMTP, and property details.</p>
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
                    <th scope="row">Master Villa Tagline</th>
                    <td><input type="text" name="p_villa_tagline" value="<?php echo esc_attr(get_option('p_villa_tagline')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Colors (Primary / Hover / Accent / Dark)</th>
                    <td>
                        <input type="color" id="color_primary" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>" />
                        <input type="color" id="color_secondary" name="purnukka_secondary_color" value="<?php echo esc_attr(get_option('purnukka_secondary_color', '#a3844a')); ?>" />
                        <input type="color" id="color_accent" name="purnukka_accent_color" value="<?php echo esc_attr(get_option('purnukka_accent_color', '#f1c40f')); ?>" />
                        <input type="color" id="color_dark" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>" />
                        <button type="button" id="reset-colors" class="button button-secondary" style="margin-left:10px;">Reset Colors</button>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Logo Image URL</th>
                    <td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2>2. Communication & SMTP</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Customer Service Email</th>
                    <td><input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Host & Port</th>
                    <td>
                        <input type="text" name="p_smtp_host" value="<?php echo esc_attr(get_option('p_smtp_host')); ?>" placeholder="smtp.example.com" class="regular-text" style="width: 250px;" />
                        <input type="number" name="p_smtp_port" value="<?php echo esc_attr(get_option('p_smtp_port', '587')); ?>" class="small-text" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Username</th>
                    <td><input type="text" name="p_smtp_user" value="<?php echo esc_attr(get_option('p_smtp_user')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">SMTP Password</th>
                    <td><input type="password" name="p_smtp_pass" value="<?php echo esc_attr(get_option('p_smtp_pass')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Contact Phone</th>
                    <td><input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2>3. Legal & Company Details</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Business ID</th>
                    <td><input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" class="regular-text" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">VAT Rate (%)</th>
                    <td><input type="number" name="p_vat_rate" value="<?php echo esc_attr(get_option('p_vat_rate', '10')); ?>" class="small-text" /> %</td>
                </tr>
            </table>
            
            <?php submit_button('Save Purnukka Stack Settings'); ?>
        </form>
    </div>

    <script>
    document.getElementById('reset-colors').addEventListener('click', function() {
        if (confirm('Reset branding colors to default?')) {
            document.getElementById('color_primary').value = '#c5a059';
            document.getElementById('color_secondary').value = '#a3844a';
            document.getElementById('color_accent').value = '#f1c40f';
            document.getElementById('color_dark').value = '#1a1a1a';
        }
    });
    </script>
    <?php
}

add_action('admin_init', function() {
    $settings = [
        'p_villa_name', 'p_villa_tagline', 'purnukka_primary_color', 'purnukka_secondary_color', 
        'purnukka_accent_color', 'purnukka_dark_color', 'purnukka_logo_url',
        'p_villa_email', 'p_villa_phone', 'p_business_id', 'p_vat_rate',
        'p_smtp_host', 'p_smtp_port', 'p_smtp_user', 'p_smtp_pass'
    ];
    foreach ($settings as $s) { register_setting('purnukka-settings-group', $s); }
});

/**
 * BRANDING INJECTION (CSS)
 */
add_action('wp_head', function() {
    if (is_admin()) return;
    $c1 = get_option('purnukka_primary_color', '#c5a059');
    $c2 = get_option('purnukka_secondary_color', '#a3844a');
    $c3 = get_option('purnukka_accent_color', '#f1c40f');
    $c4 = get_option('purnukka_dark_color', '#1a1a1a');
    echo "<style>:root { --p-primary: $c1; --p-secondary: $c2; --p-accent: $c3; --p-dark: $c4; }
    .button, button, .mphb-book-button { background-color: var(--p-primary) !important; border-color: var(--p-primary) !important; color: #fff !important; }
    .button:hover, button:hover { background-color: var(--p-secondary) !important; }
    a { color: var(--p-accent); } h1, h2, h3 { color: var(--p-dark) !important; }</style>";
}, 20);

/**
 * SMTP SETTINGS SYNC
 */
function purnukka_sync_smtp_to_plugin() {
    $current_options = get_option('wp_mail_smtp', []);
    
    $current_options['mail']['from_email'] = get_option('p_villa_email');
    $current_options['mail']['from_name']  = get_option('p_villa_name');
    $current_options['mail']['mailer']     = 'smtp';
    
    $current_options['smtp']['host']       = get_option('p_smtp_host');
    $current_options['smtp']['user']       = get_option('p_smtp_user');
    $current_options['smtp']['pass']       = get_option('p_smtp_pass');
    $current_options['smtp']['port']       = get_option('p_smtp_port', '587');
    $current_options['smtp']['auth']       = true;
    $current_options['smtp']['encryption'] = 'tls';
    $current_options['smtp']['autotls']    = true;

    update_option('wp_mail_smtp', $current_options);
}

// Triggers sync whenever SMTP options are updated
add_action('update_option_p_smtp_host', 'purnukka_sync_smtp_to_plugin');
add_action('update_option_p_smtp_user', 'purnukka_sync_smtp_to_plugin');
add_action('update_option_p_smtp_pass', 'purnukka_sync_smtp_to_plugin');
add_action('update_option_p_smtp_port', 'purnukka_sync_smtp_to_plugin');

/**
 * LOGO & TEXT REPLACEMENT
 */
add_filter('get_custom_logo', function($html) {
    $logo = get_option('purnukka_logo_url');
    if (!$logo) return $html;
    return sprintf('<a href="%s" class="custom-logo-link"><img src="%s" style="max-height:80px;"></a>', home_url('/'), esc_url($logo));
});

add_filter('the_content', 'purnukka_master_text_replacer');
add_filter('the_title', 'purnukka_master_text_replacer');
function purnukka_master_text_replacer($text) {
    if (is_admin()) return $text;
    $name = get_option('p_villa_name', 'Villa Purnukka');
    return str_replace('Villa Purnukka', $name, $text);
}

new PurnukkaStackCore();