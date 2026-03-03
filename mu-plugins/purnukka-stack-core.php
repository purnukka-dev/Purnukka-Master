<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.35)
 * Description: Ultimate Scalability: Automatic Logo Sideloading (URL to ID), Global Countries, 4-Colors, and SMTP.
 * Author: Purnukka Group Oy
 * Version: 0.35
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

/**
 * AUTO-SIDELOAD SYNC ENGINE
 */
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

    // 2. PDF Deep Sync with Auto-Sideloading
    $pdf = get_option('wpo_wcpdf_settings_general', []);
    $logo_url = get_option('purnukka_logo_url');
    
    // Etsitään ID tai ladataan se mediakirjastoon
    $logo_id = attachment_url_to_postid($logo_url);
    
    if (!$logo_id && !empty($logo_url)) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $logo_id = media_sideload_image($logo_url, 0, null, 'id');
    }

    if (!is_wp_error($logo_id) && $logo_id) {
        $pdf['header_logo'] = $logo_id;
    }

    $c_name = get_option('p_company_name');
    $c_country = get_option('p_legal_country', 'FI');
    $wc_countries = WC()->countries->get_countries();
    $country_name = isset($wc_countries[$c_country]) ? $wc_countries[$c_country] : 'Finland';

    $pdf['shop_name'] = $c_name;
    $pdf['shop_address_line_1'] = get_option('p_legal_address');
    $pdf['shop_city'] = get_option('p_legal_city');
    $pdf['shop_postcode'] = get_option('p_legal_postcode');
    $pdf['shop_country'] = $c_country;
    $pdf['shop_phone'] = get_option('p_villa_phone');
    $pdf['shop_extra_1'] = "Business ID: " . get_option('p_business_id');

    // Object Sync (Debugger Fix)
    $pdf['shop_address_city']     = array('default' => get_option('p_legal_city'));
    $pdf['shop_address_postcode'] = array('default' => get_option('p_legal_postcode'));
    $pdf['shop_address_country']  = array('default' => $c_country);
    
    $pdf['shop_address'] = $c_name . "\n" . get_option('p_legal_address') . "\n" . get_option('p_legal_postcode') . " " . get_option('p_legal_city') . "\n" . $country_name;
    $pdf['footer'] = $c_name . " | " . get_option('p_villa_email') . " | " . get_option('p_villa_phone');

    update_option('wpo_wcpdf_settings_general', $pdf);
}

/**
 * ADMIN UI
 */
add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'pukka-settings', 'render_pukka_settings', 'dashicons-admin-generic', 2);
});

function render_pukka_settings() {
    if (isset($_GET['settings-updated'])) purnukka_sync_master_data();
    $wc_countries = WC()->countries->get_countries();
    ?>
    <div class="wrap">
        <h1>Purnukka Stack v0.35</h1>
        <hr>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            <h3>1. Branding (Auto-Sync Logo)</h3>
            <table class="form-table">
                <tr><th>Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Logo Master URL</th><td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text"><br><em>*Koodi lataa kuvan automaattisesti mediakirjastoon, jos se puuttuu.</em></td></tr>
                <tr><th>Colors</th><td>
                    <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>"> Primary
                    <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>"> Dark
                </td></tr>
            </table>
            <h3>2. Communication & Legal</h3>
            <table class="form-table">
                <tr><th>Email / Phone</th><td>
                    <input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>">
                    <input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>">
                </td></tr>
                <tr><th>Company Name / VAT</th><td>
                    <input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text">
                    <input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>">
                </td></tr>
                <tr><th>Street / Zip / City</th><td>
                    <input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>">
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:80px">
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:160px">
                </td></tr>
                <tr><th>Country</th><td>
                    <select name="p_legal_country">
                        <?php foreach($wc_countries as $code => $name): ?>
                            <option value="<?php echo $code; ?>" <?php selected(get_option('p_legal_country', 'FI'), $code); ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td></tr>
            </table>
            <?php submit_button('Save & Force Global Sync'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','purnukka_logo_url','purnukka_primary_color','purnukka_dark_color','p_villa_email','p_smtp_host','p_smtp_user','p_smtp_pass','p_villa_phone','p_company_name','p_business_id','p_legal_address','p_legal_postcode','p_legal_city','p_legal_country','p_maintenance_mode'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

new PurnukkaStackCore();