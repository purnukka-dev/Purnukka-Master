<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka Tier Manager - TÄYSI VERSIO (EI KARSINTAA).
 * Hallitsee valikot, dashboardin, liukukytkimet ja rajoitukset.
 */
class Purnukka_Tier_Manager {
    private $core;
    private $config;

    public function __construct($core) {
        $this->core = $core;
        // Synkataan config-data coresta
        $this->config = $this->core->config;

        add_action('admin_menu', [$this, 'register_purnukka_menu']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_dashboard_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_dashboard_assets']);
        
        // AJAX-käsittelijä kytkimille
        add_action('wp_ajax_update_purnukka_feature', [$this, 'handle_feature_switch']);
    }

    public function register_purnukka_menu() {
        add_menu_page(
            'Purnukka Stack',
            'Purnukka',
            'manage_options',
            'purnukka-stack',
            [$this, 'render_dashboard'],
            'dashicons-admin-home',
            2
        );

        add_submenu_page(
            'purnukka-stack',
            'Lisenssi',
            'Taso: ' . esc_html($this->config['product']['tier'] ?? 'Solo'),
            'manage_options',
            'purnukka-tier',
            [$this, 'render_tier_info']
        );
    }

    public function render_dashboard() {
        // TÄRKEÄÄ: Viedään config-muuttuja näkymälle
        $config = $this->core->config;
        $view_path = PURNUKKA_STACK_PATH . 'views/admin-dashboard.php';
        
        echo '<div class="wrap purnukka-main-wrapper">';
        if (file_exists($view_path)) {
            include $view_path;
        } else {
            echo '<h1>Purnukka Stack</h1><p>Dashboard-näkymä kadonnut! Tarkista: ' . esc_html($view_path) . '</p>';
        }
        echo '</div>';

        // Palautetaan ne kytkimien vaatimat skriptit, jotka "katosivat"
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.purnukka-switch input').on('change', function() {
                const feature = $(this).data('feature');
                const isEnabled = $(this).is(':checked');
                
                $.post(ajaxurl, {
                    action: 'update_purnukka_feature',
                    feature: feature,
                    enabled: isEnabled,
                    nonce: '<?php echo wp_create_nonce("purnukka_feature_nonce"); ?>'
                }, function(response) {
                    if(response.success) {
                        console.log('Feature updated');
                    }
                });
            });
        });
        </script>
        <style>
            .purnukka-switch { position: relative; display: inline-block; width: 60px; height: 34px; }
            .purnukka-switch input { opacity: 0; width: 0; height: 0; }
            .purnukka-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
            .purnukka-slider:before { position: absolute; content: ""; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
            input:checked + .purnukka-slider { background-color: #b89b5e; }
            input:checked + .purnukka-slider:before { transform: translateX(26px); }
        </style>
        <?php
    }

    public function handle_feature_switch() {
        check_ajax_referer('purnukka_feature_nonce', 'nonce');
        // Tähän tulee myöhemmin se Hub-yhteys, mutta pidetään logiikka pystyssä
        wp_send_json_success();
    }

    public function render_tier_info() {
        include PURNUKKA_STACK_PATH . 'views/tier-info.php'; // Oletetaan että tämä on tallessa
    }

    public function enqueue_dashboard_assets() {
        wp_enqueue_style('purnukka-admin-style', PURNUKKA_STACK_URL . 'assets/css/admin.css', [], '1.5.0');
    }
}