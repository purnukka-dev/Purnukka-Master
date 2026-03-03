<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.223)
 * Description: Debugger Edition: Added SPoT Debugger to identify hidden PDF database keys.
 * Author: Purnukka Group Oy
 * Version: 0.22
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
 * MASTER SYNC
 */
function purnukka_sync_master_data() {
    $pdf = get_option('wpo_wcpdf_settings_general', []);
    $pdf['shop_name'] = get_option('p_company_name');
    $pdf['shop_address_line_1'] = get_option('p_legal_address');
    $pdf['shop_city'] = get_option('p_legal_city');
    $pdf['shop_postcode'] = get_option('p_legal_postcode');
    $pdf['shop_phone'] = get_option('p_villa_phone');
    update_option('wpo_wcpdf_settings_general', $pdf);
}

/**
 * ADMIN UI
 */
add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'purnukka-settings', 'render_purnukka_settings_page', 'dashicons-admin-generic', 2);
});

function render_purnukka_settings_page() {
    if (isset($_GET['settings-updated'])) purnukka_sync_master_data();
    ?>
    <div class="wrap">
        <h1 style="color:#c5a059;">Purnukka Stack v0.22</h1>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            <table class="form-table">
                <tr><th>Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Colors</th><td>
                    <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>"> Primary
                    <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>"> Dark
                </td></tr>
                <tr><th>Company Name</th><td><input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Street Address</th><td><input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" class="regular-text"></td></tr>
                <tr><th>Zip & City</th><td>
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:80px">
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:220px">
                </td></tr>
                <tr><th>Maintenance</th><td><input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?>> Enable</td></tr>
            </table>
            <?php submit_button('Save & Sync'); ?>
        </form>

        <hr style="margin-top:50px;">
        <div style="background:#f0f0f1; padding:20px; border:1px solid #ccc;">
            <h3>🔍 SPoT Debugger – PDF Plugin State</h3>
            <p>Tässä on PDF-lasku-pluginin tämänhetkiset arvot suoraan tietokannasta:</p>
            <table class="widefat fixed" style="width:100%;">
                <thead><tr><th>Avain (Key)</th><th>Arvo (Value)</th></tr></thead>
                <tbody>
                    <?php 
                    $debug_pdf = get_option('wpo_wcpdf_settings_general', []);
                    if (!empty($debug_pdf)) {
                        foreach ($debug_pdf as $key => $val) {
                            $display_val = is_array($val) ? json_encode($val) : $val;
                            if (empty($display_val)) $display_val = '<span style="color:red;">[TYHJÄ]</span>';
                            echo "<tr><td><strong>" . esc_html($key) . "</strong></td><td>" . esc_html($display_val) . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>Tietoja ei löytynyt. Asetuksia ei ole vielä tallennettu.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','purnukka_primary_color','purnukka_dark_color','p_company_name','p_legal_address','p_legal_postcode','p_legal_city','p_maintenance_mode'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

new PurnukkaStackCore();