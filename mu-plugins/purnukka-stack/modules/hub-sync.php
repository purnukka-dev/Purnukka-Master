<?php
/**
 * Module: Purnukka Hub Sync (v1.6.0)
 * Description: Dynaaminen Villa-luonti (Meta Box) ja WC-tuotelinkitys juoksevalla slugilla.
 * File: purnukka-hub-sync.php
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Hub_Sync {
    private $post_type = 'villa';

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_sync_endpoints']);
    }

    public function register_sync_endpoints() {
        register_rest_route('purnukka/v1', '/sync-villa', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_villa_sync'],
            'permission_callback' => '__return_true', // Tuotannossa API-key tarkistus tähän
        ]);
    }

    public function handle_villa_sync($request) {
        $params = $request->get_params();
        
        $index = intval($params['index'] ?? 1);
        $slug = "villa-" . $index;
        $title = $params['name'] ?? "Villa " . $index;
        
        // 1. Luodaan tai haetaan Villa (Meta Box CPT)
        $villa_id = $this->get_or_create_villa($slug, $title);

        // 2. Päivitetään dynaamiset Meta Box -kentät API-datalla
        update_post_meta($villa_id, 'purnukka_max_capacity', intval($params['max_capacity'] ?? 2));
        update_post_meta($villa_id, 'purnukka_base_guests', intval($params['base_guests'] ?? 2));
        update_post_meta($villa_id, 'purnukka_base_price', floatval($params['price'] ?? 100));
        update_post_meta($villa_id, 'purnukka_price_jump_limit', intval($params['price_jump_after'] ?? 2));

        // 3. Synkronoidaan linkitetty WooCommerce-tuote
        $product_id = $this->sync_linked_product($villa_id, $params);

        return new WP_REST_Response([
            'status' => 'success',
            'villa_id' => $villa_id,
            'product_id' => $product_id,
            'slug' => $slug
        ], 200);
    }

    private function get_or_create_villa($slug, $title) {
        $existing = get_page_by_path($slug, OBJECT, $this->post_type);
        if ($existing) return $existing->ID;

        return wp_insert_post([
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => $this->post_type
        ]);
    }

    private function sync_linked_product($villa_id, $params) {
        $product_id = get_post_meta($villa_id, '_linked_product_id', true);
        
        if (!$product_id || !get_post($product_id)) {
            $product = new WC_Product_Simple();
            $product->set_name("Majoitus: " . get_the_title($villa_id));
            $product->set_status('publish');
            $product->set_catalog_visibility('hidden');
            $product_id = $product->save();
            update_post_meta($villa_id, '_linked_product_id', $product_id);
        } else {
            $product = wc_get_product($product_id);
        }

        $product->set_regular_price(floatval($params['price'] ?? 100));
        $product->save();
        return $product->get_id();
    }
}

// Alustetaan moduuli
new Purnukka_Hub_Sync();