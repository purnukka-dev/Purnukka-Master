<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka Tier Manager - Hallitsee lisenssirajoja, näkymiä ja pakettien ominaisuuksia.
 * Taso: Pomminvarma Master-muotti.
 */
class Purnukka_Tier_Manager {
    private $core;
    private $current_tier;
    private $limits;

    public function __construct($core) {
        $this->core = $core;
        
        // Haetaan taso ja rajat contextista. Oletus Solo, jos JSON puuttuu.
        $product = $this->core->get_context('product', []);
        $this->current_tier = !empty($product['tier']) ? $product['tier'] : 'Solo';
        $this->limits = $this->core->get_context('limits', []);

        add_action('admin_menu', [$this, 'add_purnukka_menu']);
        add_action('admin_init', [$this, 'enforce_tier_limits']);
    }

    /**
     * Lisää Purnukka-hallintavalikon WordPressin sivupalkkiin.
     */
    public function add_purnukka_menu() {
        add_menu_page(
            'Purnukka Stack',
            'Purnukka',
            'manage_options',
            'purnukka-stack',
            [$this, 'render_admin_dashboard'],
            'dashicons-admin-home',
            2
        );

        add_submenu_page(
            'purnukka-stack',
            'Paketin tiedot',
            'Taso: ' . esc_html($this->current_tier),
            'manage_options',
            'purnukka-tier',
            [$this, 'render_tier_info']
        );
    }

    /**
     * Lataa visuaalisen dashboardin view-tiedostosta.
     */
    public function render_admin_dashboard() {
        $view_file = PURNUKKA_STACK_PATH . 'views/admin-dashboard.php';
        
        if (file_exists($view_file)) {
            // Viedään context data näkymälle
            $context = $this->core->context;
            include $view_file;
        } else {
            echo '<div class="wrap"><h1>Purnukka Stack</h1><p>Dashboard-näkymää ei löytynyt.</p></div>';
        }
    }

    /**
     * Renderöi tiedot nykyisestä paketista ja sen rajoista.
     */
    public function render_tier_info() {
        echo '<div class="wrap">';
        echo '<h1>Paketin hallinta</h1>';
        echo '<div class="card">';
        echo '<h2>Nykyinen taso: ' . esc_html($this->current_tier) . '</h2>';
        echo '<p>Version: ' . esc_html($this->core->get_context('product', [])['version'] ?? '1.0.0') . '</p>';
        
        if (!empty($this->limits)) {
            echo '<h3>Rajoitukset:</h3><ul>';
            foreach ($this->limits as $key => $value) {
                echo '<li>' . esc_html($key) . ': ' . esc_html($value) . '</li>';
            }
            echo '</ul>';
        }
        echo '</div></div>';
    }

    /**
     * Estää SOLO-käyttäjää ylittämästä rajoja (esim. kohteiden määrä).
     */
    public function enforce_tier_limits() {
        // Pomminvarma lukko: Älä rajoita ylläpitäjää jos kyseessä on huolto tai multisite.
        if (defined('WP_INSTALLING') && WP_INSTALLING) return;
        if (!is_admin()) return;

        // Esimerkki: MotoPress-rajoitukset Solo-tasolla.
        if ($this->current_tier === 'Solo') {
            $max_locs = $this->limits['max_locations'] ?? 1;
            
            // Tähän kytketään myöhemmin post-type count tarkistus,
            // joka estää "Add New" napin näkymisen jos raja on täynnä.
        }
    }

    /**
     * Helpperi muille moduuleille: onko ominaisuus sallittu tässä tasossa?
     */
    public function has_feature($feature_name) {
        $features = $this->core->get_context('features', []);
        return !empty($features[$feature_name]);
    }
}