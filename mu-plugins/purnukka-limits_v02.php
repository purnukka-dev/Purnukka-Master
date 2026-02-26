<?php
/**
 * Plugin Name: Purnukka Stack - Access Control (v0.1)
 * Description: Rajoittaa majoitustyyppien ja yksiköiden määrää JSON-konfiguraation mukaan.
 */

add_action( 'admin_init', function() {
    // 1. Polku konfiguraatioon
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    if ( !file_exists( $config_path ) ) return;

    $config = json_decode( file_get_contents( $config_path ), true );
    
    // Luetaan raja (oletus 1, jos ei määritetty)
    $max_allowed = (isset($config['stack_limits']['max_properties'])) ? (int)$config['stack_limits']['max_properties'] : 1;

    // 2. Lasketaan molemmat post-tyypit
    $count_types = (int) wp_count_posts( 'mphb_room_type' )->publish;
    $count_rooms = (int) wp_count_posts( 'mphb_room' )->publish;

    // 3. Jos jompikumpi ylittää rajan, lyödään lukot päälle
    if ( $count_types >= $max_allowed || $count_rooms >= $max_allowed ) {
        
        // Poistetaan "Add New" -linkit valikosta molemmille
        remove_submenu_page( 'edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room_type' );
        remove_submenu_page( 'edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room' );

        // Poistetaan nappulat visuaalisesti hallintapaneelista
        add_action( 'admin_head', function() {
            echo '<style>
                /* Piilotetaan "Add New" nappulat sivujen ylälaidasta */
                .post-type-mphb_room_type .page-title-action, 
                .post-type-mphb_room .page-title-action,
                /* Piilotetaan valikon linkit (varmistus) */
                #menu-posts-mphb_room_type li:nth-child(3),
                #menu-posts-mphb_room_type li:nth-child(11) { 
                    display: none !important; 
                }
            </style>';
        });

        // Estetään suora pääsy lisäyssivuille (URL-tasolla)
        $current_screen = get_current_screen();
        if ( $current_screen && $current_screen->base === 'post' && $current_screen->action === 'add' ) {
            if ( $_GET['post_type'] === 'mphb_room_type' || $_GET['post_type'] === 'mphb_room' ) {
                wp_die( 'Purnukka Stack: Maksimaalinen kohteiden määrä on saavutettu. Päivitä pakettisi suurempaan lisätäksesi kohteita.', 'Pääsy estetty', array('response' => 403) );
            }
        }
    }
});