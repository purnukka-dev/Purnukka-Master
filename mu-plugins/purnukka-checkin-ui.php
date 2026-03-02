<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master Sync v1.3.5)
 * Description: Synchronizes Master site visuals with Villa Purnukka production standards.
 * Version: 1.3.5
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : site_url('/payment-checkout/');

    $a = shortcode_atts(array(
        'rate'       => '30',
        'min_stay'   => '2',
        'product_id' => '3775', 
        'form_id'    => '4', 
        'title'      => 'Welcome Home'
    ), $atts);

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* 1. KORJATTU LEVEYS JA TAUSTA */
        .p-sync-container {
            font-family: 'Montserrat', sans-serif;
            max-width: 1000px; /* Pakotetaan leveys jotta lomake ei puristu */
            margin: 0 auto;
            background: #fff;
        }

        /* 2. PREMIUM HEADER (Palautettu Purnukka Group) */
        .p-sync-header {
            text-align: center;
            padding: 60px 20px 40px;
            border-bottom: 1px solid #f0f0f0;
        }
        .p-sync-brand {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #b89b5e;
            font-weight: 700;
            display: block;
            margin-bottom: 10px;
        }
        .p-sync-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
        }

        /* 3. FLOATING CONTENT (Villa Purnukka -ilme) */
        .p-sync-float-card {
            max-width: 850px;
            margin: -30px auto 60px;
            padding: 50px 40px;
            background: #ffffff;
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            position: relative;
            z-index: 10;
        }

        .p-sync-key { color: #b89b5e; font-size: 36px; margin-bottom: 25px; display: block; text-align: center; }
        .p-sync-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #1a2b28;
            border-bottom: 2px solid #b89b5e;
            display: inline-block;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        /* 4. LASKURIN KONTRASTI JA REUNAT */
        .p-sync-input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .p-sync-field {
            border: 1px solid #dcdcdc; /* Jämäkkä reunus palautettu */
            padding: 18px 25px;
            background: #fff;
        }

        .p-sync-field label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .p-sync-field label i { margin-right: 10px; color: #b89b5e; }

        .p-sync-field input {
            border: none;
            width: 100%;
            font-weight: 700;
            font-size: 26px;
            color: #1a2b28;
            outline: none;
        }

        /* 5. NAPIT JA SUMMA */
        .p-sync-total { font-size: 52px; font-weight: 700; color: #1a2b28; display: block; margin: 20px 0; text-align: center; }
        .p-sync-btn-gold { background: #b89b5e; color: #fff; border: none; padding: 22px; width: 100%; font-weight: 700; text-transform: uppercase; cursor: pointer; font-size: 14px; letter-spacing: 2px; }
    </style>

    <div id="p-sync-root" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-sync-container">
            <div class="p-sync-header">
                <span class="p-sync-brand">Purnukka Group</span>
                <h1><?php echo esc_html($a['title']); ?></h1>
            </div>

            <div class="p-sync-float-card">
                <i class="fas fa-key p-sync-key"></i>
                <div style="text-align: center;"><h2 class="p-sync-title">Check-in & Declaration</h2></div>
                
                <div class="p-sync-input-grid">
                    <div class="p-sync-field">
                        <label><i class="fas fa-users"></i> Additional Guests</label>
                        <input type="number" id="sync-g" value="1" min="1" oninput="runSyncCalc()">
                    </div>
                    <div class="p-sync-field">
                        <label><i class="fas fa-moon"></i> Nights</label>
                        <input type="number" id="sync-n" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="runSyncCalc()">
                    </div>
                </div>

                <div class="p-sync-total"><span id="sync-sum">0</span> €</div>
                <button class="p-sync-btn-gold" onclick="syncPay()">Update & Pay Now</button>

                <div style="margin-top: 80px; text-align: left; border-top: 1px solid #eee; padding-top: 60px;">
                    <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function runSyncCalc() {
        const app = document.getElementById('p-sync-root');
        const rate = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('sync-g').value) || 0;
        let n = parseInt(document.getElementById('sync-n').value) || 0;
        if (n < 2) n = 2;
        document.getElementById('sync-sum').innerText = g * n * rate;
    }
    function syncPay() {
        const app = document.getElementById('p-sync-root');
        window.location.href = app.getAttribute('data-checkout-url') + '?add-to-cart=' + app.getAttribute('data-product-id') + '&quantity=' + document.getElementById('sync-sum').innerText;
    }
    window.onload = runSyncCalc;
    </script>

    <?php
    return ob_get_clean();
});