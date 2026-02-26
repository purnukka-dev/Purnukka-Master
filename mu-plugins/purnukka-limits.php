<?php
/**
 * Plugin Name: Purnukka Stack - Access Control
 * Description: Estää uusien kohteiden luomisen, jos template-raja on täynnä.
 */

add_action( 'admin_init', function() {
    // Määritetään polku dynaamisesti palvelimen sisältä
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    
    // Jos tiedostoa ei löydy, lopetetaan suoritus
    if ( !file_exists( $config_path ) ) {
        return;
    }

    $config = json_decode( file_get_contents( $config_path ), true );
    $max_allowed = $config['stack_limits']['max_properties'] ?? 99;

    // Lasketaan MotoPressin majoituskohteet
    $current_posts = wp_count_posts( 'mphb_room_type' );
    $current_count = $current_posts->publish + $current_posts->draft + $current_posts->private;

    // Jos raja on täynnä, piilotetaan lisäystoiminnot
    if ( $current_count >= $max_allowed ) {
        
        // 1. Poistetaan "Add New" valikosta
        remove_submenu_page( 'edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room_type' );
        
        // 2. CSS-purkka: Piilotetaan painikkeet, joita WordPress ei anna helposti poistaa koodilla
        add_action( 'admin_head', function() {
            echo '<style>
                .post-type-mphb_room_type .page-title-action, 
                #menu-posts-mphb_room_type li:nth-child(3) { 
                    display: none !important; 
                }
            </style>';
        });

        // 3. Estetään suora pääsy lisäyssivulle URL-osoitteen kautta
        global $pagenow;
        if ( $pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'mphb_room_type' ) {
            wp_die( 'Purnukka-Stack: Kohteiden maksimimäärä (' . $max_allowed . ') on täynnä. Ota yhteys ylläpitoon.', 'Pääsy estetty', array('response' => 403) );
        }
    }
});