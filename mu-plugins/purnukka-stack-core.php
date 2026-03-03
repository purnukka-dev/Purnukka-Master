<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.9)
 * Description: Master Control Panel with 4-color system and Reset to Defaults.
 * Author: Purnukka Group Oy
 * Version: 0.9
 */

if ( !defined('ABSPATH') ) exit;

class PurnukkaStackCore {
    public function __construct() {
        if (is_admin()) {
            add_filter('admin_footer_text', [$this, 'customize_admin_footer']);
            add_action('wp_before_admin_bar_render', [$this, 'remove_wp_logo'], 0);
        }
    }
    public function customize_admin_footer() {
        $brand_name = get_option('p_villa_name', 'Villa Purnukka');
        return sprintf('<strong>%s</strong> | Purnukka Stack Master Control', esc_html($brand_name));
    }
    public function remove_wp_logo() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }
}

add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'purnukka-settings', 'render_purnukka_settings_page', 'dashicons-admin-generic', 2);
});

function render_purnukka_settings_page() {
    ?>
    <div class="wrap">
        <h1 style="color: #c5a059; font-weight: bold;">Purnukka Stack – Property Configuration</h1>
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
                    <th scope="row">Primary Brand Color</th>
                    <td><input type="color" id="color_primary" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>" /> <span class="description">Main buttons and icons</span></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Secondary Color (Hover)</th>
                    <td><input type="color" id="color_secondary" name="purnukka_secondary_color" value="<?php echo esc_attr(get_option('purnukka_secondary_color', '#a3844a')); ?>" /> <span class="description">Button hover states</span></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Accent Color</th>
                    <td><input type="color" id="color_accent" name="purnukka_accent_color" value="<?php echo esc_attr(get_option('purnukka_accent_color', '#f1c40f')); ?>" /> <span class="description">Links and highlights</span></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Dark / Heading Color</th>
                    <td><input type="color" id="color_dark" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>" /> <span class="description">Titles (h1, h2, h3)</span></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Logo Image URL</th>
                    <td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2>2. Communication & Legal</h2>
            <table class="form-table">
                <tr valign="top"><th>Email</th><td><input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th>Phone</th><td><input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th>Business ID</th><td><input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" class="regular-text" /></td></tr>
                <tr valign="top"><th>VAT Rate %</th><td><input type="number" name="p_vat_rate" value="<?php echo esc_attr(get_option('p_vat_rate', '10')); ?>" class="small-text" /></td></tr>
            </table>

            <div style="margin-top: 20px;">
                <?php submit_button('Save All Changes', 'primary', 'submit', false); ?>
                <button type="button" id="reset-colors" class="button button-secondary" style="margin-left: 10px;">Reset Colors to Default</button>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('reset-colors').addEventListener('click', function() {
        if (confirm('Haluatko varmasti palauttaa alkuperäiset brändivärit?')) {
            document.getElementById('color_primary').value = '#c5a059';
            document.getElementById('color_secondary').value = '#a3844a';
            document.getElementById('color_accent').value = '#f1c40f';
            document.getElementById('color_dark').value = '#1a1a1a';
            alert('Värit palautettu. Muista painaa "Save All Changes" tallentaaksesi ne.');
        }
    });
    </script>
    <?php
}

add_action('admin_init', function() {
    $settings = ['p_villa_name', 'p_villa_tagline', 'purnukka_primary_color', 'purnukka_secondary_color', 'purnukka_accent_color', 'purnukka_dark_color', 'purnukka_logo_url', 'p_villa_email', 'p_villa_phone', 'p_business_id', 'p_vat_rate'];
    foreach ($settings as $s) { register_setting('purnukka-settings-group', $s); }
});

add_action('wp_head', function() {
    if (is_admin()) return;
    $c1 = get_option('purnukka_primary_color', '#c5a059');
    $c2 = get_option('purnukka_secondary_color', '#a3844a');
    $c3 = get_option('purnukka_accent_color', '#f1c40f');
    $c4 = get_option('purnukka_dark_color', '#1a1a1a');
    echo "<style>:root { --p-primary: $c1; --p-secondary: $c2; --p-accent: $c3; --p-dark: $c4; }
    .button, button, .mphb-book-button { background-color: var(--p-primary) !important; border-color: var(--p-primary) !important; color: #fff !important; }
    .button:hover, button:hover, .mphb-book-button:hover { background-color: var(--p-secondary) !important; border-color: var(--p-secondary) !important; }
    a { color: var(--p-accent); } h1, h2, h3 { color: var(--p-dark) !important; }</style>";
}, 20);
/**
 * PHASE 11: ADMIN DE-CLUTTER (Anti-Ad & Notice Silencer)
 * Hides annoying plugin ads and upgrade notices from the dashboard.
 */
add_action('admin_head', function() {
    // Jos haluat silti nähdä kriittiset virheet, jätetään ne rauhaan,
    // mutta piilotetaan yleiset "notice" ja "info" -laatikot.
    echo '<style>
        /* Piilotetaan yleiset ilmoituslaatikot paitsi meidän omissa säädöissä */
        .toplevel_page_purnukka-settings .notice,
        .toplevel_page_purnukka-settings .update-nag {
            display: block !important;
        }
        
        /* Piilotetaan muiden pluginien mainokset ja kehotukset kaikkialta */
        .notice-info, 
        .notice-warning:not(.error), 
        .update-nag, 
        #wp-admin-bar-wp-logo,
        .wp-mail-smtp-review-notice,
        .mphb-notice { 
            display: none !important; 
        }
        
        /* Erityisesti Bookliumin ja WP Mail SMTP:n "Upgrade" -kehotteet */
        #footer-upgrade, .wp-mail-smtp-upgrade-bar, .mphb-upgrade-notice {
            display: none !important;
        }
    </style>';
});

// Agreessiivisempi tapa poistaa ilmoitukset PHP:n kautta
add_action('admin_menu', function() {
    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');
}, 999);
new PurnukkaStackCore();