<?php
/**
 * Plugin Name: Purnukka Check-in Master (The Final Everything Version)
 * Description: Ultra-compact BUT includes all texts, icons, and branding. Product ID 276.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* PAKOTETTU TIIVISTYS JOTTA MAHDU RUUTUUN */
        .site-content, .entry-content, .post-inner { padding-top: 0 !important; margin-top: 0 !important; }

        .purnukka-welcome-header {
            background: #ffffff;
            padding: 20px 20px 5px 20px; 
            text-align: center;
            margin-top: -65px !important;
        }

        .p-brand-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #b89b5e;
            font-weight: bold;
            display: block;
        }

        .purnukka-welcome-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 26px; 
            color: #1a2b28;
            margin: 0;
        }

        .purnukka-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 750px;
            margin: 0 auto !important;
            padding: 20px 40px; 
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 15px 40px rgba(0,0,0,0.06);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            z-index: 999;
            position: relative;
        }

        .p-top-icon { color: #b89b5e; font-size: 24px; margin-bottom: 10px; display: block; }

        /* ALOITUSLAATIKKO */
        .p-step-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 5px solid #b89b5e; 
            padding: 15px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 15px;
        }

        .btn-p-dark {
            background: #1a2b28; color: #fff; border: none; padding: 10px 20px;
            font-weight: bold; text-transform: uppercase; font-size: 10px;
            cursor: pointer;
        }

        /* TIIVISTETYT SYÖTTÖLAATIKOT IKONEILLA */
        #purnukka-form-view { display: none; margin-top: 15px; text-align: left; }

        .p-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .p-input-field {
            background: #fff;
            border: 1px solid #b89b5e;
            padding: 8px 12px;
        }

        .p-input-field label {
            display: block; font-size: 8px; color: #888;
            text-transform: uppercase; font-weight: bold; margin-bottom: 2px;
        }

        .p-input-field label i { color: #b89b5e; margin-right: 5px; }

        .p-input-field input {
            border: none; width: 100%; font-weight: bold;
            font-size: 18px; color: #1a2b28; outline: none; background: transparent;
            height: 22px;
        }

        .p-price-summary {
            border-top: 1px solid #f0f0f0;
            padding-top: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        .p-price-note { font-size: 9px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
        .p-price-total { font-size: 34px; font-weight: bold; color: #1a2b28; display: block; line-height: 1; }

        .btn-p-gold {
            background: #b89b5e; color: #fff; border: none; padding: 16px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 12px;
        }

        .btn-cancel-link {
            text-align: center; margin-top: 10px; font-size: 10px; cursor: pointer;
            color: #aaa; text-transform: uppercase; display: block;
        }

        @media (max-width: 650px) {
            .p-input-row { grid-template-columns: 1fr; }
            .p-step-box { flex-direction: column; text-align: center; gap: 10px; }
        }
    </style>

    <div class="purnukka-welcome-header">
        <span class="p-brand-label">Purnukka Group</span>
        <h1>Welcome home</h1>
    </div>

    <div class="purnukka-premium-wrapper">
        <i class="fas fa-key p-top-icon"></i> 
        <h2 style="font-family: 'Playfair Display', serif; font-size: 20px; color: #1a2b28; margin: 0 0 5px 0; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 3px;">Traveler Declaration & Check-in</h2>
        
        <p style="font-size: 12px; color: #666; margin: 10px auto 20px auto; max-width: 600px; line-height: 1.5; text-align: center;">
            A legal traveler declaration ensures your safety and guarantees that your insurance coverage remains valid throughout your entire stay.
        </p>

        <div class="p-step-box" id="p-step-1">
            <div>
                <strong style="color: #1a2b28; font-size: 14px;">Has your group size changed?</strong><br>
                <span style="font-size: 11px; color: #666;">You can add and pay for additional guests here.</span>
            </div>
            <button class="btn-p-dark" onclick="activateForm()">Yes, add guests</button>
        </div>

        <div id="purnukka-form-view">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; color: #1a2b28; margin-bottom: 12px; text-align: left;">Add guests to your booking</h3>
            
            <div class="p-input-row">
                <div class="p-input-field">
                    <label><i class="fas fa-users"></i> Additional guests (qty)</label>
                    <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
                </div>
                <div class="p-input-field">
                    <label><i class="fas fa-moon"></i> Nights (stay)</label>
                    <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
                </div>
            </div>

            <div class="p-price-summary">
                <span id="p-info" class="p-price-note">STANDARD RATE (30€/NIGHT)</span>
                <span class="p-price-total"><span id="p-final-sum">60</span> €</span>
            </div>

            <button class="btn-p-gold" onclick="proceedToPay()">Update and Pay Now</button>
            <div class="btn-cancel-link" onclick="location.reload()">Cancel</div>
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
        const val = document.getElementById('p-final-sum').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + val;
    }
    </script>

    <?php
    return ob_get_clean();
});