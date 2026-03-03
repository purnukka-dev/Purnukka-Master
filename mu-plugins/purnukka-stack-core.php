<?php
/**
 * Plugin Name: Purnukka Stack - Unified Ohjaamo (v1.2)
 * Description: Total Decoupling: Every core setting moved to UI. Nuclear Logo, 4-Color, SMTP, VAT, and Global Replacer.
 * Author: Purnukka Group Oy
 * Version: 1.2
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
            add_action('wp_head', [$this, 'inject_brand_styles'], 100);
            add_filter('the_content', [$this, 'master_text_replacer']);
            add_filter('the_title', [$this, 'master_text_replacer']);
        }
        
        // NUCLEAR SYNC (Logo & PDF Settings)
        add_filter('option_wpo_wcpdf_settings_general', [$this, 'force_branding_into_pdf'], 999);
    }

    // 1. BRAND REPLACER (v0.5 logic moved to Ohjaamo control)
    public function master_text_replacer($text) {
        if (is_admin()) return $text;
        $new_name = get_option('p_villa_name', 'Villa Purnukka');
        if ($new_name !== 'Villa Purnukka') {
            $text = str_replace('Villa Purnukka', $new_name, $text);
        }
        return $text;
    }

    // 2. DYNAMIC CSS (4-Color System + Extra Selectors like Complianz)
    public function inject_brand_styles() {
        $c1 = get_option('purnukka_primary_color', '#c5a059');
        $c2 = get_option('purnukka_secondary_color', '#a3844a');
        $c3 = get_option('purnukka_accent_color', '#f1c40f');
        $c4 = get_option('purnukka_dark_color', '#1a1a1a');
        echo "<style>
            :root { --p-primary: $c1; --p-secondary: $c2; --p-accent: $c3; --p-dark: $c4; }
            .button, button, .mphb-book-button, .cmplz-btn.cmplz-accept { 
                background-color: var(--p-primary) !important; 
                border-color: var(--p-primary) !important; 
                color: #fff !important; 
            }
            .button:hover, button:hover, .mphb-book-button:hover { 
                background-color: var(--p-secondary) !important; 
            }
            a { color: var(--p-accent); }
            h1, h2, h3, h4 { color: var(--p-dark) !important; }
        </style>";
    }

    // 3. NUCLEAR PDF LOGO & ADDRESS INJECTION
    public function force_branding_into_pdf($settings) {
        $logo_url = get_option('purnukka_logo_url');
        $logo_id = attachment_url_to_postid($logo_url);
        if ($logo_id) {
            if (!is_array($settings)) $settings = [];
            $settings['header_logo'] = $logo_id;
            $settings['header_logo_height'] = '35';
        }
        return $settings;
    }

    public function customize_admin_footer() {
        return sprintf('<strong>%s</strong> | Purnukka Stack Master Control', esc_html(get_option('p_villa_name', 'Villa Purnukka')));
    }

    public function remove_wp_logo() {
        global $wp_admin_bar;
        if (isset($wp_admin_bar)) $wp_admin_bar->remove_menu('wp-logo');
    }

    public function admin_declutter_css() {
        echo '<style>.notice-info, .notice-warning:not(.error), .update-nag, .wp-mail-smtp-review-notice, .mphb-notice, #footer-upgrade, .wp-mail-smtp-upgrade-bar, .mphb-upgrade-notice { display: none !important; }</style>';
    }

    public function remove_all_plugin_notices() {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }

    public function purnukka_maintenance_mode() {
        if (get_option('p_maintenance_mode') !== 'on' || current_user_can('manage_options')) return;
        $logo = get_option('purnukka_logo_url');
        $name = get_option('p_villa_name', 'Our Website');
        $dark = get_option('purnukka_dark_color', '#1a1a1a');
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        echo "<!DOCTYPE html><html style='background:$dark;'><body style='color:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;'>
        <div style='text-align:center;'>".($logo?"<img src='".esc_url($logo)."' style='max-height:100px;'>":"")."<h1>".esc_html($name)."</h1><p>Opening soon!</p></div></body></html>";
        exit;
    }
}

/**
 * GLOBAL DATA SYNC (SMTP & PDF Objects)
 */
function purnukka_sync_master_data() {
    // SMTP Sync
    $smtp = get_option('wp_mail_smtp', []);
    $smtp['mail']['from_email'] = get_option('p_villa_email');
    $smtp['mail']['from_name']  = get_option('p_villa_name');
    $smtp['smtp']['host'] = get_option('p_smtp_host');
    $smtp['smtp']['user'] = get_option('p_smtp_user');
    $smtp['smtp']['pass'] = get_option('p_smtp_pass');
    $smtp['mail']['mailer'] = 'smtp';
    update_option('wp_mail_smtp', $smtp);

    // PDF Static Sync
    $pdf = get_option('wpo_wcpdf_settings_general', []);
    $c_code = get_option('p_legal_country', 'FI');
    $pdf['shop_name'] = get_option('p_company_name');
    $pdf['shop_address_line_1'] = get_option('p_legal_address');
    $pdf['shop_city'] = get_option('p_legal_city');
    $pdf['shop_postcode'] = get_option('p_legal_postcode');
    $pdf['shop_country'] = $c_code;
    
    // Debugger-fix for Object Keys
    $pdf['shop_address_city'] = array('default' => get_option('p_legal_city'));
    $pdf['shop_address_postcode'] = array('default' => get_option('p_legal_postcode'));
    $pdf['shop_address_country'] = array('default' => $c_code);

    update_option('wpo_wcpdf_settings_general', $pdf);
}

