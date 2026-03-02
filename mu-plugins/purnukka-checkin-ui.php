<?php
/**
 * Plugin Name: Purnukka Check-in Master (Production Version 1.6.0)
 * Description: English UI, Product 276, Optimized for Booklium Theme.
 * Version: 1.6.0
 * Author: Purnukka Group
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* 1. MASTER WRAPPER - Antaa tilaa pystypalkille */
        .p-master-layout-fix {
            font-family: 'Montserrat', sans-serif;
            width: 100%;
            max-width: 100%;
            padding-left: 100px; /* Tämä jättää tilan "MASTER-VILLA-NAME" palkille */
            padding-right: 40px;
            box-sizing: border-box;
            margin: 0 auto;
        }

        /* 2. HEADER - Puhdas brändäys */
        .p-header-section {
            padding: 80px 20px 40px 20px;
            text-align: center;
        }

        .p-brand-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #b89b5e;
            font-weight: bold;
            display: block;
            margin-bottom: 12px;
        }

        .p-main-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 6vw, 48px);
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
            letter-spacing: -1px;
        }

        /* 3. PREMIUM CONTENT BOX - Kuten Villa Purnukassa */
        .purnukka-premium-wrapper {
            max-width: 850px;
            margin: 0 auto 100px auto; 
            padding: 50px 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 25px 60px rgba(0,0,0,0.07);
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            position: relative;
        }

        .p-top-icon { color: #b89b5e; font-size: 40px; margin-bottom: 25px; display: block; }

        .p-step-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 8px solid #b89b5e; 
            padding: 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 40px;
            gap: 20px;
        }

        /* 4. DYNAMIC FORM VIEW */
        #purnukka-form-view {
            display: none;
            margin-top: 40px;
            text-align: left;
            animation: fadeIn 0.4s ease-out;
        }

        .p-input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .p-input-field {
            background: #fff;
            border: 1px solid #b89b5e;
            padding: 18px;
        }

        .p-input-field label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .p-input-field input {
            border: none;
            width: 100%;
            font-weight: bold;
            font-size: 24px;
            color: #1a2b28;
            outline: none;
            background: transparent;
        }

        .p-price-summary {
            border-top: 2px solid #f8f8f8;
            padding-top: 30px;
            margin-bottom: 35px;
            text-align: center;
        }

        .p-price-note { font-size: 11px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
        .p-price-total { font-size: 50px; font-weight: bold; color: #1a2b28; display: block; }

        .btn-p-gold {
            background: #b89b5e; color: #fff; border: none; padding: 20px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 14px; letter-spacing: 1px;
        }

        .btn-p-dark {
            background: #1a2b28; color: #fff; border: none; padding: 15px 25px;
            font-weight: bold; text-transform: uppercase; font-size: 11px;
            cursor: pointer; white-space: nowrap;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* MOBILE OPTIMIZATION */
        @media (max-width: 900px) {
            .p-master-layout-fix { padding-left: 20px; padding-right: 20px; }
            .p-step-box { flex-direction: column; text-align: center; }
            .p-input-row { grid-template-columns: 1fr; }
        }
    </style>

    <div class="p-master-layout-fix">
        
        <div class="p-header-section">
            <span class="p-brand-label">Purnukka Group</span>
            <h1 class="p-main-title">Welcome Home</h1>
        </div>

        <div class="purnukka-premium-wrapper">
            <i class="fas fa-key p-top-icon"></i> 
            <h2 style="font-family: 'Playfair Display', serif; font-size: 30px; color: #1a2b28; margin: 0 0 15px 0; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 8px;">Check-in & Declaration</h2>
            <p style="font-size: 14px; color: #666; margin-top: 15px; line-height: 1.6;">
                A mandatory traveler declaration ensures a safe stay and keeps your insurance coverage active throughout your visit.
            </p>

            <div class="p-step-box" id="p-step-1">
                <div>
                    <strong style="color: #1a2b28; font-size: 17px;">Change in group size?</strong><br>
                    <span style="font-size: 13px; color: #666;">Add and pay for additional guests here.</span>
                </div>
                <button class="btn-p-dark" onclick="activateForm()">Add Guests</button>
            </div>

            <div id="purnukka-form-view">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 24px; color: #1a2b28; margin-bottom: 25px;">Add guests to booking</h3>
                
                <div class="p-input-row">
                    <div class="p-input-field">
                        <label><i class="fas fa-users"></i> Extra Guests (qty)</label>
                        <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
                    </div>
                    <div class="p-input-field">
                        <label><i class="fas fa-moon"></i> Nights (stay)</label>
                        <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
                    </div>
                </div>

                <div class="p-price-summary">
                    <span id="p-info" class="p-price-note">Standard Rate (30€/night)</span>
                    <span class="p-price-total"><span id="p-final-sum">60</span> €</span>
                </div>

                <button class="btn-p-gold" onclick="proceedToPay()">Update & Pay Now</button>
                <div onclick="location.reload()" style="text-align: center; margin-top: 15px; font-size: 11px; cursor: pointer; color: #888; text-transform: uppercase;">Cancel</div>
            </div>

            <div style="margin-top: 80px; text-align: left; border-top: 1px solid #eee; padding-top: 60px;">
                <?php echo do_shortcode('[formidable id=4]'); ?>
            </div>
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
        let note = "Standard Rate (30€/night)";

        if (n > 2 && n <= 6) { up = 20; note = "Mid-term Rate (20€/night)"; }
        else if (n > 6 && n <= 13) { up = 15; note = "Weekly Rate (15€/night)"; }
        else if (n >= 14) { up = 10; note = "Long-stay Rate (10€/night)"; }

        document.getElementById('p-final-sum').innerText = g * n * up;
        document.getElementById('p-info').innerText = note;
    }

    function proceedToPay() {
        const sum = document.getElementById('p-final-sum').innerText;
        // Master Product 276
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});