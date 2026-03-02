<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master)
 * Description: International Master Standard UI with built-in styling container.
 * Version: 1.4.0
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    $a = shortcode_atts(array(
        'price'      => '30',
        'minimum'    => '2',
        'product_id' => '276',
        'form_id'    => '4',
        'title'      => 'Traveler Declaration'
    ), $atts);

    ob_start(); ?>

    <style>
        :root {
            --p-primary: var(--purnukka-primary, #1a2b28);
            --p-accent: var(--purnukka-accent, #b89b5e);
            --p-bg-light: #fdfdfd;
        }

        /* TÄMÄ ON SE RATKAISU: Koodi luo oman raamin sivun sisälle */
        .p-master-app-container {
            max-width: 850px; /* Sopiva leveys, ettei teksti leviä liikaa */
            margin: 60px auto; /* Keskittää koodin sivulla */
            padding: 60px 50px; /* Tuo sitä Elementorin ilmavuutta */
            background: #ffffff; 
            border-radius: 4px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.06); /* Tyylikäs varjo */
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

        .p-form-divider { 
            height: 1px; background: #eee; margin: 60px 0 40px; position: relative; 
        }

        @media (max-width: 650px) {
            .p-master-app-container { margin: 20px; padding: 40px 25px; }
            .p-master-box { flex-direction: column; text-align: center; gap: 20px; }
        }
    </style>

    <div class="p-master-app-container"> <div id="p-master-app" 
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

                <div id="p-master-form" style="display:none;">
                     </div>

                <div class="p-form-divider"></div>

                <div style="text-align: left;">
                    <?php 
                    if ( class_exists( 'FrmFormsController' ) ) {
                        echo FrmFormsController::get_form_shortcode( array( 'id' => $a['form_id'] ) );
                    }
                    ?>
                </div>
            </div>
        </div>
    </div> <script>
        /* (Sama JS-logiikka kuin aiemmin...) */
    </script>

    <?php
    return ob_get_clean();
});