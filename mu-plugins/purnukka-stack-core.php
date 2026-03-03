<?php
/**
 * Plugin Name: Purnukka Stack - Master Control (v1.1)
 * Description: Ultimate Hybrid: Restoration of v0.9 (VAT & Reset) + v0.39 (Nuclear PDF & Logo Sync).
 * Author: Purnukka Group Oy
 * Version: 1.1
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
        }
        
        // NUCLEAR PDF INJECTION (Logo, Colors & Address)
        add_filter('option_wpo_wcpdf_settings_general', [$this, 'force_branding_into_pdf'], 999);
    }

    public function inject_brand_styles() {
        $c1 = get_option('purnukka_primary_color', '#c5a059');
        $c2 = get_option('purnukka_secondary_color', '#a3844a');
        $c3 = get_option('purnukka_accent_color', '#f1c40f');
        $c4 = get_option('purnukka_dark_color', '#1a1a1a');
        echo "<style>
            :root { --p-primary: $c1; --p-secondary: $c2; --p-accent: $c3; --p-dark: $c4; }
            .button, button, input[type='submit'], .mphb-book-button { background-color: var(--p-primary) !important; border-color: var(--p-primary) !important; color: #fff !important; transition: 0.3s; }
            .button:hover, button:hover, input[type='submit']:hover { background-color: var(--p-secondary) !important; border-color: var(--p-secondary) !important; }
            a { color: var(--p-accent); }
            h1, h2, h3, h4 { color: var(--p-dark) !important; }
        </style>";
    }

    public function force_branding_into_pdf($settings) {
        $logo_url = get_option('purnukka_logo_url');
        $logo_id = attachment_url_to_postid($logo_url);
        if ($logo_id) {
            if (!is_array($settings)) $settings = [];
            $settings['header_logo'] = $logo_id;
            if (empty($settings['header_logo_height'])) $settings['header_logo_height'] = '35';
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
        echo "<!DOCTYPE html><html style='background:$dark;width:100%;height:100%;'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>$name</title><style>
        html,body{background:$dark!important;margin:0;padding:0;height:100vh;width:100vw;display:flex;align-items:center;justify-content:center;color:#fff;font-family:sans-serif;overflow:hidden;}
        .c{text-align:center;animation:f 1.5s;padding:20px;} @keyframes f{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        img{max-height:120px;margin-bottom:30px} h1{color:$primary;font-size:3.5rem;margin:0;font-weight:700;} hr{border:0;height:1px;background:linear-gradient(to right,transparent,$primary,transparent);margin:25px auto;width:60%}
        </style></head><body><div class='c'>".($logo?"<img src='".esc_url($logo)."'>":"")."<h1>".esc_html($name)."</h1><hr><p>Currently under construction.<br>Opening soon!</p></div></body></html>";
        exit;
    }
}

/**
 * MASTER DATA SYNC
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

    // 2. PDF Sync
    $pdf = get_option('wpo_wcpdf_settings_general', []);
    $c_code = get_option('p_legal_country', 'FI');
    $pdf['shop_name'] = get_option('p_company_name');
    $pdf['shop_address_line_1'] = get_option('p_legal_address');
    $pdf['shop_city'] = get_option('p_legal_city');
    $pdf['shop_postcode'] = get_option('p_legal_postcode');
    $pdf['shop_country'] = $c_code;
    $pdf['shop_phone'] = get_option('p_villa_phone');
    
    // Objekti-muodot (Debugger-korjaus)
    $pdf['shop_address_city'] = array('default' => get_option('p_legal_city'));
    $pdf['shop_address_postcode'] = array('default' => get_option('p_legal_postcode'));
    $pdf['shop_address_country'] = array('default' => $c_code);

    update_option('wpo_wcpdf_settings_general', $pdf);
}

/**
 * ADMIN UI
 */
add_action('admin_menu', function() {
    add_menu_page('Purnukka Settings', 'Purnukka Stack', 'manage_options', 'purnukka-settings', 'render_pukka_settings', 'dashicons-admin-generic', 2);
});

