<?php
/**
 * Module: Access Control (v1.6.0 MASTER)
 * Valvoo lisenssirajoituksia (esim. max_locations) Master-tasolla.
 * Refactor: Constructor Injection. Poistettu riippuvuus globaaleista muuttujista.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Access_Control {
    private $core;

    public function __construct($core) {
        // Varmistetaan core-instanssin olemassaolo (Consistency Refactor)
        if (!$core) return;
        $this->core = $core;
        
        add_action('admin_init', [$this, 'enforce_limits']);
    }

    public function enforce_limits() {
        // Haetaan rajoitukset suoraan injektoidusta core-konfiguraatiosta
        $config = $this->core->config;
        $max_allowed = (int)($config['limits']['max_locations'] ?? 1);
        
        // Lasketaan julkaistut majoituskohteet (MPHB Room Types)
        $count_posts = wp_count_posts('mphb_room_type');
        $count_types = (int)($count_posts->publish ?? 0);

        // Jos raja on saavutettu tai ylitetty, poistetaan "Lisää uusi" -valikko
        if ($count_types >= $max_allowed) {
            remove_submenu_page('edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room_type');
            
            // Estetään pääsy suoraan linkillä post-new.php:hen
            global $pagenow;
            if ($pagenow === 'post-new.php' && ($_GET['post_type'] ?? '') === 'mphb_room_type') {
                wp_die(__('Maksupakettisi rajoitus on tullut vastaan. Päivitä paketti Hubissa lisätäksesi uusia kohteita.', 'purnukka'));
            }
        }
    }
}