<?php
/**
 * Module: Purnukka Checkout Logic (v1.6.5 MASTER)
 * Description: Dynaaminen hinta- ja tuotehallinta. Alkuperäinen logiikka säilytetty.
 * Refactor: Constructor Injection.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Checkout_Logic {
    private $core;

    public function __construct($core) {
        if (!$core) return;
        $this->core = $core;

        // SÄILYTETTY: Alkuperäiset koukut
        add_action('template_redirect', [$this, 'handle_checkout_redirects']);
        add_filter('woocommerce_cart_item_quantity', [$this, 'lock_villa_product_quantity'], 10, 3);
    }

    /**
     * Ohjaukset ja ostoskoriin lisäys.
     */
    public function handle_checkout_redirects() {
        if (!function_exists('WC')) return;

        // Dynaaminen haku: haetaan kaikki tuotteet, jotka on linkitetty villoihin
        $args = [
            'post_type' => 'villa',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ];
        $villas = get_posts($args);
        $linked_product_ids = [];
        
        foreach($villas as $vid) {
            $pid = get_post_meta($vid, '_linked_product_id', true);
            if($pid) {
                $linked_product_ids[] = intval($pid);
            }
        }

        $current_add_id = isset($_GET['add-to-cart']) ? intval($_GET['add-to-cart']) : 0;

        // 1. ADD-TO-CART FLOW (Alkuperäinen logiikka dynaamisilla ID:illä)
        if ($current_add_id && in_array($current_add_id, $linked_product_ids)) {
            WC()->cart->empty_cart();
            $quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
            WC()->cart->add_to_cart($current_add_id, $quantity);
            wp_safe_redirect(wc_get_checkout_url());
            exit;
        }

        // 2. AUTO-RECOVERY (Alkuperäinen toiminnallisuus)
        if (is_checkout() && WC()->cart->is_empty() && !isset($_GET['order-received'])) {
            wp_safe_redirect(home_url('/check-in/'));
            exit;
        }
    }

    /**
     * Lukitaan määrät dynaamisille tuotteille (Alkuperäinen toiminnallisuus).
     */
    public function lock_villa_product_quantity($product_quantity, $cart_item_key, $cart_item) {
        $is_villa_product = false;
        
        // Haetaan villat ja niiden linkitetyt tuotteet
        $args = ['post_type' => 'villa', 'posts_per_page' => -1, 'fields' => 'ids'];
        $villas = get_posts($args);
        
        foreach($villas as $vid) {
            $pid = get_post_meta($vid, '_linked_product_id', true);
            if(intval($pid) === intval($cart_item['product_id'])) {
                $is_villa_product = true;
                break;
            }
        }

        if ($is_villa_product) {
            return $cart_item['quantity'];
        }
        
        return $product_quantity;
    }
}