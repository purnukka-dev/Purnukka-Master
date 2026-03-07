<?php
/**
 * Plugin Name: Purnukka Stack Loader
 * Description: Modular SaaS engine for accommodation management.
 * Version: 1.5.1
 * Author: Purnukka Dev
 */

if (!defined('ABSPATH')) exit;

// Prioriteetti 1: Määritetään polkuvakiot Master-ytimelle
if (!defined('PURNUKKA_STACK_PATH')) {
    define('PURNUKKA_STACK_PATH', plugin_dir_path(__FILE__) . 'purnukka-stack/');
}

if (!defined('PURNUKKA_STACK_URL')) {
    define('PURNUKKA_STACK_URL', plugin_dir_url(__FILE__) . 'purnukka-stack/');
}

// Alustetaan moottori
require_once PURNUKKA_STACK_PATH . 'core.php';