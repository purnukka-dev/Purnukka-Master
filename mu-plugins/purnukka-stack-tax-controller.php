<?php
/**
 * Plugin Name: Purnukka Stack - Tax Controller
 * Description: Pakottaa ALV-kannat kooditasolla (Majoitus 10% / Muut 25.5%) context.jsonin perusteella.
 * Version: 1.0
 */

if ( !defined('ABSPATH') ) exit;

add_filter( 'woocommerce_product_get_tax_class', 'purnukka_force_tax_class', 10, 2 );

function purnukka_force_tax_class( $tax_class, $product ) {
    // 1. Haetaan majoitustyypit (esim. MotoPress Accommodation)
    $is_accommodation = ($product->get_type() === 'mphb_room_type');

    if ( $is_accommodation ) {
        return 'vat-10-stay'; // Pakotetaan 10% kanta
    }

    // 2. Kaikki muu menee oletuksena 25.5% kantaan
    return 'standard'; 
}