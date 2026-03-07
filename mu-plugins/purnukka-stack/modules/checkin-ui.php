<?php
/**
 * Module: Check-in UI (v1.7.0)
 * Description: Dynaaminen vierashallinta ja saapumiskokemus. 
 * Fix: Hinnat luetaan dynaamisesti context.json-tiedostosta (Analyysin kohta 2).
 * File: checkin-ui.php
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Checkin_UI {
    public function __construct() {
        add_shortcode('purnukka_checkin', [$this, 'render_ui']);
    }

    public function render_ui() {
        // Varmistetaan core-yhteys (Analyysin kohta 3 mukainen valmistelu)
        $core = $GLOBALS['purnukka'] ?? null;
        if (!$core) return 'Purnukka Core not loaded.';

        $config = $core->config;
        
        // Luetaan dynaamiset hinnat tai käytetään alkuperäisiä oletuksia fallbackina
        $prices = $config['pricing']['extra_guests'] ?? [
            'adult'  => 30,
            'child'  => 20,
            'infant' => 15,
            'pet'    => 10
        ];

        ob_start();
        ?>
        <style>
            /* SÄILYTETTY: Kaikki alkuperäiset tyylit */
            .purnukka-checkin-container { font-family: 'Inter', sans-serif; max-width: 600px; margin: auto; color: #1d2327; }
            .guest-row { display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #eee; }
            .price-tag { font-weight: bold; color: #2271b1; }
            .total-section { margin-top: 20px; padding: 20px; background: #f6f7f7; border-radius: 8px; text-align: right; font-size: 20px; }
        </style>

        <div class="purnukka-checkin-container">
            <div id="guest-selector">
                <h2>Ketä on tulossa?</h2>
                
                <div class="guest-row" data-type="adult">
                    <span>Aikuiset (<span class="price-tag"><?php echo esc_html($prices['adult']); ?>€</span>)</span>
                    <div class="controls">
                        <button type="button" class="minus">-</button>
                        <span class="count">0</span>
                        <button type="button" class="plus">+</button>
                    </div>
                </div>

                <div class="guest-row" data-type="child">
                    <span>Lapset (<span class="price-tag"><?php echo esc_html($prices['child']); ?>€</span>)</span>
                    <div class="controls">
                        <button type="button" class="minus">-</button>
                        <span class="count">0</span>
                        <button type="button" class="plus">+</button>
                    </div>
                </div>

                <div class="guest-row" data-type="infant">
                    <span>Sylivauvat (<span class="price-tag"><?php echo esc_html($prices['infant']); ?>€</span>)</span>
                    <div class="controls">
                        <button type="button" class="minus">-</button>
                        <span class="count">0</span>
                        <button type="button" class="plus">+</button>
                    </div>
                </div>

                <div class="guest-row" data-type="pet">
                    <span>Lemmikit (<span class="price-tag"><?php echo esc_html($prices['pet']); ?>€</span>)</span>
                    <div class="controls">
                        <button type="button" class="minus">-</button>
                        <span class="count">0</span>
                        <button type="button" class="plus">+</button>
                    </div>
                </div>

                <div class="total-section">
                    Yhteensä: <span id="total-price">0€</span>
                </div>
            </div>
        </div>

        <script>
        (function($) {
            // Injektoidaan PHP:lla haetut dynaamiset hinnat JavaScriptiin (Analyysin korjaus)
            const guestPrices = <?php echo json_encode($prices); ?>;

            $(document).ready(function() {
                $('.plus, .minus').on('click', function() {
                    const row = $(this).closest('.guest-row');
                    const type = row.data('type');
                    let count = parseInt(row.find('.count').text());

                    if ($(this).hasClass('plus')) {
                        count++;
                    } else if (count > 0) {
                        count--;
                    }

                    row.find('.count').text(count);
                    updateTotal();
                });

                function updateTotal() {
                    let total = 0;
                    $('.guest-row').each(function() {
                        const type = $(this).data('type');
                        const count = parseInt($(this).find('.count').text());
                        total += count * (guestPrices[type] || 0);
                    });
                    $('#total-price').text(total + '€');
                }
            });
        })(jQuery);
        </script>
        <?php
        return ob_get_clean();
    }
}

new Purnukka_Checkin_UI();