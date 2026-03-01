<?php
/**
 * Plugin Name: Purnukka Stack - Checkout Logic
 * Description: Hallitsee kassan dynaamiset lisäpalvelut. Sisältää lisäys- ja poistotoiminnot.
 * Version: 1.2
 */

if ( !defined('ABSPATH') ) exit;

add_shortcode( 'purnukka_master_checkout', function() {
    if ( is_admin() || ! function_exists('WC') || WC()->cart->is_empty() ) return '';

    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    $bg_color = '#1a2b28'; 
    $accent_color = '#b89b5e'; 

    if ( file_exists( $config_path ) ) {
        $config = json_decode( file_get_contents( $config_path ), true );
        $bg_color = $config['design_system']['colors']['primary'] ?? $bg_color;
        $accent_color = $config['design_system']['colors']['accent'] ?? $accent_color;
    }

    $output = '';
    $ids = array();
    $cart_items_map = array(); // Tallennetaan ID -> cart_item_key poistamista varten

    // Kerätään korissa olevat tiedot
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $cart_items_map[$cart_item['product_id']] = $cart_item_key;
    }

    // Haetaan ristiinmyytävät tuotteet
    foreach ( WC()->cart->get_cart() as $item ) {
        $linked = $item['data']->get_cross_sell_ids();
        if ( ! empty( $linked ) ) $ids = array_merge( $ids, $linked );
    }

    if ( ! empty( $ids ) ) {
        $output .= '<div class="purnukka-checkout-extras" style="border:1px solid ' . esc_attr($bg_color) . '; border-top:4px solid ' . esc_attr($accent_color) . '; padding:15px; background:#fdfdfd; margin-bottom:20px;">';
        $output .= '<h3 style="margin-top:0; color:' . esc_attr($bg_color) . '; text-transform:uppercase; font-size:14px; letter-spacing:1px; border-bottom:1px solid #eee; padding-bottom:10px;">Enhance Your Stay</h3>';
        $output .= '<div style="max-height:500px; overflow-y:auto; margin-top:10px;">';
        
        foreach ( array_unique($ids) as $id ) {
            $product = wc_get_product($id);
            if ( ! $product || ! $product->is_visible() ) continue;

            $output .= '<div style="background:#fff; border:1px solid #eee; padding:12px; margin-bottom:10px;">';
            $output .= '<div style="margin-bottom:8px;">';
            $output .= '<strong style="display:block; color:' . esc_attr($bg_color) . '; font-size:13px; line-height:1.2;">' . $product->get_name() . '</strong>';
            $output .= '<span style="color:' . esc_attr($accent_color) . '; font-weight:bold; font-size:13px;">' . $product->get_price_html() . '</span>';
            $output .= '</div>';
            
            if ( isset( $cart_items_map[$id] ) ) {
                // Tuote on jo korissa -> Näytetään poistolinkki
                $remove_url = wc_get_cart_remove_url( $cart_items_map[$id] );
                $output .= '<div style="text-align:center; padding:5px; border:1px solid #eee; background:#fafafa;">';
                $output .= '<span style="color:#27ae60; font-size:10px; text-transform:uppercase; font-weight:bold; display:block; margin-bottom:4px;">✓ Added</span>';
                $output .= '<a href="' . esc_url($remove_url) . '" style="color:#e74c3c; font-size:9px; text-decoration:underline; text-transform:uppercase; font-weight:bold;">Remove</a>';
                $output .= '</div>';
            } else {
                // Tuote ei ole korissa -> Näytetään lisäysnappi
                $output .= '<a href="?add-to-cart=' . $id . '" style="background:' . esc_attr($bg_color) . '; color:' . esc_attr($accent_color) . '; padding:8px; text-decoration:none; text-transform:uppercase; font-size:10px; font-weight:bold; border:1px solid ' . esc_attr($accent_color) . '; display:block; text-align:center;">+ Add to Booking</a>';
            }
            
            $output .= '</div>';
        }
        
        $output .= '</div></div>';
    }

    return $output;
});