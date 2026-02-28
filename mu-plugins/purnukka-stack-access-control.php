<?php
/**
 * Plugin Name: Purnukka Stack - Access Control (v0.3)
 * Description: Restricts the number of accommodation types and units based on context.json configuration.
 * Author: Purnukka Group Oy
 */

if ( !defined('ABSPATH') ) exit;

add_action( 'admin_init', function() {
    // 1. Path to the master configuration
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    if ( !file_exists( $config_path ) ) return;

    $config = json_decode( file_get_contents( $config_path ), true );
    
    // Read the limit (default to 1 if not defined in JSON)
    $max_allowed = (isset($config['stack_limits']['max_properties'])) ? (int)$config['stack_limits']['max_properties'] : 1;
    $package_name = (isset($config['stack_limits']['package_level'])) ? $config['stack_limits']['package_level'] : 'Standard';

    // 2. Count current published Room Types and individual Units
    $count_types = (int) wp_count_posts( 'mphb_room_type' )->publish;
    $count_rooms = (int) wp_count_posts( 'mphb_room' )->publish;

    // 3. If limits are reached, lock the "Add New" features
    if ( $count_types >= $max_allowed || $count_rooms >= $max_allowed ) {
        
        // Remove "Add New" sub-menu links for both types
        remove_submenu_page( 'edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room_type' );
        remove_submenu_page( 'edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room' );

        // Visual safety: Hide "Add New" buttons via CSS in the admin head
        add_action( 'admin_head', function() {
            echo '<style>
                /* Hide "Add New" buttons from the top of the pages */
                .post-type-mphb_room_type .page-title-action, 
                .post-type-mphb_room .page-title-action,
                /* Hide menu links as a secondary measure */
                #menu-posts-mphb_room_type li:nth-child(3),
                #menu-posts-mphb_room_type li:nth-child(11) { 
                    display: none !important; 
                }
            </style>';
        });

        // Hard block: Prevent direct URL access to the "Add New" screens
        $current_screen = get_current_screen();
        if ( $current_screen && $current_screen->base === 'post' && $current_screen->action === 'add' ) {
            if ( isset($_GET['post_type']) && ($_GET['post_type'] === 'mphb_room_type' || $_GET['post_type'] === 'mphb_room') ) {
                wp_die( 
                    '<strong>Purnukka Stack:</strong> Maximum number of properties reached for your <strong>' . esc_html($package_name) . '</strong> plan. Please upgrade your subscription to add more units.', 
                    'Access Denied', 
                    array('response' => 403) 
                );
            }
        }
    }
});
// Access control verified 2026-02-28