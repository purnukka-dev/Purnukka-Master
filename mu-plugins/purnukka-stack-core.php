<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.33)
 * Description: The Final Master Piece: Global Countries, 4-Colors, SMTP, and Deep Contact Sync for PDF.
 * Author: Purnukka Group Oy
 * Version: 0.33
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
    }

    public function customize_admin_footer() {
        return sprintf('<strong>%s</strong> | Purnukka Stack Master Control', esc_html(get_option('p_villa_name', 'Villa Purnukka')));
    }

    public function remove_wp_logo() {
        global $wp_admin_bar;
        if (isset($wp_admin_bar)) $wp_admin_bar->remove_menu('wp-logo');
    }

    public function admin_declutter_css() {
        echo '<style>.notice-info, .notice-warning:not(.error), .update-nag, .wp-mail-smtp-review-notice, .mphb-notice, #footer-upgrade, .wp-mail-smtp-upgrade-bar, .mphb-upgrade-notice { display: none !important; } #wp-admin-bar-wp-logo { display: none !important; }</style>';
    }

    public function remove_all_plugin_notices() {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }

    public function purnukka_maintenance_mode() {
        if (get_option('p_maintenance_mode') !== 'on' || current_user_can('manage_options')) return;
        $logo = get_option('purnukka_logo_url');
        $name = get_option('p_villa_name', 'Our Website');
        $primary = get_option('purnukka_primary_color', '#c5a059');
        $dark = get_option('purnukka_dark_color', '#1a1a1a');
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        echo "<!DOCTYPE html><html style='background:$dark;margin:0;padding:0;overflow:hidden;width:100%;height:100%;'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>$name</title><style>
        html,body{background:$dark!important;margin:0;padding:0;height:100vh;width:100vw;display:flex;align-items:center;justify-content:center;color:#fff;font-family:sans-serif;overflow:hidden;}
        .c{text-align:center;animation:f 1.5s;padding:20px;} @keyframes f{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        img{max-height:120px;margin-bottom:30px} h1{color:$primary;font-size:3.5rem;margin:0;font-weight:700;} hr{border:0;height:1px;background:linear-gradient(to right,transparent,$primary,transparent);margin:25px auto;width:60%}
        p{font-size:1.2rem;opacity:0.8;line-height:1.5;}
        </style></head><body><div class='c'>".($logo?"<img src='".esc_url($logo)."'>":"")."<h1>".esc_html($name)."</h1><hr><p>Opening soon!</p></div></body></html>";
        exit;
    }
}

function purnukka_sync_master_data() {
    // 1. SMTP Sync
    $smtp = get_option('wp_mail_smtp', []);
    $smtp['mail']['from_email'] = get_option('p_villa_email');
    $smtp['mail']['from_name']  = get_option('p_villa_name');
    $smtp['smtp']['host'] = get_option('p_smtp_host');
    $smtp['smtp']['user'] = get_option('p_smtp_user');
    $smtp['smtp']['pass'] = get_option('p_smtp_pass');
    $smtp['mail']['mailer'] = 'smtp';
    update_option('wp_mail_smtp', $smtp);

    // 2. PDF Deep Precision Sync
    $pdf = get_option('wpo_wcpdf_settings_general', []);
    $country_code = get_option('p_legal_country', 'FI');
    $wc_countries = WC()->countries->get_countries();
    $country_full_name = isset($wc_countries[$country_code]) ? $wc_countries[$country_code] : 'Finland';

    $c_name = get_option('p_company_name');
    $c_email = get_option('p_villa_email');
    $c_phone = get_option('p_villa_phone');
    $c_id = get_option('p_business_id');

    // Individual keys for PDF
    $pdf['shop_name'] = $c_name;
    $pdf['shop_address_line_1'] = get_option('p_legal_address');
    $pdf['shop_city'] = get_option('p_legal_city');
    $pdf['shop_postcode'] = get_option('p_legal_postcode');
    $pdf['shop_country'] = $country_code;
    $pdf['shop_phone'] = $c_phone;
    $pdf['shop_email_address'] = $c_email;
    $pdf['shop_extra_1'] = "Business ID: " . $c_id;

    // Object mapping for PDF internal structure (Fixing {"default":""} issues)
    $pdf['shop_address_city']     = array('default' => get_option('p_legal_city'));
    $pdf['shop_address_postcode'] = array('default' => get_option('p_legal_postcode'));
    $pdf['shop_address_country']  = array('default' => $country_code);
    $pdf['shop_phone_number']     = array('default' => $c_phone);
    $pdf['shop_email_address_obj'] = array('default' => $c_email);

    // Final compiled address and footer
    $pdf['shop_address'] = $c_name . "\n" . get_option('p_legal_address') . "\n" . get_option('p_legal_postcode') . " " . get_option('p_legal_city') . "\n" . $country_full_name;
    $pdf['footer'] = $c_name . " | " . $c_email . " | " . $c_phone . " | Business ID: " . $c_id;

    update_option('wpo_wcpdf_settings_general', $pdf);
}

add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'pukka-settings', 'render_pukka_settings', 'dashicons-admin-generic', 2);
});

