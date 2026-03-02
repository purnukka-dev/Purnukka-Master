<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master Standard)
 * Description: Standardized English UI with improved contrast and wider layout.
 * Version: 1.2.3
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : site_url('/payment-checkout/');

    $a = shortcode_atts(array(
        'rate'       => '30',
        'min_stay'   => '2',
        'product_id' => '276',
        'form_id'    => '4', 
        'title'      => 'Welcome Home'
    ), $atts);

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* LEVITYS JA TAUSTA */
        .p-master-wrapper { 
            font-family: 'Montserrat', sans-serif; 
            max-width: 1000px; /* Nostettu 700px -> 1000px jotta lomake leviää */
            margin: 0 auto 60px; 
            padding: 0 20px; 
            text-align: center; 
        }

        .p-master-header { padding: 60px 20px 40px; text-align: center; }
        .p-master-brand { font-family: 'Montserrat', sans-serif; font-size: 10px; text-transform: uppercase; letter-spacing: 5px; color: #b89b5e; font-weight: 700; display: block; margin-bottom: 15px; }
        .p-master-header h1 { font-family: 'Playfair Display', serif; font-size: 32px; color: #1a2b28; margin: 0; font-weight: 400; }
        
        /* LAATIKOIDEN EROTTUVUUS */
        .p-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 25px; 
            margin-bottom: 30px; 
        }

        .p-input-box { 
            border: 1px solid #e0e0e0; 
            padding: 25px; 
            background: #f9f9f9; /* Lisätty hienovarainen taustaväri jotta erottuu valkoisesta */
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02); /* Kevyt varjo syvyyden luomiseksi */
            text-align: left;
        }

        .p-input-box label { display: block; font-size: 10px; color: #b89b5e; text-transform: uppercase; font-weight: 700; margin-bottom: 10px; }
        .p-input-box input { 
            border: none; 
            background: transparent; 
            width: 100%; 
            font-weight: 700; 
            font-size: 28px; 
            color: #1a2b28; 
            outline: none; 
        }

        /* PAINIKKEET JA LASKURI */
        .p-price-display { text-align: center; margin: 40px 0; }
        .p-price-total { font-size: 48px; font-weight: 700; color: #1a2b28; display: block; }
        .p-price-note { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        
        .p-btn-gold { 
            background: #b89b5e; 
            color: #fff; 
            border: none; 
            padding: 22px; 
            width: 100%; 
            max-width: 600px; /* Pidetään nappi tyylikkäänä vaikka sivu levenee */
            font-weight: 700; 
            text-transform: uppercase; 
            cursor: pointer; 
            font-size: 14px; 
            letter-spacing: 2px; 
            transition: 0.3s; 
            margin: 0 auto;
            display: block;
        }
        .p-btn-gold:hover { background: #1a2b28; }

        @media (max-width: 600px) { .p-grid { grid-template-columns: 1fr; } }
    </style>

    <div id="p-master-app" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-master-header">
            <span class="p-master-brand">Purnukka Group</span>
            <h1><?php echo esc_html($a['title']); ?></h1>
            <div style="width: 40px; height: 1px; background-color: #b89b5e; margin: 25px auto;"></div>
        </div>

        <div class="p-master-wrapper">
            <p style="font-size: 15px; color: #666; margin-bottom: 40px;">Please complete your check-in and traveler declaration.</p>

            <div id="p-master-form-ui">
                <div class="p-grid">
                    <div class="p-input-box">
                        <label>Additional Guests</label>
                        <input type="number" id="p-m-guests" value="1" min="1" oninput="recalcPurnukkaMaster()">
                    </div>
                    <div class="p-input-box">
                        <label>Nights</label>
                        <input type="number" id="p-m-nights" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="recalcPurnukkaMaster()">
                    </div>
                </div>
                
                <div class="p-price-display">
                    <span id="p-m-note" class="p-price-note">Standard Rate</span>
                    <span class="p-price-total"><span id="p-m-sum">0</span> €</span>
                </div>
                
                <button class="p-btn-gold" onclick="payPurnukkaMaster()">Update & Pay Now</button>
            </div>

            <div style="margin-top: 60px; text-align: left;">
                <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function recalcPurnukkaMaster() {
        const app = document.getElementById('p-master-app');
        const rateBase = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('p-m-guests').value) || 0;
        let n = parseInt(document.getElementById('p-m-nights').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        
        if (n < minN) n = minN;

        let currentRate = rateBase;
        let note = "STANDARD RATE (" + rateBase + "€/NIGHT)";
        
        if (n > 2 && n <= 6) { currentRate = 20; note = "MID-TERM DISCOUNT (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { currentRate = 15; note = "WEEKLY DISCOUNT (15€/NIGHT)"; }
        else if (n >= 14) { currentRate = 10; note = "LONG-STAY DISCOUNT (10€/NIGHT)"; }

        document.getElementById('p-m-sum').innerText = g * n * currentRate;
        document.getElementById('p-m-note').innerText = note;
    }

    function payPurnukkaMaster() {
        const app = document.getElementById('p-master-app');
        const checkoutBaseUrl = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = Math.round(parseFloat(document.getElementById('p-m-sum').innerText));

        if (sum > 0) {
            const sep = checkoutBaseUrl.includes('?') ? '&' : '?';
            window.location.href = checkoutBaseUrl + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
        }
    }
    // Suoritetaan laskenta kerran alussa
    window.onload = recalcPurnukkaMaster;
    </script>

    <?php
    return ob_get_clean();
});