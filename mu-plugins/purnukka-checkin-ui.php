<?php
/**
 * Plugin Name: Purnukka Check-in Master (Final Fix v1.7.0)
 * Description: Brutal layout fix to bypass Booklium container constraints.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* PAKOTETAAN SISÄLTÖ ULOS TEEMAN LAATIKOSTA */
        .p-master-brutal-fix {
            all: initial; /* Nollataan teeman perimät tyylit */
            display: block;
            font-family: 'Montserrat', sans-serif;
            width: calc(100% - 120px) !important; /* Jätetään tilaa pystypalkille */
            margin-left: 100px !important;
            background: #fff;
            box-sizing: border-box;
        }

        .purnukka-premium-wrapper {
            max-width: 800px !important;
            margin: 40px auto !important;
            padding: 50px 40px !important;
            background: #ffffff !important;
            text-align: center !important;
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06) !important;
            border: 1px solid #f0f0f0 !important;
            border-radius: 4px !important;
        }

        /* Korjataan rikkoutuneet syöttökentät */
        .p-input-grid {
            display: flex !important;
            justify-content: center !important;
            gap: 20px !important;
            margin-bottom: 30px !important;
        }

        .p-field {
            flex: 1 !important;
            border: 1px solid #b89b5e !important;
            padding: 15px !important;
            text-align: left !important;
        }

        .p-field label { display: block !important; font-size: 10px !important; color: #888 !important; text-transform: uppercase !important; font-weight: bold !important; margin-bottom: 5px !important; }
        .p-field input { border: none !important; width: 100% !important; font-size: 22px !important; font-weight: bold !important; outline: none !important; }

        .btn-update {
            background: #b89b5e !important;
            color: #fff !important;
            border: none !important;
            padding: 20px !important;
            width: 100% !important;
            font-weight: bold !important;
            text-transform: uppercase !important;
            cursor: pointer !important;
        }

        @media (max-width: 900px) {
            .p-master-brutal-fix { width: 100% !important; margin-left: 0 !important; }
            .p-input-grid { flex-direction: column !important; }
        }
    </style>

    <div class="p-master-brutal-fix">
        
        <div style="text-align: center; padding: 60px 0 20px 0;">
            <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 4px; color: #b89b5e; font-weight: bold;">Purnukka Group</span>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 42px; color: #1a2b28; margin: 10px 0;">Welcome Home</h1>
        </div>

        <div class="purnukka-premium-wrapper">
            <i class="fas fa-key" style="color: #b89b5e; font-size: 36px; margin-bottom: 20px; display: block;"></i>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin-bottom: 30px; border-bottom: 2px solid #b89b5e; display: inline-block;">Check-in & Declaration</h2>

            <div id="p-form-area">
                <div class="p-input-grid">
                    <div class="p-field">
                        <label>Extra Guests</label>
                        <input type="number" id="p-guests" value="1" min="1" oninput="recalc()">
                    </div>
                    <div class="p-field">
                        <label>Nights</label>
                        <input type="number" id="p-nights" value="2" min="2" oninput="recalc()">
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <span id="p-price-display" style="font-size: 48px; font-weight: bold; color: #1a2b28;">60</span> 
                    <span style="font-size: 24px; font-weight: bold; color: #1a2b28;">€</span>
                </div>

                <button class="btn-update" onclick="pay()">Update & Pay Now</button>
            </div>

            <div style="margin-top: 50px; text-align: left;">
                <?php echo do_shortcode('[formidable id=4]'); ?>
            </div>
        </div>
    </div>

    <script>
    function recalc() {
        const g = parseInt(document.getElementById('p-guests').value) || 0;
        const n = parseInt(document.getElementById('p-nights').value) || 0;
        document.getElementById('p-price-display').innerText = g * n * 30;
    }
    function pay() {
        const total = document.getElementById('p-price-display').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + total;
    }
    </script>

    <?php
    return ob_get_clean();
});