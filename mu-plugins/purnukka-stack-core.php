<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.18)
 * Description: Restoration & Deep SPoT Fix: 4-Color System, Individual PDF Field Sync, and Full-Screen Curtain.
 * Author: Purnukka Group Oy
 * Version: 0.18
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
        img{max-height:120px;margin-bottom:30px} h1{color:$primary;font-size:3.5rem;margin:0;font-weight:700;} hr{border:0;height:1px;background:linear-gradient(to right,transparent,$primary,transparent);margin:25px auto;width:60%}
        p{font-size:1.2rem;opacity:0.8;line-height:1.5;}
        </style></head><body><div class='c'>".($logo?"<img src='".esc_url($logo)."'>":"")."<h1>".esc_html($name)."</h1><hr><p>Our site is currently under construction.<br>We will be opening soon!</p></div></body></html>";
        exit;
    }
}

/**
 * DEEP SYNC ENGINE - TARGETING INDIVIDUAL FIELDS
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

    // 2. PDF Invoice Deep Sync (Populating Individual Settings Fields)
    $pdf_gen = get_option('wpo_wcpdf_settings_general', []);
    
    // Core Mapping
    $pdf_gen['shop_name']     = get_option('p_company_name');
    $pdf_gen['shop_address']  = get_option('p_legal_address') . "\n" . get_option('p_legal_postcode') . " " . get_option('p_legal_city');
    
    // Mapping Individual Lines to avoid MASTER- placeholders
    $pdf_gen['shop_address_line_1'] = get_option('p_legal_address');
    $pdf_gen['shop_city']           = get_option('p_legal_city');
    $pdf_gen['shop_postcode']       = get_option('p_legal_postcode');
    $pdf_gen['shop_phone']          = get_option('p_villa_phone');
    
    // Mapping Custom Fields (Y-tunnus)
    $pdf_gen['shop_extra_1'] = "Business ID: " . get_option('p_business_id');

    update_option('wpo_wcpdf_settings_general', $pdf_gen);
}

/**
 * ADMIN UI - RESTORED FULL PANEL
 */
add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'purnukka-settings', 'render_purnukka_settings_page', 'dashicons-admin-generic', 2);
});

function render_purnukka_settings_page() {
    if (isset($_GET['settings-updated'])) purnukka_sync_master_data();
    ?>
    <div class="wrap">
        <h1 style="color:#c5a059;">Purnukka Stack v0.18</h1>
        <hr>
        <form method="post" action="options.php">
            <?php settings_fields('purnukka-settings-group'); ?>
            
            <h3>1. Branding & 4-Color System</h3>
            <table class="form-table">
                <tr><th>Villa Name & Tagline</th><td>
                    <input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name')); ?>" placeholder="Villa Name" class="regular-text">
                    <input type="text" name="p_villa_tagline" value="<?php echo esc_attr(get_option('p_villa_tagline')); ?>" placeholder="Tagline" class="regular-text">
                </td></tr>
                <tr><th>Logo & Colors</th><td>
                    <input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" placeholder="Logo URL" class="regular-text"><br><br>
                    <input type="color" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>"> Primary
                    <input type="color" name="purnukka_secondary_color" value="<?php echo esc_attr(get_option('purnukka_secondary_color', '#a3844a')); ?>"> Hover
                    <input type="color" name="purnukka_accent_color" value="<?php echo esc_attr(get_option('purnukka_accent_color', '#f1c40f')); ?>"> Accent
                    <input type="color" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>"> Dark
                </td></tr>
            </table>

            <h3>2. Communication & SMTP</h3>
            <table class="form-table">
                <tr><th>Public Email</th><td><input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" class="regular-text"></td></tr>
                <tr><th>SMTP Host / User / Pass</th><td>
                    <input type="text" name="p_smtp_host" value="<?php echo esc_attr(get_option('p_smtp_host')); ?>" placeholder="Host" style="width:140px">
                    <input type="text" name="p_smtp_user" value="<?php echo esc_attr(get_option('p_smtp_user')); ?>" placeholder="User" style="width:140px">
                    <input type="password" name="p_smtp_pass" value="<?php echo esc_attr(get_option('p_smtp_pass')); ?>" placeholder="Password" style="width:140px">
                </td></tr>
                <tr><th>Phone Number</th><td><input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" class="regular-text"></td></tr>
            </table>

            <h3>3. Legal & PDF SPoT</h3>
            <table class="form-table">
                <tr><th>Company Name</th><td><input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text"></td></tr>
                <tr><th>Business ID (Y-tunnus)</th><td><input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" class="regular-text"></td></tr>
                <tr><th>Street Address</th><td><input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" class="regular-text"></td></tr>
                <tr><th>Zip & City</th><td>
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:80px">
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:220px">
                </td></tr>
            </table>

            <h3>4. Maps & Maintenance</h3>
            <table class="form-table">
                <tr><th>Lat / Long</th><td>
                    <input type="text" name="p_latitude" value="<?php echo esc_attr(get_option('p_latitude')); ?>" placeholder="Lat" style="width:150px">
                    <input type="text" name="p_longitude" value="<?php echo esc_attr(get_option('p_longitude')); ?>" placeholder="Long" style="width:150px">
                </td></tr>
                <tr><th>Maintenance Mode</th><td>
                    <input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?>> Enable Curtain
                </td></tr>
            </table>
            
            <?php submit_button('Save & Deep Sync All Master Data'); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','p_villa_tagline','purnukka_logo_url','purnukka_primary_color','purnukka_secondary_color','purnukka_accent_color','purnukka_dark_color','p_villa_email','p_smtp_host','p_smtp_user','p_smtp_pass','p_villa_phone','p_company_name','p_business_id','p_legal_address','p_legal_postcode','p_legal_city','p_latitude','p_longitude','p_maintenance_mode'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

new PurnukkaStackCore();