<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master Standard)
 * Description: Dynaaminen matkustajailmoitus ja laskuri dynaamisella kassa-linkityksellä.
 * Version: 1.2.0
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    // 1. DYNAAMINEN KASSA-URL
    // Haetaan WooCommercen asetuksista kassan osoite (oli se mikä tahansa)
    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : site_url('/payment-checkout/');

    $a = shortcode_atts(array(
        'hinta'    => '30',
        'minimi'   => '2',
        'tuote_id' => '276',
        'lomake_id' => '31', 
        'otsikko'  => 'Welcome Home'
    ), $atts);

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .p-master-header { padding: 60px 20px 40px; text-align: center; background: #fff; }
        .p-master-brand { font-family: 'Montserrat', sans-serif; font-size: 10px; text-transform: uppercase; letter-spacing: 5px; color: #b89b5e; font-weight: 700; display: block; margin-bottom: 15px; }
        .p-master-header h1 { font-family: 'Playfair Display', serif; font-size: 32px; color: #1a2b28; margin: 0; font-weight: 400; }
        .p-master-wrapper { font-family: 'Montserrat', sans-serif; max-width: 700px; margin: 0 auto 60px; padding: 0 20px; text-align: center; }
        .p-master-box { background: #fdfdfd; border: 1px solid #f0f0f0; border-top: 4px solid #b89b5e; padding: 40px; display: flex; align-items: center; justify-content: space-between; text-align: left; margin: 30px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .p-btn-dark { background: #1a2b28; color: #fff; border: none; padding: 16px 25px; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; cursor: pointer; transition: 0.3s; }
        .p-btn-dark:hover { background: #b89b5e; }
        #p-master-form { display: none; margin: 40px 0; text-align: left; animation: pFade 0.4s; padding-bottom: 40px; border-bottom: 1px solid #eee; }
        .p-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .p-input-box { border: 1px solid #eee; padding: 15px; background: #fff; border-radius: 2px; }
        .p-input-box label { display: block; font-size: 9px; color: #b89b5e; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; }
        .p-input-box input { border: none; width: 100%; font-weight: 700; font-size: 22px; color: #1a2b28; outline: none; }
        .p-price-display { text-align: center; margin-bottom: 30px; }
        .p-price-total { font-size: 42px; font-weight: 700; color: #1a2b28; display: block; }
        .p-price-note { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .p-btn-gold { background: #b89b5e; color: #fff; border: none; padding: 20px; width: 100%; font-weight: 700; text-transform: uppercase; cursor: pointer; font-size: 13px; letter-spacing: 2px; transition: 0.3s; }
        .p-btn-gold:hover { background: #1a2b28; }
        @keyframes pFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 600px) { .p-master-box { flex-direction: column; text-align: center; } .p-grid { grid-template-columns: 1fr; } }
    </style>

    <div id="p-master-app" 
         data-hinta="<?php echo esc_attr($a['hinta']); ?>" 
         data-minimi="<?php echo esc_attr($a['minimi']); ?>" 
         data-pid="<?php echo esc_attr($a['tuote_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-master-header">
            <span class="p-master-brand">Purnukka Group</span>
            <h1><?php echo esc_html($a['otsikko']); ?></h1>
            <div style="width: 40px; height: 1px; background-color: #b89b5e; margin: 25px auto;"></div>
        </div>

        <div class="p-master-wrapper">
            <p style="font-size: 15px; color: #666; line-height: 1.6;">Please complete your check-in and traveler declaration to receive your access code.</p>

            <div class="p-master-box" id="p-gate-master">
                <div>
                    <strong style="color: #1a2b28; font-size: 16px;">Change in group size?</strong><br>
                    <span style="font-size: 13px; color: #888;">Update your booking and pay for extra guests here.</span>
                </div>
                <button class="p-btn-dark" onclick="initPurnukkaMaster()">Add Guests</button>
            </div>

            <div id="p-master-form">
                <div class="p-grid">
                    <div class="p-input-box">
                        <label>Additional Guests</label>
                        <input type="number" id="p-m-guests" value="1" min="1" oninput="recalcPurnukkaMaster()">
                    </div>
                    <div class="p-input-box">
                        <label>Nights</label>
                        <input type="number" id="p-m-nights" value="<?php echo esc_attr($a['minimi']); ?>" min="<?php echo esc_attr($a['minimi']); ?>" oninput="recalcPurnukkaMaster()">
                    </div>
                </div>
                <div class="p-price-display">
                    <span id="p-m-note" class="p-price-note">Standard Rate</span>
                    <span class="p-price-total"><span id="p-m-sum">0</span> €</span>
                </div>
                <button class="p-btn-gold" onclick="payPurnukkaMaster()">Update & Pay</button>
                <div onclick="location.reload()" style="text-align: center; margin-top: 20px; font-size: 10px; cursor: pointer; color: #bbb; text-transform: uppercase; letter-spacing: 1px;">Cancel</div>
            </div>

            <div style="margin-top: 50px;">
                <?php echo do_shortcode('[formidable id=' . $a['lomake_id'] . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function initPurnukkaMaster() {
        document.getElementById('p-gate-master').style.display = 'none';
        document.getElementById('p-master-form').style.display = 'block';
        recalcPurnukkaMaster();
    }
    function recalcPurnukkaMaster() {
        const app = document.getElementById('p-master-app');
        const hBase = parseInt(app.getAttribute('data-hinta'));
        const g = parseInt(document.getElementById('p-m-guests').value) || 0;
        let n = parseInt(document.getElementById('p-m-nights').value) || 0;
        const mN = parseInt(app.getAttribute('data-minimi'));
        if (n < mN) n = mN;

        let curP = hBase;
        let note = "STANDARD RATE ("+hBase+"€/NIGHT)";
        if (n > 2 && n <= 6) { curP = 20; note = "MID-TERM DISCOUNT (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { curP = 15; note = "WEEKLY DISCOUNT (15€/NIGHT)"; }
        else if (n >= 14) { curP = 10; note = "LONG-STAY DISCOUNT (10€/NIGHT)"; }

        document.getElementById('p-m-sum').innerText = g * n * curP;
        document.getElementById('p-m-note').innerText = note;
    }
    function payPurnukkaMaster() {
        const app = document.getElementById('p-master-app');
        const checkoutBaseUrl = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-pid');
        const sum = Math.round(parseFloat(document.getElementById('p-m-sum').innerText));

        if (sum > 0) {
            // Lisätään parametrit dynaamisen URL:n perään
            const sep = checkoutBaseUrl.includes('?') ? '&' : '?';
            window.location.href = checkoutBaseUrl + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
        } else {
            alert('Please select guests and nights.');
        }
    }
    </script>

    <?php
    return ob_get_clean();
});