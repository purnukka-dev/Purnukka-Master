<?php
/**
 * Plugin Name: Purnukka Check-in Master (Sidebar Compatible)
 * Description: UI that respects the Booklium Vertical Header.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
    /* PAKOTETAAN SIVU LEVIÄMÄÄN MASTERISSA */
    .p-master-layout-fix {
        width: 100vw !important;
        position: relative !important;
        left: 50% !important;
        right: 50% !important;
        margin-left: -50vw !important;
        margin-right: -50vw !important;
        padding-left: 80px; /* Tilaa sille pystypalkille */
        box-sizing: border-box;
    }

    .purnukka-premium-wrapper {
        font-family: 'Montserrat', sans-serif;
        max-width: 850px !important; /* Laskurin maksimileveys */
        margin: 0 auto 60px auto !important; 
        padding: 40px;
        background: #ffffff;
        text-align: center; 
        box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
        border-radius: 4px;
        border: 1px solid #f0f0f0;
    }
    
    /* Pidetään muu logiikka ja ulkoasu samana kuin aiemmin */
    .p-brand-label { font-size: 11px; text-transform: uppercase; letter-spacing: 4px; color: #b89b5e; font-weight: bold; display: block; margin-bottom: 10px; }
    .purnukka-welcome-header h1 { font-family: 'Playfair Display', serif; font-size: 42px; color: #1a2b28; margin: 0; text-align: center; }
    .p-step-box { background: #fdfdfd; border: 1px solid #1a2b28; border-left: 6px solid #b89b5e; padding: 30px; display: flex; align-items: center; justify-content: space-between; text-align: left; margin-top: 30px; }
    #purnukka-form-view { display: none; margin-top: 30px; text-align: left; }
    .btn-p-gold { background: #b89b5e; color: #fff; border: none; padding: 18px; width: 100%; font-weight: bold; text-transform: uppercase; cursor: pointer; }

    @media (max-width: 900px) { 
        .p-master-layout-fix { 
            margin-left: 0 !important; 
            margin-right: 0 !important; 
            left: 0 !important; 
            width: 100% !important;
            padding-left: 20px; 
        } 
    }
</style>

    <div class="p-master-layout-fix">
        <div style="text-align:center; padding: 40px 0;">
            <span class="p-brand-label">Purnukka Group</span>
            <h1 class="purnukka-welcome-header">Welcome Home</h1>
        </div>

        <div class="purnukka-premium-wrapper">
             <i class="fas fa-key" style="color: #b89b5e; font-size: 36px; margin-bottom: 20px;"></i>
             <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28;">Check-in & Declaration</h2>
             
             <div class="p-step-box" id="p-step-1">
                <div>
                    <strong style="color: #1a2b28;">Change in group size?</strong><br>
                    <span style="font-size: 12px; color: #666;">Add and pay for additional guests here.</span>
                </div>
                <button onclick="activateForm()" style="background: #1a2b28; color: #fff; border: none; padding: 10px 20px; cursor: pointer;">Add Guests</button>
            </div>

            <div id="purnukka-form-view">
                <div class="p-input-row">
                    <div class="p-input-field">
                        <label style="font-size: 10px; color: #888;">Guests</label>
                        <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()" style="width: 100%; border: none; font-size: 20px; font-weight: bold;">
                    </div>
                    <div class="p-input-field">
                        <label style="font-size: 10px; color: #888;">Nights</label>
                        <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()" style="width: 100%; border: none; font-size: 20px; font-weight: bold;">
                    </div>
                </div>
                <div style="text-align: center; margin: 20px 0;">
                    <span id="p-final-sum" style="font-size: 40px; font-weight: bold; color: #1a2b28;">60</span> €
                </div>
                <button class="btn-p-gold" onclick="proceedToPay()">Update & Pay Now</button>
            </div>

            <div style="margin-top: 40px; text-align: left;">
                <?php echo do_shortcode('[formidable id=4]'); ?>
            </div>
        </div>
    </div>

    <script>
    function activateForm() {
        document.getElementById('p-step-1').style.display = 'none';
        document.getElementById('purnukka-form-view').style.display = 'block';
    }
    function runRecalc() {
        const g = document.getElementById('p-guests').value;
        const n = document.getElementById('p-nights').value;
        document.getElementById('p-final-sum').innerText = g * n * 30;
    }
    function proceedToPay() {
        const sum = document.getElementById('p-final-sum').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});