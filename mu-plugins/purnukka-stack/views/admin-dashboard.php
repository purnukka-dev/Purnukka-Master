<?php
/**
 * View: Admin Dashboard (v1.5 Master)
 * Integrated Logic: Reads/Writes context.json via AJAX.
 */

if (!defined('ABSPATH')) exit;

// --- 1. PHP KONEHUONE (Käsittelee tallennuksen) ---
// Rekisteröidään AJAX-käsittelijä heti tiedoston alussa
add_action('wp_ajax_update_purnukka_feature', function() {
    if (!current_user_can('manage_options')) wp_send_json_error('No permission');
    
    $feature = sanitize_text_field($_POST['feature']);
    $status  = $_POST['status'] === 'true'; // Muunnetaan JS booleaniksi
    
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    
    if (file_exists($config_path)) {
        $config = json_decode(file_get_contents($config_path), true);
        if ($config) {
            $config['features'][$feature] = $status;
            // Kirjoitetaan takaisin tiedostoon nätisti muotoiltuna
            file_put_contents($config_path, json_encode($config, JSON_PRETTY_PRINT));
            wp_send_json_success('JSON updated');
        }
    }
    wp_send_json_error('JSON file not found or invalid');
});

// --- 2. DATAN HAKU NÄKYMÄÄ VARTEN ---
$config = $GLOBALS['purnukka']->config;
$property_name = $config['property_info']['name'] ?? 'Unknown Villa';
$tier = $config['product']['tier'] ?? 'Solo';
$features = $config['features'] ?? [];

$feature_meta = [
    'branding'       => ['icon' => 'dashicons-art', 'desc' => 'Custom logos and brand colors.'],
    'mail-connector' => ['icon' => 'dashicons-email-alt', 'desc' => 'SMTP and automated email routing.'],
    'ai-connector'   => ['icon' => 'dashicons-reddit', 'desc' => 'Digital Host and guest rule engine.'],
    'checkout-logic' => ['icon' => 'dashicons-cart', 'desc' => 'VAT, Stripe, and invoice automation.'],
    'access-control' => ['icon' => 'dashicons-shield', 'desc' => 'Subscription tier and limit enforcement.'],
    'checkin-ui'     => ['icon' => 'dashicons-welcome-learn-more', 'desc' => 'Guest check-in and arrival experience.'],
    'upsell-ui'      => ['icon' => 'dashicons-plus-alt', 'desc' => 'Dynamic cart offers and extra services.'],
];
?>

<div class="wrap purnukka-dashboard">
    <div class="p-header">
        <div class="p-title">
            <h1>Purnukka Stack <span class="v-badge">v1.5</span></h1>
            <p class="p-subtitle"><?php echo esc_html($property_name); ?> &mdash; <span class="tier-tag"><?php echo esc_html($tier); ?> Plan</span></p>
        </div>
        <div class="p-status">
            <span class="status-indicator online"></span> Hub Connected
        </div>
    </div>

    <div class="p-grid">
        <div class="p-card modules-card">
            <h2>Feature Modules</h2>
            <p class="section-desc">Toggle modules ON/OFF. Changes are written directly to <code>context.json</code>.</p>
            
            <div class="module-list">
                <?php foreach ($features as $key => $is_enabled): 
                    $meta = $feature_meta[$key] ?? ['icon' => 'dashicons-yes', 'desc' => 'Active feature module.'];
                ?>
                    <div class="module-item <?php echo $is_enabled ? 'is-active' : 'is-disabled'; ?>" data-id="<?php echo esc_attr($key); ?>">
                        <div class="module-icon">
                            <span class="dashicons <?php echo esc_attr($meta['icon']); ?>"></span>
                        </div>
                        <div class="module-info">
                            <span class="module-name"><?php echo esc_html(ucfirst(str_replace('-', ' ', $key))); ?></span>
                            <span class="module-desc"><?php echo esc_html($meta['desc']); ?></span>
                        </div>
                        <div class="module-toggle">
                            <label class="p-switch">
                                <input type="checkbox" class="feature-toggle-cb" data-feature="<?php echo esc_attr($key); ?>" <?php checked($is_enabled); ?>>
                                <span class="p-slider"></span>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="p-card info-card">
            <h2>System Info</h2>
            <ul class="info-list">
                <li><strong>Config Path:</strong> <code>/purnukka-config/context.json</code></li>
                <li><strong>Control Level:</strong> <span class="tier-tag">Master Override</span></li>
            </ul>
            <div class="p-alert">
                <span class="dashicons dashicons-info"></span>
                Toggling a feature OFF will immediately disable its logic and local UI locks.
            </div>
        </div>
    </div>
</div>

<style>
    .purnukka-dashboard { margin-top: 20px; max-width: 1000px; font-family: sans-serif; }
    .p-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid #dcdcde; padding-bottom: 20px; }
    .p-title h1 { margin: 0; font-size: 28px; }
    .v-badge { font-size: 12px; background: #2271b1; color: #fff; padding: 2px 8px; border-radius: 4px; }
    .tier-tag { color: #b89b5e; font-weight: 600; text-transform: uppercase; font-size: 12px; }
    .status-indicator.online { background: #46b450; width: 10px; height: 10px; display: inline-block; border-radius: 50%; }

    .p-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
    .p-card { background: #fff; border: 1px solid #dcdcde; border-radius: 8px; padding: 20px; }
    
    .module-item { display: flex; align-items: center; padding: 12px; border: 1px solid #f0f0f1; border-radius: 6px; margin-bottom: 10px; transition: 0.3s opacity; }
    .module-item.is-active { border-left: 4px solid #46b450; background: #fafffa; }
    .module-item.is-disabled { opacity: 0.5; background: #f6f7f7; }
    .module-icon { width: 40px; color: #2271b1; }
    .module-info { flex-grow: 1; }
    .module-name { font-weight: 600; display: block; }
    .module-desc { font-size: 12px; color: #646970; }

    /* Modern Toggle Switch */
    .p-switch { position: relative; display: inline-block; width: 40px; height: 20px; }
    .p-switch input { opacity: 0; width: 0; height: 0; }
    .p-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 20px; }
    .p-slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .p-slider { background-color: #46b450; }
    input:checked + .p-slider:before { transform: translateX(20px); }
</style>

<script>
jQuery(document).ready(function($) {
    $('.feature-toggle-cb').on('change', function() {
        const cb = $(this);
        const feature = cb.data('feature');
        const status = cb.is(':checked');
        const row = cb.closest('.module-item');

        row.css('opacity', '0.5');

        $.post(ajaxurl, {
            action: 'update_purnukka_feature',
            feature: feature,
            status: status
        }, function(response) {
            if (response.success) {
                if (status) {
                    row.removeClass('is-disabled').addClass('is-active').css('opacity', '1');
                } else {
                    row.removeClass('is-active').addClass('is-disabled').css('opacity', '0.5');
                }
            } else {
                alert('Error updating context.json');
                cb.prop('checked', !status);
                row.css('opacity', '1');
            }
        });
    });
});
</script>