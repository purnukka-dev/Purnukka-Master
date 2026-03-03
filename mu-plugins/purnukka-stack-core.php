<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.15)
 * Description: Deep SPoT Sync: Physical override for PDF Invoices, SMTP, and Full-Screen Dark Curtain.
 * Author: Purnukka Group Oy
 * Version: 0.15
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
        echo "<!DOCTYPE html><html style='background:$dark;'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>$name</title><style>
        html,body{background:$dark!important;margin:0;padding:0;height:100vh;width:100vw;display:flex;align-items:center;justify-content:center;color:#fff;font-family:sans-serif;overflow:hidden;}
        .c{text-align:center;animation:f 1.5s;} @keyframes f{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:0}}
        img{max-height:100px;margin-bottom:20px} h1{color:$primary;font-size:3rem;margin:0} hr{border:0;height:1px;background:linear-gradient(to right,transparent,$primary,transparent);margin:20px auto;width:60%}
        </style></head><body><div class='c'>".($logo?"<img src='$logo'>":"")."<h1>$name</h1><hr><p>Currently under construction.<br>Opening soon!</p></div></body></html>";
        exit;
    }
}

/**
 * MASTER SYNC ENGINE
 */
function purnukka_sync_master_data() {
    // 1. SMTP Sync
    $smtp = get_option('wp_mail_smtp', []);
    $smtp['mail']['from_email'] = get_option('p_villa_email');
    $smtp['mail']['from_name']  = get_option('p_villa_name');
    $smtp['smtp']['host'] = get_option('p_smtp_host');
    $smtp['smtp']['user'] = get_option('p_smtp_user');
    $smtp['smtp']['pass'] = get_option('p_smtp_pass');
    update_option('wp_mail_smtp', $smtp);

    // 2. DEEP PDF INVOICE SYNC (Ylikirjoittaa jokaisen kentän erikseen)
    $pdf_gen = get_option('wpo_wcpdf_settings_general', []);
    
    // Nämä ovat ne kentät, jotka laskussa yleensä kummittelevat
    $pdf_gen['shop_name']    = get_option('p_company_name');
    $pdf_gen['shop_address'] = get_option('p_legal_address') . "\n" . get_option('p_legal_postcode') . " " . get_option('p_legal_city');
    
    // Pakotetaan myös erilliset "Extra" kentät jos template käyttää niitä
    $pdf_gen['footer'] = get_option('p_company_name') . " | Business ID: " . get_option('p_business_id');
    
    // Tallennetaan pääasetukset
    update_option('wpo_wcpdf_settings_general', $pdf_gen);
    
    // Jos plugin käyttää "Address" tai "Footer" -kohtaan omia muuttujiaan
    update_option('wpo_wcpdf_shop_name', get_option('p_company_name'));
    update_option('wpo_wcpdf_shop_address', $pdf_gen['shop_address'] . "\n" . get_option('p_villa_phone'));
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
        <h1>Purnukka Stack v0.15</h1>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            <table class="form-table">
                <tr><th>Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Logo & Colors</th><td>
                    <input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" placeholder="Logo URL" class="regular-text"><br><br>
                    <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>">
                    <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>">
                </td></tr>
                <tr><th>Company Name</th><td><input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Street Address</th><td><input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" class="regular-text"></td></tr>
                <tr><th>Zip & City</th><td>
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:70px">
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:220px">
                </td></tr>
                <tr><th>Phone & Business ID</th><td>
                    <input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" placeholder="Phone">
                    <input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" placeholder="Y-tunnus">
                </td></tr>
                <tr><th>Maintenance Mode</th><td>
                    <input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?>> Enable Curtain
                </td></tr>
            </table>
            <?php submit_button('Save & Deep Sync'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','purnukka_logo_url','purnukka_primary_color','purnukka_dark_color','p_company_name','p_legal_address','p_legal_postcode','p_legal_city','p_villa_phone','p_business_id','p_maintenance_mode','p_villa_email','p_smtp_host','p_smtp_user','p_smtp_pass'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

new PurnukkaStackCore();