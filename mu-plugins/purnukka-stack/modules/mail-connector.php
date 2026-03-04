<?php
/**
 * Module: Mail Connector (v1.5 Master)
 * Description: Automatically synchronizes SMTP settings for WP Mail SMTP plugin from context.json.
 * Ported from: purnukka-stack-core.php (v1.2)
 */

if (!defined('ABSPATH')) exit;

/**
 * Sync SMTP settings on admin initialization
 */
add_action('admin_init', function() {
    $config = $GLOBALS['purnukka']->config;
    
    // Check if technical/smtp config exists
    if (empty($config['technical']['smtp_host'])) return;

    $tech = $config['technical'];
    $info = $config['property_info'] ?? [];

    // Build the options array as expected by WP Mail SMTP
    $smtp_options = [
        'mail' => [
            'from_email' => $info['email'] ?? get_option('admin_email'),
            'from_name'  => $info['name'] ?? get_bloginfo('name'),
            'mailer'     => 'smtp',
            'return_path' => true
        ],
        'smtp' => [
            'host' => $tech['smtp_host'],
            'port' => $tech['smtp_port'] ?? 587,
            'auth' => true,
            'user' => $tech['smtp_user'],
            'pass' => $tech['smtp_pass'],
            'encryption' => $tech['smtp_encryption'] ?? 'tls',
            'autotls'    => true
        ]
    ];

    // Nuclear Sync: Force update the WP Mail SMTP option in database
    update_option('wp_mail_smtp', $smtp_options);
});