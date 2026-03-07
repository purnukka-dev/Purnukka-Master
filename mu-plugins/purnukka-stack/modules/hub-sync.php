<?php
/**
 * Module: Hub Sync (v1.7.0)
 * Description: Dynaaminen Villa-luonti Bearer Token -suojauksella.
 * File: hub-sync.php
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
            'permission_callback' => [$this, 'validate_api_token'],
        ]);
    }

    /**
     * KRIITTINEN: Bearer Token -tunnistautuminen (Analyysin kohta 1)
     */
    public function validate_api_token($request) {
        $auth_header = $request->get_header('Authorization');
        
        // Haetaan odotettu token configista (oletuksena turvallinen fallback)
        $config = $GLOBALS['purnukka']->config;
        $expected_token = $config['api_token'] ?? '';

        if (empty($expected_token)) {
            error_log('Purnukka Alert: API Token is not set in context.json!');
            return new WP_Error('rest_forbidden', 'API setup incomplete.', ['status' => 401]);
        }

        if ($auth_header !== 'Bearer ' . $expected_token) {
            return new WP_Error('rest_forbidden', 'Invalid API Token.', ['status' => 403]);
        }

        return true;
    }

    public function handle_villa_sync($request) {
        $params = $request->get_params();
        
        $index = intval($params['index'] ?? 1);
        $slug = "villa-" . $index;
        $title = $params['name'] ?? "Villa " . $index;
        
        // 1. Luodaan tai haetaan Villa (Meta Box CPT)
        $villa_id = $this->get_or_create_villa($slug, $title);

        // 2. Päivitetään dynaamiset Meta Box -kentät
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

new Purnukka_Hub_Sync();