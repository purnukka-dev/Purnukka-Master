<?php
/**
 * View: Admin Dashboard (v2.1.0 Master)
 * Description: Dynaaminen ohjauspaneeli moduulien hallinnalla ja kohdelistauksella.
 * Integrated Logic: Reads/Writes context.json via AJAX.
 * Standards: Full code preserved, no features removed.
 */

if (!defined('ABSPATH')) exit;

// --- 1. DATAN HAKU NÄKYMÄÄ VARTEN ---
$core = $GLOBALS['purnukka'];
$config = $core->config;
$property_name = $config['property_info']['name'] ?? 'Purnukka Property';
$tier = $config['tier'] ?? 'Starter';
$features = $config['features'] ?? [];

// Haetaan dynaamisesti luodut villat listaukseen (Uusi dynaaminen logiikka)
$villas = get_posts([
    'post_type' => 'villa',
    'posts_per_page' => -1,
    'post_status' => 'publish'
]);

$feature_meta = [
    'branding'       => ['icon' => 'dashicons-art', 'desc' => 'Custom logos and brand colors.'],
    'mail-connector' => ['icon' => 'dashicons-email-alt', 'desc' => 'SMTP and automated email routing.'],
    'ai-connector'   => ['icon' => 'dashicons-reddit', 'desc' => 'Digital Host and guest rule engine.'],
    'checkout-logic' => ['icon' => 'dashicons-cart', 'desc' => 'VAT, Stripe, and invoice automation.'],
    'access-control' => ['icon' => 'dashicons-shield', 'desc' => 'Subscription tier and limit enforcement.'],
    'checkin-ui'     => ['icon' => 'dashicons-welcome-learn-more', 'desc' => 'Guest check-in and arrival experience.'],
    'upsell-ui'      => ['icon' => 'dashicons-plus-alt', 'desc' => 'Dynamic cart offers and extra services.'],
    'hub-sync'       => ['icon' => 'dashicons-update', 'desc' => 'Real-time synchronization with Purnukka Hub.'],
    'tier-manager'   => ['icon' => 'dashicons-awards', 'desc' => 'License and feature management.'],
];
?>

<div class="wrap purnukka-dashboard">
    <div class="p-header">
        <div class="p-title">
            <h1>Purnukka Stack <span class="v-badge">v2.1</span></h1>
            <p class="p-subtitle"><?php echo esc_html($property_name); ?> &mdash; <span class="tier-tag"><?php echo esc_html($tier); ?> Plan</span></p>
        </div>
        <div class="p-status">
            <span class="status-indicator online"></span> Hub Connected
        </div>
    </div>

    <div class="p-grid">
        <div class="p-card">
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
                            <span class="module-name"><?php echo esc_html(ucwords(str_replace('-', ' ', $key))); ?></span>
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

        <div class="p-side-column">
            <div class="p-card info-card" style="margin-bottom: 20px;">
                <h2>Aktiiviset Villat (<?php echo count($villas); ?>)</h2>
                <table class="wp-list-table widefat fixed striped" style="border: none; box-shadow: none;">
                    <thead>
                        <tr>
                            <th>Kohde</th>
                            <th>ID</th>
                            <th>Hinta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($villas) : foreach ($villas as $villa) : 
                            $product_id = get_post_meta($villa->ID, '_linked_product_id', true);
                            $price = $product_id ? get_post_meta($product_id, '_regular_price', true) : '-';
                        ?>
                            <tr>
                                <td><strong><?php echo esc_html($villa->post_title); ?></strong></td>
                                <td><code>#<?php echo esc_html($product_id ?: '?'); ?></code></td>
                                <td><?php echo esc_html($price); ?> €</td>
                            </tr>
                        <?php endforeach; else : ?>
                            <tr><td colspan="3">Ei aktiivisia kohteita.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-card info-card">
                <h2>System Status</h2>
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
</div>

<style>
    .purnukka-dashboard { margin-top: 20px; max-width: 1200px; font-family: sans-serif; padding-right: 20px; }
    .p-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid #dcdcde; padding-bottom: 20px; }
    .p-title h1 { margin: 0; font-size: 28px; font-weight: 700; color: #1d2327; }
    .v-badge { font-size: 12px; background: #2271b1; color: #fff; padding: 2px 8px; border-radius: 4px; position: relative; top: -5px; }
    .p-subtitle { margin: 5px 0 0; color: #646970; }
    .tier-tag { color: #b89b5e; font-weight: 700; text-transform: uppercase; font-size: 12px; }
    .status-indicator.online { background: #46b450; width: 10px; height: 10px; display: inline-block; border-radius: 50%; margin-right: 5px; }

    .p-grid { display: grid; grid-template-columns: 1fr 350px; gap: 20px; align-items: start; }
    .p-card { background: #fff; border: 1px solid #dcdcde; border-radius: 12px; padding: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .p-card h2 { margin-top: 0; font-size: 18px; color: #1d2327; }
    .section-desc { color: #646970; font-size: 13px; margin-bottom: 20px; }
    
    .module-item { display: flex; align-items: center; padding: 15px; border: 1px solid #f0f0f1; border-radius: 8px; margin-bottom: 12px; transition: all 0.2s ease; }
    .module-item.is-active { border-left: 5px solid #46b450; background: #fafffa; }
    .module-item.is-disabled { opacity: 0.6; background: #f6f7f7; }
    .module-icon { width: 45px; color: #2271b1; }
    .module-icon .dashicons { font-size: 24px; }
    .module-info { flex-grow: 1; }
    .module-name { font-weight: 600; display: block; font-size: 15px; color: #1d2327; }
    .module-desc { font-size: 12px; color: #646970; }

    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-list li { padding: 10px 0; border-bottom: 1px solid #f0f0f1; font-size: 13px; }
    .p-alert { background: #f0f6fb; padding: 12px; border-radius: 6px; font-size: 12px; color: #0073aa; margin-top: 20px; display: flex; align-items: center; }
    .p-alert .dashicons { margin-right: 8px; }

    /* Modern Toggle Switch */
    .p-switch { position: relative; display: inline-block; width: 44px; height: 22px; }
    .p-switch input { opacity: 0; width: 0; height: 0; }
    .p-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccd0d4; transition: .3s; border-radius: 22px; }
    .p-slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; }
    input:checked + .p-slider { background-color: #46b450; }
    input:checked + .p-slider:before { transform: translateX(22px); }
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
                    row.removeClass('is-active').addClass('is-disabled').css('opacity', '0.6');
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