/**
 * ADMIN UI (Ohjaamo)
 */
add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'purnukka-settings', 'render_pukka_ui', 'dashicons-admin-generic', 2);
});

function render_pukka_ui() {
    if (isset($_GET['settings-updated'])) purnukka_sync_master_data();
    $wc_countries = WC()->countries->get_countries();
    ?>
    <div class="wrap">
        <h1 style="color: #c5a059;">Purnukka Ohjaamo v1.2</h1>
        <hr>
        <form method="post" action="options.php" id="pukka-form">
            <?php settings_fields('purnukka-settings-group'); ?>
            
            <h3>1. Identity & Brand Replacer</h3>
            <table class="form-table">
                <tr><th>Master Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name', 'Villa Purnukka')); ?>" class="regular-text"></td></tr>
                <tr><th>Villa Tagline</th><td><input type="text" name="p_villa_tagline" value="<?php echo esc_attr(get_option('p_villa_tagline')); ?>" class="regular-text"></td></tr>
                <tr><th>Logo Master URL</th><td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text"></td></tr>
                <tr><th>Colors (4-Color System)</th><td>
                    <input type="color" id="c_p" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>"> Primary
                    <input type="color" id="c_h" name="purnukka_secondary_color" value="<?php echo esc_attr(get_option('purnukka_secondary_color', '#a3844a')); ?>"> Hover
                    <input type="color" id="c_a" name="purnukka_accent_color" value="<?php echo esc_attr(get_option('purnukka_accent_color', '#f1c40f')); ?>"> Accent
                    <input type="color" id="c_d" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>"> Dark
                    <button type="button" id="reset-colors" class="button">Reset</button>
                </td></tr>
            </table>

            <h3>2. Legal & Financial</h3>
            <table class="form-table">
                <tr><th>Company Name / Y-tunnus</th><td>
                    <input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text">
                    <input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" placeholder="Y-tunnus">
                </td></tr>
                <tr><th>VAT Rate %</th><td><input type="number" name="p_vat_rate" value="<?php echo esc_attr(get_option('p_vat_rate', '10')); ?>" class="small-text"> %</td></tr>
                <tr><th>Legal Address (Invoices)</th><td><input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" class="regular-text"></td></tr>
                <tr><th>Zip / City / Country</th><td>
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:80px">
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:160px">
                    <select name="p_legal_country">
                        <?php foreach($wc_countries as $code => $name): ?>
                            <option value="<?php echo $code; ?>" <?php selected(get_option('p_legal_country', 'FI'), $code); ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td></tr>
            </table>

            <h3>3. Communication & SEO</h3>
            <table class="form-table">
                <tr><th>Email / Phone</th><td>
                    <input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>">
                    <input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>">
                </td></tr>
                <tr><th>SEO Slugs</th><td>
                    <input type="text" name="p_villa_slug" value="<?php echo esc_attr(get_option('p_villa_slug', 'villapurnukka')); ?>" placeholder="Villa Slug">
                    <input type="text" name="p_location_slug" value="<?php echo esc_attr(get_option('p_location_slug')); ?>" placeholder="Location Slug">
                </td></tr>
                <tr><th>Maps (Lat/Long)</th><td>
                    <input type="text" name="p_latitude" value="<?php echo esc_attr(get_option('p_latitude')); ?>" placeholder="Lat">
                    <input type="text" name="p_longitude" value="<?php echo esc_attr(get_option('p_longitude')); ?>" placeholder="Long">
                </td></tr>
            </table>

            <div style="margin-top:20px;">
                <?php submit_button('Save & Sync Ohjaamo', 'primary', 'submit', false); ?>
                <input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?>> Maintenance Mode
            </div>
        </form>
    </div>

    <script>
    document.getElementById('reset-colors').addEventListener('click', function() {
        if (confirm('Palautetaanko oletusvärit?')) {
            document.getElementById('c_p').value = '#c5a059';
            document.getElementById('c_h').value = '#a3844a';
            document.getElementById('c_a').value = '#f1c40f';
            document.getElementById('c_d').value = '#1a1a1a';
        }
    });
    </script>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','p_villa_tagline','purnukka_logo_url','purnukka_primary_color','purnukka_secondary_color','purnukka_accent_color','purnukka_dark_color','p_villa_email','p_villa_phone','p_villa_slug','p_location_slug','p_company_name','p_business_id','p_legal_address','p_legal_postcode','p_legal_city','p_legal_country','p_latitude','p_longitude','p_maintenance_mode','p_vat_rate','p_smtp_host','p_smtp_user','p_smtp_pass'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

new PurnukkaStackCore();