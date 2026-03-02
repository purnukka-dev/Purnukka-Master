<?php
/**
 * Plugin Name: Purnukka Check-in Master (Ultra-Compact)
 * Description: Minimalist heights to fit one screen. All branding kept. Product ID 276.
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
            padding: 10px 20px; 
            text-align: center;
            margin-top: -65px !important; /* Nostaa aivan yläreunaan */
        }

        .p-brand-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #b89b5e;
            font-weight: bold;
        }

        .purnukka-welcome-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px; 
            color: #1a2b28;
            margin: 0;
        }

        .purnukka-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 700px; /* Kapeampi, jotta pysyy ryhdikkäänä */
            margin: 0 auto !important;
            padding: 15px 30px; 
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 10px 30px rgba(0,0,0,0.05);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            z-index: 999;
            position: relative;
        }

        .p-top-icon { color: #b89b5e; font-size: 20px; margin-bottom: 5px; display: block; }

        /* TIIVISTETTY ALOITUSLAATIKKO */
        .p-step-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 4px solid #b89b5e; 
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 10px;
        }

        .btn-p-dark {
            background: #1a2b28; color: #fff; border: none; padding: 8px 15px;
            font-weight: bold; text-transform: uppercase; font-size: 9px;
            cursor: pointer;
        }

        /* ULTRA-TIIVISTETYT SYÖTTÖLAATIKOT */
        #purnukka-form-view { display: none; margin-top: 10px; text-align: left; }

        .p-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .p-input-field {
            background: #fff;
            border: 1px solid #b89b5e;
            padding: 6px 10px; /* Puristettu minimiin */
        }

        .p-input-field label {
            display: block; font-size: 8px; color: #888;
            text-transform: uppercase; font-weight: bold; margin-bottom: 0px;
        }

        .p-input-field input {
            border: none; width: 100%; font-weight: bold;
            font-size: 16px; color: #1a2b28; outline: none; background: transparent;
            height: 24px; /* Pakotettu matala korkeus */
        }

        .p-price-summary {
            border-top: 1px solid #f0f0f0;
            padding-top: 8px;
            margin-bottom: 10px;
            text-align: center;
        }

        .p-price-note { font-size: 8px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
        .p-price-total { font-size: 28px; font-weight: bold; color: #1a2b28; display: block; line-height: 1; }

        .btn-p-gold {
            background: #b89b5e; color: #fff; border: none; padding: 12px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 11px;
        }

        .btn-cancel-link {
            text-align: center; margin-top: 8px; font-size: 9px; cursor: pointer;
            color: #aaa; text-transform: uppercase; display: block;
        }
    </style>

    <div class="purnukka-welcome-header">
        <span class="p-brand-label">Purnukka Group</span>
        <h1>Welcome home</h1>
    </div>

    <div class="purnukka-premium-wrapper">
        <i class="fas fa-key p-top-icon"></i> 
        <h2 style="font-family: 'Playfair Display', serif; font-size: 18px; color: #1a2b28; margin: 0 0 5px 0; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 2px;">Traveler Declaration & Check-in</h2>
        
        <div class="p-step-box" id="p-step-1">
            <div>
                <strong style="color: #1a2b28; font-size: 13px;">Group size changed?</strong><br>
                <span style="font-size: 10px; color: #888;">Add and pay for additional guests.</span>
            </div>
            <button class="btn-p-dark" onclick="activateForm()">Add guests</button>
        </div>

        <div id="purnukka-form-view">
            <div class="p-input-row">
                <div class="p-input-field">
                    <label>Guests (qty)</label>
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