function render_pukka_settings() {
    if (isset($_GET['settings-updated'])) purnukka_sync_master_data();
    $wc_countries = WC()->countries->get_countries();
    ?>
    <div class="wrap">
        <h1 style="color: #c5a059; font-weight: bold;">Purnukka Stack – Property Configuration v1.1</h1>
        <hr>
        <form method="post" action="options.php" id="purnukka-settings-form">
            <?php settings_fields('purnukka-settings-group'); ?>
            
            <h2>1. Property Identity & Branding</h2>
            <table class="form-table">
                <tr><th>Master Villa Name</th><td><input type="text" name="p_villa_name" value="<?php echo esc_attr(get_option('p_villa_name', 'Villa Purnukka')); ?>" class="regular-text" /></td></tr>
                <tr><th>Master Villa Tagline</th><td><input type="text" name="p_villa_tagline" value="<?php echo esc_attr(get_option('p_villa_tagline')); ?>" class="regular-text" /></td></tr>
                <tr><th>Logo Image URL</th><td><input type="text" name="purnukka_logo_url" value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" class="regular-text" /></td></tr>
                <tr><th>Brand Colors</th><td>
                    <input type="color" id="color_primary" name="purnukka_primary_color" value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>" /> Primary &nbsp;
                    <input type="color" id="color_secondary" name="purnukka_secondary_color" value="<?php echo esc_attr(get_option('purnukka_secondary_color', '#a3844a')); ?>" /> Hover &nbsp;
                    <input type="color" id="color_accent" name="purnukka_accent_color" value="<?php echo esc_attr(get_option('purnukka_accent_color', '#f1c40f')); ?>" /> Accent &nbsp;
                    <input type="color" id="color_dark" name="purnukka_dark_color" value="<?php echo esc_attr(get_option('purnukka_dark_color', '#1a1a1a')); ?>" /> Dark
                </td></tr>
            </table>

            <h2>2. Communication & SMTP</h2>
            <table class="form-table">
                <tr><th>Public Email / Phone</th><td>
                    <input type="email" name="p_villa_email" value="<?php echo esc_attr(get_option('p_villa_email')); ?>" placeholder="Email">
                    <input type="text" name="p_villa_phone" value="<?php echo esc_attr(get_option('p_villa_phone')); ?>" placeholder="Phone">
                </td></tr>
                <tr><th>SMTP Master Config</th><td>
                    <input type="text" name="p_smtp_host" value="<?php echo esc_attr(get_option('p_smtp_host')); ?>" placeholder="Host" style="width:140px">
                    <input type="text" name="p_smtp_user" value="<?php echo esc_attr(get_option('p_smtp_user')); ?>" placeholder="User" style="width:140px">
                    <input type="password" name="p_smtp_pass" value="<?php echo esc_attr(get_option('p_smtp_pass')); ?>" placeholder="Pass" style="width:140px">
                </td></tr>
            </table>

            <h2>3. Legal & World SPoT</h2>
            <table class="form-table">
                <tr><th>Company Name / VAT ID</th><td>
                    <input type="text" name="p_company_name" value="<?php echo esc_attr(get_option('p_company_name')); ?>" class="regular-text">
                    <input type="text" name="p_business_id" value="<?php echo esc_attr(get_option('p_business_id')); ?>" placeholder="Y-tunnus">
                </td></tr>
                <tr><th>VAT Rate %</th><td><input type="number" name="p_vat_rate" value="<?php echo esc_attr(get_option('p_vat_rate', '10')); ?>" class="small-text" /></td></tr>
                <tr><th>Street / Zip / City</th><td>
                    <input type="text" name="p_legal_address" value="<?php echo esc_attr(get_option('p_legal_address')); ?>" placeholder="Street">
                    <input type="text" name="p_legal_postcode" value="<?php echo esc_attr(get_option('p_legal_postcode')); ?>" style="width:80px" placeholder="Zip">
                    <input type="text" name="p_legal_city" value="<?php echo esc_attr(get_option('p_legal_city')); ?>" style="width:160px" placeholder="City">
                </td></tr>
                <tr><th>Country</th><td>
                    <select name="p_legal_country" style="width:250px;">
                        <?php foreach($wc_countries as $code => $name): ?>
                            <option value="<?php echo $code; ?>" <?php selected(get_option('p_legal_country', 'FI'), $code); ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td></tr>
                <tr><th>Maintenance / Map</th><td>
                    <input type="checkbox" name="p_maintenance_mode" value="on" <?php checked(get_option('p_maintenance_mode'), 'on'); ?>> Enable Curtain &nbsp;
                    <input type="text" name="p_latitude" value="<?php echo esc_attr(get_option('p_latitude')); ?>" placeholder="Lat" style="width:80px">
                    <input type="text" name="p_longitude" value="<?php echo esc_attr(get_option('p_longitude')); ?>" placeholder="Long" style="width:80px">
                </td></tr>
            </table>

            <div style="margin-top: 20px;">
                <?php submit_button('Save & Sync Global Data', 'primary', 'submit', false); ?>
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
            alert('Värit palautettu käyttöliittymään. Muista painaa "Save & Sync" tallentaaksesi ne tietokantaan.');
        }
    });
    </script>
    <?php
}

add_action('admin_init', function() {
    $s = ['p_villa_name','p_villa_tagline','purnukka_logo_url','purnukka_primary_color','purnukka_secondary_color','purnukka_accent_color','purnukka_dark_color','p_villa_email','p_smtp_host','p_smtp_user','p_smtp_pass','p_villa_phone','p_company_name','p_business_id','p_vat_rate','p_legal_address','p_legal_postcode','p_legal_city','p_legal_country','p_latitude','p_longitude','p_maintenance_mode'];
    foreach($s as $o) register_setting('purnukka-settings-group', $o);
});

new PurnukkaStackCore();