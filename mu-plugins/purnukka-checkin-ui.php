<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master)
 * Description: Master UI using Production CSS/Logic with Product ID 276.
 * Version: 1.5.0
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    // 1. SETTINGS: Logic based on your request (Product 276)
    $a = shortcode_atts(array(
        'price'      => '30',
        'minimum'    => '2',
        'product_id' => '276', // AS REQUESTED
        'form_id'    => '4',   // MASTER FORM ID
        'title'      => 'Traveler Declaration'
    ), $atts);

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* 2. STYLES: Copied from your working Production code */
        :root {
            --p-primary: #1a2b28;
            --p-accent: #b89b5e;
        }

        .purnukka-welcome-header {
            background: #ffffff;
            padding: 60px 20px 40px 20px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .p-brand-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: var(--p-accent);
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .purnukka-welcome-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 8vw, 42px);
            color: var(--p-primary);
            margin: 0;
            font-weight: 400;
        }

        .purnukka-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: -30px auto 60px auto;
            padding: 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }

        .p-top-icon { color: var(--p-accent); font-size: 36px; margin-bottom: 20px; display: block; }

        .p-step-box {
            background: #fdfdfd;
            border: 1px solid var(--p-primary);
            border-left: 6px solid var(--p-accent); 
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 30px;
            gap: 20px;
        }

        .btn-p-dark {
            background: var(--p-primary);
            color: #fff;
            border: none;
            padding: 14px 25px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.3s ease;
            white-space: nowrap;
        }
        .btn-p-dark:hover { background: var(--p-accent); }

        /* DYNAMIC FORM VIEW */
        #purnukka-form-view {
            display: none;
            margin-top: 30px;
            text-align: left;
        }

        .p-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .p-input-field { background: #fff; border: 1px solid var(--p-accent); padding: 15px; }
        .p-input-field label { display: block; font-size: 10px; color: #888; text-transform: uppercase; margin-bottom: 5px; font-weight: bold; }
        .p-input-field input { border: none; width: 100%; font-weight: bold; font-size: 22px; color: var(--p-primary); outline: none; background: transparent; }

        .p-price-summary { border-top: 2px solid #f8f8f8; padding-top: 25px; margin-bottom: 30px; text-align: center; }
        .p-price-note { font-size: 11px; color: var(--p-accent); font-weight: bold; text-transform: uppercase; }
        .p-price-total { font-size: 40px; font-weight: bold; color: var(--p-primary); display: block; }

        .btn-p-gold {
            background: var(--p-accent);
            color: #fff;
            border: none;
            padding: 18px;
            width: 100%;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            font-size: 13px;
            transition: 0.3s;
        }

        @media (max-width: 650px) {
            .p-step-box { flex-direction: column; text-align: center; }
            .p-input-row { grid-template-columns: 1fr; }
        }
    </style>

    <div id="p-master-logic" 
         data-pid="<?php echo esc_attr($a['product_id']); ?>" 
         data-base="<?php echo esc_attr($a['price']); ?>" 
         data-min="<?php echo esc_attr($a['minimum']); ?>">

        <div class="purnukka-welcome-header">
            <span class="p-brand-label">Purnukka Group</span>
            <h1>Welcome Home</h1>
        </div>

        <div class="purnukka-premium-wrapper">
            <i class="fas fa-key p-top-icon"></i> 
            <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin: 0 0 15px 0; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 6px;">
                <?php echo esc_html($a['title']); ?>
            </h2>
            <p style="font-size: 14px; color: #666; margin: 10px auto 30px auto; max-width: 650px; line-height: 1.6;">
                Please complete your traveler declaration to receive your access codes and arrival instructions.
            </p>

            <div class="p-step-box" id="p-step-1">
                <div>
                    <strong style="color: #1a2b28; font-size: 16px;">Change in group size?</strong><br>
                    <span style="font-size: 12px; color: #666;">Add additional guests and pay for extra beds here.</span>
                </div>
                <button class="btn-p-dark" onclick="activateMasterForm()">Add Guests</button>
            </div>

            <div id="purnukka-form-view">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 22px; color: #1a2b28; margin-bottom: 20px;">Add guests to your booking</h3>
                
                <div class="p-input-row">
                    <div class="p-input-field">
                        <label><i class="fas fa-users"></i> Guests (qty)</label>
                        <input type="number" id="m-guests" value="1" min="1" oninput="runMasterRecalc()">
                    </div>
                    <div class="p-input-field">
                        <label><i class="fas fa-moon"></i> Nights</label>
                        <input type="number" id="m-nights" value="<?php echo esc_attr($a['minimum']); ?>" min="<?php echo esc_attr($a['minimum']); ?>" oninput="runMasterRecalc()">
                    </div>
                </div>

                <div class="p-price-summary">
                    <span id="m-info" class="p-price-note">STANDARD RATE</span>
                    <span class="p-price-total"><span id="m-final-sum">0</span> €</span>
                </div>

                <button class="btn-p-gold" onclick="proceedToMasterPay()">Update & Pay</button>
                <div onclick="location.reload()" style="text-align: center; margin-top: 15px; font-size: 11px; cursor: pointer; color: #888; text-transform: uppercase;">Cancel</div>
            </div>

            <div style="margin-top: 50px; text-align: left; border-top: 1px solid #eee; padding-top: 40px;">
                <?php echo do_shortcode('[formidable id=' . $a['form_id'] . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    // 4. JAVASCRIPT: Logic copied and renamed for Master safety
    function activateMasterForm() {
        document.getElementById('p-step-1').style.display = 'none';
        document.getElementById('purnukka-form-view').style.display = 'block';
        runMasterRecalc();
    }

    function runMasterRecalc() {
        const root = document.getElementById('p-master-logic');
        const base = parseInt(root.getAttribute('data-base'));
        const g = parseInt(document.getElementById('m-guests').value) || 0;
        let n = parseInt(document.getElementById('m-nights').value) || 0;
        const minN = parseInt(root.getAttribute('data-min'));
        
        if (n < minN) n = minN;

        let up = base;
        let note = "STANDARD RATE (" + base + "€/NIGHT)";

        // Logic from your production code
        if (n > 2 && n <= 6) { up = 20; note = "MID-TERM DISCOUNT (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { up = 15; note = "WEEKLY DISCOUNT (15€/NIGHT)"; }
        else if (n >= 14) { up = 10; note = "LONG-STAY DISCOUNT (10€/NIGHT)"; }

        document.getElementById('m-final-sum').innerText = g * n * up;
        document.getElementById('m-info').innerText = note;
    }

    function proceedToMasterPay() {
        const root = document.getElementById('p-master-logic');
        const pid = root.getAttribute('data-pid'); // Gets 276
        const total = document.getElementById('m-final-sum').innerText;
        
        // Checkout redirect
        window.location.href = window.location.origin + '/checkout/?add-to-cart=' + pid + '&quantity=' + total;
    }
    </script>

    <?php
    return ob_get_clean();
});