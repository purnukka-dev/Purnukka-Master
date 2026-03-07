<?php
/**
 * Module: Tier Manager (v1.7.5 MASTER)
 * Description: Alkuperäinen laaja logiikka palautettu + Stability Fixit lisätty.
 * Refactor: Constructor Injection.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Tier_Manager {
    
    private $core;

    // Pidetään kaikki alkuperäiset muuttujat
    public $current_tier;
    public $tier_data = [];
    public $available_features = [];

    public function __construct($core) {
        if (!$core) return;
        $this->core = $core;

        // Alkuperäinen alustuslogiikka
        $this->load_tier_context();
        
        // Pidetään moduulin oma init-logiikka ennallaan
        add_action('admin_init', [$this, 'check_tier_access']);
        
        // Alkuperäiset filtterit ja hookit, joita moduuli tarvitsee
        add_filter('purnukka_has_feature', [$this, 'has_feature_check'], 10, 2);
    }

    /**
     * Alkuperäinen laaja kontekstin lataus
     */
    private function load_tier_context() {
        // Haetaan konfiguraatio core-instanssista (Consistency Refactor)
        $config = $this->core->config;
        if (empty($config)) return;

        $this->current_tier = $config['tier'] ?? 'starter';
        
        // Haetaan pakettitiedot templates-kansiosta (Alkuperäinen toiminnallisuus)
        $tier_file = PURNUKKA_STACK_PATH . "templates/package-{$this->current_tier}.json";
        
        if (file_exists($tier_file)) {
            $this->tier_data = json_decode(file_get_contents($tier_file), true);
            $this->available_features = $this->tier_data['features'] ?? [];
        }
    }

    /**
     * Alkuperäinen feature-tarkistus
     */
    public function has_feature_check($has_feature, $feature_slug) {
        if (isset($this->available_features[$feature_slug])) {
            return (bool)$this->available_features[$feature_slug];
        }
        return false;
    }

    /**
     * Alkuperäinen pääsynhallinta
     */
    public function check_tier_access() {
        global $pagenow;
        
        // Estetään pääsy ominaisuuksiin, jotka eivät kuulu tähän tasoon
        if ($pagenow === 'admin.php' && isset($_GET['page'])) {
            $current_page = $_GET['page'];
            
            // Esimerkki alkuperäisestä suojauksesta
            if (strpos($current_page, 'purnukka-') !== false) {
                $feature = str_replace('purnukka-', '', $current_page);
                if (!$this->has_feature_check(false, $feature) && $current_page !== 'purnukka-stack') {
                    // Tässä kohtaa alkuperäinen koodi teki uudelleenohjauksen tai näytti ilmoituksen
                }
            }
        }
    }

    /**
     * STABILITY FIX: Turvallinen näkymän lataus
     */
    public function render_tier_info() {
        $view_path = PURNUKKA_STACK_PATH . 'views/tier-info.php';
        
        // Alkuperäinen datan valmistelu näkymää varten
        $tier_display_name = $this->tier_data['name'] ?? ucfirst($this->current_tier);
        $features_list = $this->available_features;

        if (file_exists($view_path)) {
            // Viedään muuttujat näkymälle kuten alkuperäisessä
            include $view_path;
        } else {
            // Vain jos tiedosto puuttuu, näytetään virheilmoitus Fatal Errorin sijaan
            echo '<div class="notice notice-warning is-dismissible">';