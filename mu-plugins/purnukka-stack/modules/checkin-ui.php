<?php
/**
 * Module: Check-in UI
 * Based on: Purnukka Check-in Master (Gold "Welcome home")
 */
if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    $config = $GLOBALS['purnukka']->config;
    $accent = $config['design_system']['colors']['accent'] ?? '#b89b5e';
    $primary = $config['design_system']['colors']['primary'] ?? '#1a2b28';
    $villa_name = $config['property_info']['name'] ?? 'Villa Purnukka';

    ob_start(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">

    <style>
        .purnukka-welcome-header { background: #ffffff; padding: 30px 20px 10px 20px; text-align: center; margin-top: -20px !important; }
        .p-brand-label { color: <?php echo $accent; ?>; font-family: 'Montserrat', sans-serif; letter-spacing: 2px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .btn-p-gold { background-color: <?php echo $accent; ?> !important; color: #fff !important; padding: 18px 40px; border: none; border-radius: 4px; font-weight: 700; cursor: pointer; }
        /* Tähän voit kopioida loput vanhan tiedostosi CSS-tyyleistä jos tarpeen */
    </style>

    <div class="purnukka-welcome-header">
        <div class="p-brand-label">Welcome home</div>
        <h1 style="font-family: 'Playfair Display', serif; font-size: 48px; color: <?php echo $primary; ?>;"><?php echo esc_html($villa_name); ?></h1>
    </div>

    <div style="text-align: center; padding: 40px;">
        <button class="btn-p-gold" onclick="alert('Proceeding to Check-in...')">START CHECK-IN</button>
    </div>
    <?php
    return ob_get_clean();
});