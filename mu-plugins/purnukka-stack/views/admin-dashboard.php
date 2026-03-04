<?php
/**
 * View: Admin Dashboard
 * Clean, modern UI for managing Purnukka Stack modules.
 */

if (!defined('ABSPATH')) exit;

$config = $GLOBALS['purnukka']->config;
$property_name = $config['property_info']['name'] ?? 'Unknown Villa';
$tier = $config['product']['tier'] ?? 'Solo';
$features = $config['features'] ?? [];

// Define icons and descriptions for features
$feature_meta = [
    'branding'       => ['icon' => 'dashicons-art', 'desc' => 'Custom logos and brand colors.'],
    'ai-connector'   => ['icon' => 'dashicons-reddit', 'desc' => 'Digital Host and guest rule engine.'],
    'checkout-logic' => ['icon' => 'dashicons-cart', 'desc' => 'VAT, Stripe, and invoice automation.'],
    'access-control' => ['icon' => 'dashicons-shield', 'desc' => 'Subscription tier and limit enforcement.'],
    'checkin-ui'     => ['icon' => 'dashicons-welcome-learn-more', 'desc' => 'Guest check-in and arrival experience.'],
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
            <p class="section-desc">Modules are controlled via context.json or synchronized from the Hub.</p>
            
            <div class="module-list">
                <?php foreach ($features as $key => $is_enabled): 
                    $meta = $feature_meta[$key] ?? ['icon' => 'dashicons-yes', 'desc' => 'Active feature module.'];
                ?>
                    <div class="module-item <?php echo $is_enabled ? 'is-active' : 'is-disabled'; ?>">
                        <div class="module-icon">
                            <span class="dashicons <?php echo esc_attr($meta['icon']); ?>"></span>
                        </div>
                        <div class="module-info">
                            <span class="module-name"><?php echo esc_html(ucfirst(str_replace('-', ' ', $key))); ?></span>
                            <span class="module-desc"><?php echo esc_html($meta['desc']); ?></span>
                        </div>
                        <div class="module-toggle">
                            <div class="p-switch">
                                <span class="p-slider <?php echo $is_enabled ? 'on' : 'off'; ?>"></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="p-card info-card">
            <h2>System Info</h2>
            <ul class="info-list">
                <li><strong>Environment:</strong> <?php echo (defined('WP_DEBUG') && WP_DEBUG) ? 'Development' : 'Production'; ?></li>
                <li><strong>Config Path:</strong> <code>/purnukka-config/context.json</code></li>
                <li><strong>API Endpoint:</strong> <code>/wp-json/purnukka/v1/sync</code></li>
            </ul>
            <div class="p-alert">
                <span class="dashicons dashicons-info"></span>
                Settings are read-only here. Use the <strong>Purnukka Hub</strong> to push updates.
            </div>
        </div>
    </div>
</div>

<style>
    .purnukka-dashboard { margin-top: 20px; max-width: 1000px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
    .p-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid #dcdcde; padding-bottom: 20px; }
    .p-title h1 { margin: 0; font-size: 28px; font-weight: 700; color: #1d2327; }
    .v-badge { font-size: 12px; background: #2271b1; color: #fff; padding: 2px 8px; border-radius: 4px; vertical-align: middle; }
    .p-subtitle { margin: 5px 0 0; color: #646970; font-size: 16px; }
    .tier-tag { color: #b89b5e; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .p-status { font-weight: 500; color: #1d2327; display: flex; align-items: center; }
    .status-indicator { width: 10px; height: 10px; border-radius: 50%; margin-right: 8px; }
    .status-indicator.online { background: #46b450; box-shadow: 0 0 5px rgba(70, 180, 80, 0.5); }

    .p-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
    .p-card { background: #fff; border: 1px solid #dcdcde; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .p-card h2 { margin-top: 0; font-size: 18px; }
    .section-desc { color: #646970; margin-bottom: 25px; }

    .module-list { display: flex; flex-direction: column; gap: 15px; }
    .module-item { display: flex; align-items: center; padding: 12px; border: 1px solid #f0f0f1; border-radius: 6px; transition: all 0.2s; }
    .module-item.is-active { border-left: 4px solid #46b450; background: #fafffa; }
    .module-item.is-disabled { opacity: 0.6; background: #f6f7f7; }
    
    .module-icon { width: 40px; color: #2271b1; }
    .module-info { flex-grow: 1; }
    .module-name { display: block; font-weight: 600; font-size: 14px; color: #1d2327; }
    .module-desc { font-size: 12px; color: #646970; }

    .p-switch { width: 36px; height: 18px; background: #dcdcde; border-radius: 10px; position: relative; }
    .p-slider { position: absolute; width: 14px; height: 14px; background: #fff; border-radius: 50%; top: 2px; transition: 0.2s; }
    .p-slider.on { right: 2px; background: #46b450; }
    .p-slider.off { left: 2px; background: #a7aaad; }

    .info-list { list-style: none; padding: 0; font-size: 13px; }
    .info-list li { margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f1; }
    .info-list code { background: #f6f7f7; padding: 2px 4px; border-radius: 3px; }
    .p-alert { background: #f0f6fb; border-left: 4px solid #2271b1; padding: 12px; font-size: 12px; color: #2c3338; display: flex; align-items: center; }
    .p-alert .dashicons { margin-right: 8px; color: #2271b1; }
</style>