<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.37)
 * Description: Absolute Logo Force: Injecting ID 317 into every possible PDF header key.
 * Author: Purnukka Group Oy
 * Version: 0.37
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
        </style></head><body><div class='c'>".($logo?"<img src='".esc_url($logo)."'>":"")."<h1>".esc_html($name)."</h1><hr><p>Opening soon!</p></div></body></html>";
        exit;
    }
}

/**
 * ABSOLUTE FORCE ENGINE
 */
function purnukka_sync_master_data() {
    $pdf = get_option('wpo_wcpdf_settings_general', []);
    $logo_url = get_option('purnukka_logo_url');
    $logo_id = attachment_url_to_postid($logo_url);

    // Pakotetaan logo kaikkiin mahdollisiin PDF-pluginin avaimiin
    if ($logo_id) {
        // 1. Perinteinen ID-kenttä
        $pdf['header_logo'] = $logo_id; 
        
        // 2. Objektimuotoinen kenttä (Debugger-löydöksen perusteella)
        $pdf['header_logo_obj'] = array('default' => $logo_id); 
        
        // 3. Lisäavaimet joita jotkut versiot käyttävät
        $pdf['header_logo_id'] = $logo_id;
    }

    // Osoitetietojen synkronointi (varmistetaan objektimuodot)
    $c_country = get_option('p_legal_country', 'FI');
    $pdf['shop_name'] = get_option('p_company_name');
    $pdf['shop_address_line_1'] = get_option('p_legal_address');
    $pdf['shop_city'] = get_option('p_legal_city');
    $pdf['shop_postcode'] = get_option('p_legal_postcode');
    $pdf['shop_country'] = $c_country;
    
    // Pakotetaan kaupunki ja maa myös objekteina
    $pdf['shop_address_city'] = array('default' => get_option('p_legal_city'));
    $pdf['shop_address_country'] = array('default' => $c_country);

    update_option('wpo_wcpdf_settings_general', $pdf);
    return $logo_id;
}

/**
 * ADMIN UI
 */
add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'pukka-settings', 'render_pukka_settings', 'dashicons-admin-generic', 2);
});

function render_pukka_settings() {
    $status_msg = "";
    if (isset($_GET['settings-updated'])) {
        $id = purnukka_sync_master_data();
        if ($id) {
            $status_msg = '<div class="notice notice-success is-dismissible"><p>✅ Logo (ID: '.$id.') pakotettu kaikkiin PDF-asetuksiin!</p></div>';
        }
    }
    
    $wc_countries = WC()->countries->get_countries();
    ?>
    <div class="wrap">
        <h1>Purnukka Stack v0.37</h1>
        <?php echo $status_msg; ?>
        <hr>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            <table class="form-table">
                <tr><th>Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Logo URL</th><td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text"></td></tr>
                <tr><th>Y-tunnus</th><td><input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>"></td></tr>
            </table>
            <?php submit_button('Save & Force Absolute Sync'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','purnukka_logo_url','p_business_id','p_legal_city','p_legal_country','p_legal_address','p_legal_postcode','p_villa_phone','p_company_name'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

new PurnukkaStackCore();