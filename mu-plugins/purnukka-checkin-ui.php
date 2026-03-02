<?php
/**
 * Plugin Name: Purnukka Check-in Master English
 * Description: Original 2-step logic, English language, ID 276 and Formidable 4.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .purnukka-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: 40px auto;
            padding: 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
        }

        .p-top-icon { color: #b89b5e; font-size: 36px; margin-bottom: 20px; display: block; }

        /* STEP 1: INITIAL BOX */
        .p-step-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 6px solid #b89b5e; 
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 30px;
            gap: 20px;
        }

        .btn-p-dark {
            background: #1a2b28;
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

        /* STEP 2: CALCULATOR FORM */
        #purnukka-form-view {
            display: none;
            margin-top: 30px;
            text-align: left;
            animation: fadeIn 0.4s ease-out;
        }

        .p-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .p-input-field {
            background: #fff;
            border: 1px solid #b89b5e;
            padding: 15px;
        }

        .p-input-field label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .p-input-field input {
            border: none;
            width: 100%;
            font-weight: bold;
            font-size: 22px;
            color: #1a2b28;
            outline: none;
            background: transparent;
        }

        .p-price-summary {
            border-top: 2px solid #f8f8f8;
            padding-top: 25px;
            margin-bottom: 30px;
            text-align: center;
        }

        .p-price-note { font-size: 11px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
        .p-price-total { font-size: 40px; font-weight: bold; color: #1a2b28; display: block; }

        .btn-p-gold {
            background: #b89b5e;
            color: #fff;
            border: none;
            padding: 18px;
            width: 100%;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            font-size: 13px;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 650px) {
            .p-step-box { flex-direction: column; text-align: center; }
            .p-input-row { grid-template-columns: 1fr; }
        }
    </style>

    <div class="purnukka-premium-wrapper">
        <i class="fas fa-key p-top-icon"></i> 
        <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin: 0 0 15px 0;">Traveler Declaration & Check-in</h2>
        <p style="font-size: 14px; color: #666; margin: 10px auto 30px auto; max-width: 650px; line-height: 1.6;">
            A legal traveler declaration ensures a safe stay and keeps your insurance coverage valid throughout your visit.
        </p>
        
        <div class="p-step-box" id="p-step-1">
            <div>
                <strong style="color: #1a2b28; font-size: 16px;">Has your group size changed?</strong><br>
                <span style="font-size: 12px; color: #666;">You can add and pay for additional guests here.</span>
            </div>
            <button class="btn-p-dark" onclick="activateForm()">Yes, add guests</button>
        </div>

        <div id="purnukka-form-view">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 22px; color: #1a2b28; margin-bottom: 20px;">Add guests to your booking</h3>
            
            <div class="p-input-row">
                <div class="p-input-field">
                    <label>Additional Guests (qty)</label>
                    <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
                </div>
                <div class="p-input-field">
                    <label>Nights (stay)</label>
                    <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
                </div>
            </div>

            <div class="p-price-summary">
                <span id="p-info" class="p-price-note">STANDARD RATE (30€/NIGHT)</span>
                <span class="p-price-total"><span id="p-final-sum">60</span> €</span>
            </div>

            <button class="btn-p-gold" onclick="proceedToPay()">Update and Pay</button>
            <div onclick="location.reload()" style="text-align: center; margin-top: 15px; font-size: 11px; cursor: pointer; color: #888; text-transform: uppercase;">Cancel</div>
        </div>

        <div style="margin-top: 50px; text-align: left; border-top: 1px solid #eee; padding-top: 30px;">
            <?php echo do_shortcode('[formidable id=4]'); ?>
        </div>
    </div>

    <script>
    function activateForm() {
        document.getElementById('p-step-1').style.display = 'none';
        document.getElementById('purnukka-form-view').style.display = 'block';
        runRecalc();
    }

    function runRecalc() {
        const g = parseInt(document.getElementById('p-guests').value) || 0;
        let n = parseInt(document.getElementById('p-nights').value) || 0;
        if (n < 2) n = 2;

        let up = 30;
        let note = "STANDARD RATE (30€/NIGHT)";

        if (n > 2 && n <= 6) { up = 20; note = "MID-STAY BENEFIT (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { up = 15; note = "WEEKLY BENEFIT (15€/NIGHT)"; }
        else if (n >= 14) { up = 10; note = "LONG-STAY BENEFIT (10€/NIGHT)"; }

        document.getElementById('p-final-sum').innerText = g * n * up;
        document.getElementById('p-info').innerText = note;
    }

    function proceedToPay() {
        const sum = document.getElementById('p-final-sum').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});