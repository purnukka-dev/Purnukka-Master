<?php
/**
 * Module: Tier Manager (v1.5)
 * Description: Dynamically controls features and limits based on the subscription tier in context.json.
 * Standards: English code, variables, and technical comments. UI strings support translation.
 */

if (!defined('ABSPATH')) exit;

/**
 * Enforce subscription limits defined in context.json
 */
add_action('admin_init', function() {
    // Access the global Purnukka instance for configuration
    $config = $GLOBALS['purnukka']->config;
    
    // Set limits from context.json. Default to 1 (Solo tier) if not set.
    $max_locations = (isset($config['limits']['max_locations'])) ? (int)$config['limits']['max_locations'] : 1;
    $current_tier  = $config['product']['tier'] ?? 'Solo';

    // Count published accommodation types (MotoPress Room Types)
    $published_types = (int) wp_count_posts('mphb_room_type')->publish;

    // IF LIMIT REACHED: Restrict creation of new units
    if ($published_types >= $max_locations) {
        
        // 1. Remove "Add New" submenu links
        remove_submenu_page('edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room_type');
        remove_submenu_page('edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room');

        // 2. Hide "Add New" buttons from the UI via CSS injection
        add_action('admin_head', function() {
            echo '<style>
                .post-type-mphb_room_type .page-title-action, 
                .post-type-mphb_room .page-title-action,
                #menu-posts-mphb_room_type .wp-first-item + li { 
                    display: none !important; 
                }
            </style>';
        });

        // 3. Prevent direct URL access to the "Add New" post page
        global $pagenow;
        if ($pagenow === 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'mphb_room_type') {
            wp_die(
                '<h3>Purnukka Stack: Limit Reached</h3>' .
                '<p>Your current plan (<strong>' . esc_html($current_tier) . '</strong>) allows for a maximum of ' . $max_locations . ' location(s).</p>' .
                '<p>Please contact support to upgrade your subscription.</p>',
                'Access Denied',
                ['response' => 403]
            );
        }
    }
});