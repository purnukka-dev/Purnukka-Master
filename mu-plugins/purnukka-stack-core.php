<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.24)
 * Description: SPoT Object Override: Forcing City and Postcode into the plugin's internal "default" arrays.
 * Author: Purnukka Group Oy
 * Version: 0.24
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
        $wp_admin_bar->remove_menu('wp-logo');
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
        echo "<!DOCTYPE html><html style='background:$dark;margin:0;padding:0;overflow:hidden;'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>$name</title><style>
        html,body{background:$dark!important;margin:0;padding:0;height:100vh;width:100vw;display:flex;align-items:center;justify-content:center;color:#fff;font-family:sans-serif;overflow:hidden;}
        .c{text-align:center;animation:f 1.5s;padding:20px;} @keyframes f{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        img{max-height:100px;margin-bottom:25px} h1{color:$primary;font-size:3rem;margin:0} hr{border:0;height:1px;background:linear-gradient(to right,transparent,$primary,transparent);margin:20px auto;width:60%}
        </style></head><body><div class='c'>".($logo?"<img src='".esc_url($logo)."'>":"")."<h1>".esc_html($name)."</h1><hr><p>Opening soon!</p></div></body></html>";
        exit;
    }
}

/**
 * OBJECT-BASED MASTER SYNC
 */
function purnukka_sync_master_data() {
    $pdf = get_option('wpo_wcpdf_settings_general', []);
    
    $c_name  = get_option('p_company_name');
    $c_addr  = get_option('p_legal_address');
    $c_zip   = get_option('p_legal_postcode');
    $c_city  = get_option('p_legal_city');
    $c_phone = get_option('p_villa_phone');
    $c_id    = get_option('p_business_id');

    // 1. Päivitetään suorat tekstikentät
    $pdf['shop_name']           = $c_name;
    $pdf['shop_address_line_1'] = $c_addr;
    $pdf['shop_city']           = $c_city;
    $pdf['shop_postcode']       = $c_zip;
    $pdf['shop_phone']          = $c_phone;
    $pdf['shop_extra_1']        = "Y-tunnus: " . $c_id;

    // 2. Päivitetään objektit (Nämä olivat tyhjiä Debuggerissa)
    $pdf['shop_address_city']     = array('default' => $c_city);
    $pdf['shop_address_postcode'] = array('default' => $c_zip);
    $pdf['shop_phone_number']     = array('default' => $c_phone);
    $pdf['shop_address_country']  = array('default' => 'Finland');
    
    // 3. Täysi osoite yhtenä merkkijonona
    $pdf['shop_address'] = $c_name . "\n" . $c_addr . "\n" . $c_zip . " " . $c_city;

    update_option('wpo_wcpdf_settings_general', $pdf);
}

/**
 * UI & SAVING
 */
add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'purnukka-settings', 'render_purnukka_settings_page', 'dashicons-admin-generic', 2);
});

function render_purnukka_settings_page() {
    if (isset($_GET['settings-updated'])) purnukka_sync_master_data();
    ?>
    <div class="wrap">
        <h1 style="color:#c5a059;">Purnukka Stack v0.24</h1>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            <table class="form-table">
                <tr><th>Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Colors</th><td>
                    <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>"> Primary
                    <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>"> Dark
                </td></tr>
                <tr><th>Company Name</th><td><input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Phone & Business ID</th><td>
                    <input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" placeholder="Phone">
                    <input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" placeholder="VAT ID">
                </td></tr>
                <tr><th>Street Address</th><td><input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" class="regular-text"></td></tr>
                <tr><th>Zip & City</th><td>
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:80px">
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:220px">
                </td></tr>
                <tr><th>Maintenance</th><td><input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?>> Enable Curtain</td></tr>
            </table>
            <?php submit_button('Save & Force Sync Objects'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','purnukka_primary_color','purnukka_dark_color','p_company_name','p_legal_address','p_legal_postcode','p_legal_city','p_villa_phone','p_business_id','p_maintenance_mode'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

new PurnukkaStackCore();