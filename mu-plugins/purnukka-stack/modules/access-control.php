<?php
/**
 * Module: Access Control
 * Description: Enforces tier-based limits (Solo, Growth, Unlimited).
 * Language: English
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Access_Control {
    public function __construct() {
        add_action('admin_init', [$this, 'enforce_tier_restrictions']);
    }

    public function enforce_tier_restrictions() {
        $config = $GLOBALS['purnukka']->config;
        $tier = $config['product']['tier'] ?? 'Solo';
        
        // Example: If on Solo tier, prevent access to "Smart Locks" even if toggled in JSON
        if ($tier === 'Solo') {
            // We can add logic here to override features if they are not allowed in Solo
            // For now, let's just keep it as a placeholder for license validation
        }
    }
}

new Purnukka_Access_Control();