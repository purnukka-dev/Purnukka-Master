<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master)
 * Description: International Master Standard UI with built-in styling container.
 * Version: 1.4.1
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    $a = shortcode_atts(array(
        'price'      => '30',
        'minimum'    => '2',
        'product_id' => '276',
        'form_id'    => '4', // FIXED MASTER ID
        'title'      => 'Traveler Declaration'
    ), $atts);

    ob_start(); ?>

    <style>
        :root {
            --p-primary: var(--purnukka-primary, #1a2b28);
            --p-accent: var(--purnukka-accent, #b89b5e);
            --p-bg-light: #fdfdfd;
        }

        /* THE CONTAINER: This creates the "Elementor-look" automatically */
        .p-master-app-container {
            max-width: 850px;
            margin: 60px auto;
            padding: 60px 50px;
            background: #ffffff;
            border-radius: 4px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.06);
            border: 1px solid #f2f2f2;
            font-family: 'Montserrat', sans-serif;
        }

        .p-master-header { text-align: center; margin-bottom: 50px; }
        .p-master-brand { 
            font-size: 10px; text-transform: uppercase; letter-spacing: 5px; 
            color: var(--p-accent); font-weight: 700; display: block; margin-bottom: 15px; 
        }
        .p-master-header h1 { 
            font-family: 'Playfair Display', serif; font-size: 38px; 
            color: var(--p-primary); margin: 0; font-weight: 400; 
        }

        .p-master-box { 
            background: #fbfbfb; border: 1px solid #eee; border-left: 5px solid var(--p-accent); 
            padding: 30px; display: flex; align-items: center; justify-content: space-between; 
            text-align: left; margin: 40px 0; 
        }

        .p-btn-dark { 
            background: var(--p-primary); color: #fff; border: none; padding: 18px 30px; 
            font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; 
            cursor: pointer; transition: 0.3s; 
        }
        .p-btn-dark:hover { background: var(--p-accent); }

        #p-master-form { 
            display: none; margin: 40px 0; text-align: left; padding-bottom: 40px; 
            border-bottom: 1px solid #eee; animation: pFade 0.4s;
        }

        .p-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .p-input-box { border: 1px solid #eee; padding: 15px; background: #fff; }
        .p-input-box label { display: block; font-size: 9px; color: var(--p-accent); text-transform: uppercase; font-weight: 700; margin-bottom: 5px; }
        .p-input-box input { border: none; width: 100%; font-weight: 700; font-size: 22px; color: var(--p-primary); outline: none; }
        
        .p-price-display { text-align: center; margin-bottom: 30px; }
        .p-price-total { font-size: 42px; font-weight: 700; color: var(--p-primary); display: block; }
        .p-price-note { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 1px; }

        .p-btn-gold { 
            background: var(--p-accent); color: #fff; border: none; padding: 20px; 
            width: 100%; font-weight: 700; text-transform: uppercase; cursor: pointer; 
            font-size: 13px; letter-spacing: 2px; transition: 0.3s;
        }
        .p-btn-gold:hover { background: var(--p-primary); }

        .p-form-divider { height: 1px; background: #eee; margin: 60px 0 40px; }

        @keyframes pFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 650px) {
            .p-master-app-container { margin: 20px; padding: 40px 25px; }
            .p-master-box { flex-direction: column; text-align: center; gap: 20px; }
            .p-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div class="p-master-app-container">
        <div id="p-master-app" 
             data-price="<?php echo esc_attr($a['price']); ?>" 
             data-min="<?php echo esc_attr($a['minimum']); ?>" 
             data-pid="<?php echo esc_attr($a['product_id']); ?>">
            
            <div class="p-master-header">
                <span class="p-master-brand">Purnukka Group</span>
                <h1><?php echo esc_html($a['title']); ?></h1>
                <div style="width: 50px; height: 1px; background-color: var(--p-accent); margin: 30px auto;"></div>
            </div>

            <div class="p-master-wrapper">
                <p style="font-size: 16px; color: #555; line-height: 1.8; margin-bottom: 40px;">
                    Please complete your traveler declaration to receive your access codes and arrival instructions.
                </p>

                <div class="p-master-box" id="p-gate-master">
                    <div>
                        <strong style="color: var(--p-primary); font-size: 18px;">Change in group size?</strong><br>
                        <span style="font-size: 14px; color: #888;">Add additional guests and pay for extra beds here.</span>
                    </div>
                    <button class="p-btn-dark" onclick="initPurnukkaMaster()">Add Guests</button>
                </div>

                <div id="p-master-form">
                    <div class="p-grid">
                        <div class="p-input-box">
                            <label>Additional Guests</label>
                            <input type="number" id="p-m-guests" value="1" min="1" oninput="recalcPurnukka()">
                        </div>
                        <div class="p-input-box">
                            <label>Nights</label>
                            <input type="number" id="p-m-nights" value="<?php echo esc_attr($a['minimum']); ?>" min="<?php echo esc_attr($a['minimum']); ?>" oninput="recalcPurnukka()">
                        </div>
                    </div>
                    <div class="p-price-display">
                        <span id="p-m-note" class="p-price-note">Standard Rate</span>
                        <span class="p-price-total"><span id="p-m-sum">0</span> €</span>
                    </div>
                    <button class="p-btn-gold" onclick="payPurnukka()">Update & Pay</button>
                    <div onclick="location.reload()" style="text-align: center; margin-top: 20px; font-size: 10px; cursor: pointer; color: #bbb; text-transform: uppercase; letter-spacing: 1px;">Cancel</div>
                </div>

                <div class="p-form-divider"></div>

                <div style="text-align: left;">
                    <?php 
                    if ( class_exists( 'FrmFormsController' ) ) {
                        echo FrmFormsController::get_form_shortcode( array( 'id' => $a['form_id'] ) );
                    } else {
                        echo "<p style='color:red;'>Formtool not active.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function initPurnukkaMaster() {
        document.getElementById('p-gate-master').style.display = 'none';
        document.getElementById('p-master-form').style.display = 'block';
        recalcPurnukka();
    }

    function recalcPurnukka() {
        const app = document.getElementById('p-master-app');
        const basePrice = parseInt(app.getAttribute('data-price'));
        const guests = parseInt(document.getElementById('p-m-guests').value) || 0;
        let nights = parseInt(document.getElementById('p-m-nights').value) || 0;
        const minNights = parseInt(app.getAttribute('data-min'));
        
        if (nights < minNights) nights = minNights;

        let currentPrice = basePrice;
        let rateNote = "STANDARD RATE (" + basePrice + "€/NIGHT)";
        
        if (nights > 2 && nights <= 6) { 
            currentPrice = 20; 
            rateNote = "MID-TERM DISCOUNT (20€/NIGHT)"; 
        } else if (nights > 6 && nights <= 13) { 
            currentPrice = 15; 
            rateNote = "WEEKLY DISCOUNT (15€/NIGHT)"; 
        } else if (nights >= 14) { 
            currentPrice = 10; 
            rateNote = "LONG-STAY DISCOUNT (10€/NIGHT)"; 
        }

        document.getElementById('p-m-sum').innerText = guests * nights * currentPrice;
        document.getElementById('p-m-note').innerText = rateNote;
    }

    function payPurnukka() {
        const app = document.getElementById('p-master-app');
        const productId = app.getAttribute('data-pid');
        const total = document.getElementById('p-m-sum').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=' + productId + '&quantity=' + total;
    }
    recalcPurnukka();
    </script>

    <?php
    return ob_get_clean();
});