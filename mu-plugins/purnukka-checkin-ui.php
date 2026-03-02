<?php
/**
 * Plugin Name: Purnukka Check-in Master (Sky High Version)
 * Description: Forced top alignment. All branding restored. Product ID 276.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* PAKOTETTU TEEMAN NOLLAUS TÄLLE SIVULLE */
        .site-content, .entry-content, .post-inner {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        /* 1. PREMIUM HEADER - NOSTETTU */
        .purnukka-welcome-header {
            background: #ffffff;
            padding: 20px 20px 10px 20px; /* Minimalistinen täyte */
            text-align: center;
            margin-top: -50px !important; /* Vetää headerin aivan ylös */
        }

        .p-brand-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #b89b5e;
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .purnukka-welcome-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
        }

        /* 2. MASTER CONTAINER - NOSTETTU PYSTYPANKIN TASOLLE */
        .purnukka-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: -20px auto 40px auto !important; /* Negatiivinen marginaali nostoon */
            padding: 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            position: relative;
            z-index: 999;
        }

        .p-top-icon { color: #b89b5e; font-size: 36px; margin-bottom: 15px; display: block; }

        /* 3. VAIHE 1: ALOITUSLAATIKKO */
        .p-step-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 6px solid #b89b5e; 
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 20px;
        }

        .btn-p-dark {
            background: #1a2b28; color: #fff; border: none; padding: 14px 25px;
            font-weight: bold; text-transform: uppercase; font-size: 11px;
            cursor: pointer; transition: 0.3s; white-space: nowrap;
        }

        /* 4. VAIHE 2: LASKURI */
        #purnukka-form-view { display: none; margin-top: 25px; text-align: left; }

        .p-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .p-input-field {
            background: #fff;
            border: 1px solid #b89b5e;
            padding: 15px;
        }

        .p-input-field label {
            display: block; font-size: 10px; color: #888;
            text-transform: uppercase; margin-bottom: 5px; font-weight: bold;
        }

        .p-input-field input {
            border: none; width: 100%; font-weight: bold;
            font-size: 22px; color: #1a2b28; outline: none; background: transparent;
        }

        .p-price-summary {
            border-top: 2px solid #f8f8f8;
            padding-top: 20px;
            margin-bottom: 25px;
            text-align: center;
        }

        .p-price-note { font-size: 11px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
        .p-price-total { font-size: 40px; font-weight: bold; color: #1a2b28; display: block; }

        .btn-p-gold {
            background: #b89b5e; color: #fff; border: none; padding: 18px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 13px;
        }

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
        <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin: 0 0 10px 0; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 6px;">Traveler Declaration & Check-in</h2>
        
        <div class="p-step-box" id="p-step-1">
            <div>
                <strong style="color: #1a2b28; font-size: 16px;">Has your group size changed?</strong><br>
                <span style="font-size: 12px; color: #666;">Add and pay for additional guests here.</span>
            </div>
            <button class="btn-p-dark" onclick="activateForm()">Yes, add guests</button>
        </div>

        <div id="purnukka-form-view">
            <div class="p-input-row">
                <div class="p-input-field">
                    <label><i class="fas fa-users"></i> Additional guests</label>
                    <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
                </div>
                <div class="p-input-field">
                    <label><i class="fas fa-moon"></i> Nights of stay</label>
                    <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
                </div>
            </div>

            <div class="p-price-summary">
                <span id="p-info" class="p-price-note">STANDARD RATE (30€/NIGHT)</span>
                <span class="p-price-total"><span id="p-final-sum">60</span> €</span>
            </div>

            <button class="btn-p-gold" onclick="proceedToPay()">Update and Pay</button>
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
        if (n > 2 && n <= 6) up = 20;
        else if (n > 6 && n <= 13) up = 15;
        else if (n >= 14) up = 10;
        document.getElementById('p-final-sum').innerText = g * n * up;
    }
    function proceedToPay() {
        const val = document.getElementById('p-final-sum').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + val;
    }
    </script>

    <?php
    return ob_get_clean();
});