function render_pukka_settings() {
    if (isset($_GET['settings-updated'])) purnukka_sync_master_data();
    $wc_countries = WC()->countries->get_countries();
    ?>
    <div class="wrap">
        <h1 style="color:#c5a059;">Purnukka Stack v0.33</h1>
        <hr>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            
            <h3>1. Branding & 4-Color System</h3>
            <table class="form-table">
                <tr><th>Villa Name / Tagline</th><td>
                    <input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name')); ?>" class="regular-text">
                    <input type="text" name="p_villa_tagline" value="<?php echo esc_attr(get_option('p_villa_tagline')); ?>" class="regular-text">
                </td></tr>
                <tr><th>Colors</th><td>
                    <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>"> Primary &nbsp;
                    <input type="color" name="purnukka_secondary_color" value="<?php echo esc_attr(get_option('purnukka_secondary_color', '#a3844a')); ?>"> Hover &nbsp;
                    <input type="color" name="purnukka_accent_color" value="<?php echo esc_attr(get_option('purnukka_accent_color', '#f1c40f')); ?>"> Accent &nbsp;
                    <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>"> Dark
                </td></tr>
                <tr><th>Logo URL</th><td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text"></td></tr>
            </table>

            <h3>2. Communication & SMTP Master</h3>
            <table class="form-table">
                <tr><th>Contact Details</th><td>
                    <input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" placeholder="Public Email">
                    <input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" placeholder="Phone Number">
                </td></tr>
                <tr><th>SMTP Config</th><td>
                    <input type="text" name="p_smtp_host" value="<?php echo esc_attr(get_option('p_smtp_host')); ?>" placeholder="Host">
                    <input type="text" name="p_smtp_user" value="<?php echo esc_attr(get_option('p_smtp_user')); ?>" placeholder="User">
                    <input type="password" name="p_smtp_pass" value="<?php echo esc_attr(get_option('p_smtp_pass')); ?>" placeholder="Pass">
                </td></tr>
            </table>

            <h3>3. Legal & World Country SPoT</h3>
            <table class="form-table">
                <tr><th>Company & VAT</th><td>
                    <input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text">
                    <input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" placeholder="VAT ID">
                </td></tr>
                <tr><th>Address</th><td>
                    <input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" placeholder="Street">
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:80px" placeholder="Zip">
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:160px" placeholder="City">
                </td></tr>
                <tr><th>Select Country</th><td>
                    <select name="p_legal_country" style="width:250px;">
                        <?php foreach($wc_countries as $code => $name): ?>
                            <option value="<?php echo $code; ?>" <?php selected(get_option('p_legal_country', 'FI'), $code); ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td></tr>
            </table>

            <h3>4. Maintenance Mode</h3>
            <table class="form-table">
                <tr><th>Curtain Mode</th><td><input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?>> Enable Curtain</td></tr>
            </table>
            
            <?php submit_button('Save & Sync Global Master Data'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','p_villa_tagline','purnukka_logo_url','purnukka_primary_color','purnukka_secondary_color','purnukka_accent_color','purnukka_dark_color','p_villa_email','p_smtp_host','p_smtp_user','p_smtp_pass','p_villa_phone','p_company_name','p_business_id','p_legal_address','p_legal_postcode','p_legal_city','p_legal_country','p_maintenance_mode'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

add_action('wp_head', function() {
    if (is_admin()) return;
    $c1 = get_option('purnukka_primary_color', '#c5a059');
    $c2 = get_option('purnukka_secondary_color', '#a3844a');
    $c3 = get_option('purnukka_accent_color', '#f1c40f');
    $c4 = get_option('purnukka_dark_color', '#1a1a1a');
    echo "<style>:root { --p-primary: $c1; --p-secondary: $c2; --p-accent: $c3; --p-dark: $c4; }
    .button, button, .mphb-book-button { background-color: var(--p-primary) !important; color: #fff !important; border:none !important; }
    .button:hover, button:hover { background-color: var(--p-secondary) !important; }
    a { color: var(--p-accent); } h1, h2, h3 { color: var(--p-dark) !important; }</style>";
}, 20);

new PurnukkaStackCore();