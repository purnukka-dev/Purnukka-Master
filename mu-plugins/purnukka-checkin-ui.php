<?php
/**
 * Plugin Name: Purnukka Check-in UI (Production Match v1.3.1)
 * Description: Logic matched 1:1 with production code, UI styled for Master Standard.
 * Version: 1.3.1
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    // 1. MASTER SETTINGS & DYNAMIC ROUTING
    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : site_url('/payment-checkout/');

    $a = shortcode_atts(array(
        'rate'       => '30',
        'min_stay'   => '2',
        'product_id' => '3775', // Production Flex Product ID
        'form_id'    => '4', 
        'title'      => 'Welcome Home'
    ), $atts);

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* UI WRAPPER */
        .p-master-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: 0 auto 60px;
            padding: 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
        }

        /* PHASE 1: START BOX (Matched to Production Border/Gold) */
        .p-step-box-master {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 8px solid #b89b5e; 
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 30px;
            gap: 20px;
        }

        /* PHASE 2: CALC VIEW (Hidden by default) */
        #p-master-form-view {
            display: none; /* Fixed: Initial state */
            margin-top: 30px;
            text-align: left;
        }

        /* INPUT STYLING */
        .p-input-row-master {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .p-input-field-master {
            background: #fff;
            border: 1px solid #b89b5e; /* Production gold border */
            padding: 15px;
        }

        .p-input-field-master label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .p-input-field-master input {
            border: none;
            width: 100%;
            font-weight: bold;
            font-size: 22px;
            color: #1a2b28;
            outline: none;
            background: transparent;
        }

        /* SUMMARY & BUTTONS */
        .p-price-summary-master {
            border-top: 2px solid #f8f8f8;
            padding-top: 25px;
            margin-bottom: 30px;
            text-align: center;
        }

        .p-price-note-master { font-size: 11px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
        .p-price-total-master { font-size: 40px; font-weight: bold; color: #1a2b28; display: block; }

        .btn-p-gold-master { background: #b89b5e; color: #fff; border: none; padding: 18px; width: 100%; font-weight: bold; text-transform: uppercase; cursor: pointer; font-size: 13px; transition: 0.3s; }
        .btn-p-dark-master { background: #1a2b28; color: #fff; border: none; padding: 14px 25px; font-weight: bold; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; cursor: pointer; transition: 0.3s; white-space: nowrap; }

        @media (max-width: 650px) {
            .p-input-row-master { grid-template-columns: 1fr; }
            .p-step-box-master { flex-direction: column; text-align: center; }
        }
    </style>

    <div id="p-master-app-root" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div style="text-align: center; padding: 40px 0;">
            <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 4px; color: #b89b5e; font-weight: bold; display: block; margin-bottom: 10px;">Purnukka Group</span>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 42px; color: #1a2b28; margin: 0; font-weight: 400;"><?php echo esc_html($a['title']); ?></h1>
        </div>

        <div class="p-master-premium-wrapper">
            <i class="fas fa-key" style="color: #b89b5e; font-size: 36px; margin-bottom: 20px; display: block;"></i> 
            <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin: 0 0 15px 0; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 6px;">Check-in & Declaration</h2>
            <p style="font-size: 14px; color: #666; margin-bottom: 30px; line-height: 1.6;">
                The mandatory traveler declaration ensures a safe stay and keeps your insurance coverage active throughout your visit.
            </p>

            <div class="p-step-box-master" id="p-master-step-1">
                <div>
                    <strong style="color: #1a2b28; font-size: 16px;">Change in group size?</strong><br>
                    <span style="font-size: 12px; color: #666;">Add and pay for additional guests here.</span>
                </div>
                <button class="btn-p-dark-master" onclick="activateMasterCalc()">Add Guests</button>
            </div>

            <div id="p-master-form-view">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 22px; color: #1a2b28; margin-bottom: 20px;">Add guests to booking</h3>
                
                <div class="p-input-row-master">
                    <div class="p-input-field-master">
                        <label><i class="fas fa-users"></i> Additional Guests</label>
                        <input type="number" id="p-m-guests" value="1" min="1" oninput="runMasterRecalc()">
                    </div>
                    <div class="p-input-field-master">
                        <label><i class="fas fa-moon"></i> Nights</label>
                        <input type="number" id="p-m-nights" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="runMasterRecalc()">
                    </div>
                </div>

                <div class="p-price-summary-master">
                    <span id="p-m-info" class="p-price-note-master">STANDARD RATE (<?php echo $a['rate']; ?>€/NIGHT)</span>
                    <span class="p-price-total-master"><span id="p-m-final-sum">0</span> €</span>
                </div>

                <button class="btn-p-gold-master" onclick="proceedToMasterPay()">Update & Pay Now</button>
                <div onclick="location.reload()" style="text-align: center; margin-top: 15px; font-size: 11px; cursor: pointer; color: #888; text-transform: uppercase; letter-spacing: 1px;">Cancel</div>
            </div>

            <div style="margin-top: 60px; text-align: left; border-top: 1px solid #eee; padding-top: 40px;">
                <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function activateMasterCalc() {
        document.getElementById('p-master-step-1').style.display = 'none';
        document.getElementById('p-master-form-view').style.display = 'block';
        runMasterRecalc();
    }

    function runMasterRecalc() {
        const app = document.getElementById('p-master-app-root');
        const baseRate = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('p-m-guests').value) || 0;
        let n = parseInt(document.getElementById('p-m-nights').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        
        if (n < minN) n = minN;

        let up = baseRate;
        let note = "STANDARD RATE (" + baseRate + "€/NIGHT)";

        // Production logic ports
        if (n > 2 && n <= 6) { up = 20; note = "MID-TERM RATE (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { up = 15; note = "WEEKLY RATE (15€/NIGHT)"; }
        else if (n >= 14) { up = 10; note = "LONG-STAY RATE (10€/NIGHT)"; }

        document.getElementById('p-m-final-sum').innerText = g * n * up;
        document.getElementById('p-m-info').innerText = note;
    }

    function proceedToMasterPay() {
        const app = document.getElementById('p-master-app-root');
        const url = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = document.getElementById('p-m-final-sum').innerText;
        
        if (parseInt(sum) > 0) {
            const sep = url.includes('?') ? '&' : '?';
            window.location.href = url + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
        }
    }
    </script>

    <?php
    return ob_get_clean();
});