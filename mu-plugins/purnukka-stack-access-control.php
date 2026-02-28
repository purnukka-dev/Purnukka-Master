<?php
/**
 * Plugin Name: Purnukka Stack - Access Control (v0.4)
 * Description: Restricts the number of accommodation types and units based on context.json configuration.
 * Version: 0.4
 */

if ( !defined('ABSPATH') ) exit;

add_action( 'admin_init', function() {
    // 1. Path to the configuration
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    if ( !file_exists( $config_path ) ) return;

    $config = json_decode( file_get_contents( $config_path ), true );
    
    // Updated keys to match the new English JSON structure
    $max_allowed = (isset($config['limits']['max_locations'])) ? (int)$config['limits']['max_locations'] : 1;
    $tier_name = (isset($config['product']['tier'])) ? $config['product']['tier'] : 'Solo';

    // 2. Count current published Room Types (Accommodation Types) and individual Units
    $count_types = (int) wp_count_posts( 'mphb_room_type' )->publish;
    $count_rooms = (int) wp_count_posts( 'mphb_room' )->publish;

    // 3. If limits are reached, lock the "Add New" features
    if ( $count_types >= $max_allowed || $count_rooms >= $max_allowed ) {
        
        // Remove "Add New" sub-menu links for both types
        remove_submenu_page( 'edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room_type' );
        remove_submenu_page( 'edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room' );

        // Visual safety: Hide "Add New" buttons via CSS
        add_action( 'admin_head', function() {
            echo '<style>
                .post-type-mphb_room_type .page-title-action, 
                .post-type-mphb_room .page-title-action,
                #menu-posts-mphb_room_type li:nth-child(3),
                #menu-posts-mphb_room_type li:nth-child(11) { 
                    display: none !important; 
                }
            </style>';
        });

        // Hard block: Prevent direct URL access to "Add New"
        $current_screen = get_current_screen();
        if ( $current_screen && $current_screen->base === 'post' && $current_screen->action === 'add' ) {
            if ( isset($_GET['post_type']) && ($_GET['post_type'] === 'mphb_room_type' || $_GET['post_type'] === 'mphb_room') ) {
                wp_die( 
                    '<strong>Purnukka Stack:</strong> Maximum number of locations reached for your <strong>' . esc_html($tier_name) . '</strong> edition. Please upgrade your subscription to add more units.', 
                    'Access Denied', 
                    array('response' => 403) 
                );
            }
        }
    }
});