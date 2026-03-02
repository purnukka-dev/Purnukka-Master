<?php
/**
 * Plugin Name: Purnukka Check-in Master (Ultra-Light)
 * Description: Calculator only. Form removed to fix screen height. Product ID 276.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* 1. HEADER - TIIVISTETTY */
        .purnukka-welcome-header {
            background: #ffffff;
            padding: 30px 20px 20px 20px; /* Vähemmän tyhjää ylhäällä */
            text-align: center;
        }

        .p-brand-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #b89b5e;
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .purnukka-welcome-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
        }

        /* 2. DASHBOARD WRAPPER - MATALA PROFIILI */
        .purnukka-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: -10px auto 20px auto; 
            padding: 25px 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 15px 40px rgba(0,0,0,0.05);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }

        .p-top-icon { color: #b89b5e; font-size: 28px; margin-bottom: 15px; display: block; }

        /* 3. STEP 1: INITIAL STATE */
        .p-step-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 6px solid #b89b5e; 
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 15px;
            gap: 15px;
        }

        .btn-p-dark {
            background: #1a2b28; color: #fff; border: none; padding: 12px 20px;
            font-weight: bold; text-transform: uppercase; font-size: 10px;
            cursor: pointer; transition: 0.3s ease; white-space: nowrap;
        }

        /* 4. STEP 2: CALCULATOR (2+1 Layout) */
        #purnukka-form-view {
            display: none;
            margin-top: 20px;
            text-align: left;
            animation: fadeIn 0.3s ease-out;
        }

        .p-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .p-input-field {
            background: #fff;
            border: 1px solid #b89b5e;
            padding: 12px;
        }

        .p-input-field label {
            display: block; font-size: 9px; color: #888;
            text-transform: uppercase; font-weight: bold; margin-bottom: 3px;
        }

        .p-input-field input {
            border: none; width: 100%; font-weight: bold;
            font-size: 20px; color: #1a2b28; outline: none; background: transparent;
        }

        .p-price-summary {
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .p-price-note { font-size: 9px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
        .p-price-total { font-size: 36px; font-weight: bold; color: #1a2b28; display: block; }

        .btn-p-gold {
            background: #b89b5e; color: #fff; border: none; padding: 16px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 12px; transition: 0.3s;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        @media (max-width: 650px) {
            .p-input-row { grid-template-columns: 1fr; }
            .p-step-box { flex-direction: column; text-align: center; }
        }
    </style>

    <div class="purnukka-welcome-header">
        <span class="p-brand-label">Purnukka Group</span>
        <h1>Welcome home</h1>
    </div>

    <div class="purnukka-premium-wrapper">
        <i class="fas fa-key p-top-icon"></i> 
        <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; color: #1a2b28; margin-bottom: 5px;">Traveler Declaration & Check-in</h2>
        <p style="font-size: 12px; color: #666; margin-bottom: 20px;">A legal declaration ensures your insurance coverage remains valid.</p>

        <div class="p-step-box" id="p-step-1">
            <div>
                <strong style="color: #1a2b28; font-size: 15px;">Has your group size changed?</strong><br>
                <span style="font-size: 11px; color: #888;">Add and pay for additional guests here.</span>
            </div>
            <button class="btn-p-dark" onclick="activateForm()">Yes, add guests</button>
        </div>

        <div id="purnukka-form-view">
            <div class="p-input-row">
                <div class="p-input-field">
                    <label>Additional guests</label>
                    <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
                </div>
                <div class="p-input-field">
                    <label>Nights of stay</label>
                    <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
                </div>
            </div>

            <div class="p-price-summary">
                <span id="p-info" class="p-price-note">STANDARD RATE (30€/NIGHT)</span>
                <span class="p-price-total"><span id="p-final-sum">60</span> €</span>
            </div>

            <button class="btn-p-gold" onclick="proceedToPay()">Update and Pay</button>
            <div onclick="location.reload()" style="text-align: center; margin-top: 12px; font-size: 10px; cursor: pointer; color: #aaa; text-transform: uppercase;">Cancel</div>
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
        const g = document.getElementById('p-guests').value;
        let n = parseInt(document.getElementById('p-nights').value);
        if (n < 2) n = 2;
        let up = 30;
        if (n > 2 && n <= 6) up = 20;
        else if (n > 6 && n <= 13) up = 15;
        else if (n >= 14) up = 10;
        
        const finalVal = g * n * up;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + finalVal;
    }
    </script>

    <?php
    return ob_get_clean();
});