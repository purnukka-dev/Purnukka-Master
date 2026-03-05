<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka Mail Connector - SMTP-hallinta ja viestien dynaaminen reititys.
 * Taso: Pomminvarma Master-muotti.
 */
class Purnukka_Mail_Connector {
    private $core;

    public function __construct($core) {
        $this->core = $core;
        
        // Aktivoi SMTP-pakotus PHPMAILER-initin kautta
        add_action('phpmailer_init', [$this, 'configure_smtp_delivery']);
        
        // Varmista lähettäjän tiedot dynaamisesti
        add_filter('wp_mail_from', [$this, 'set_custom_mail_from']);
        add_filter('wp_mail_from_name', [$this, 'set_custom_mail_from_name']);
    }

    /**
     * Konfiguroi SMTP-asetukset context.json:n technical-lohkon perusteella.
     */
    public function configure_smtp_delivery($phpmailer) {
        $tech = $this->core->get_context('technical', []);
        
        // Pomminvarma lukko: Jos host puuttuu, keskeytä ja käytä WP:n oletusta
        if (empty($tech['smtp_host'])) {
            return;
        }

        $phpmailer->isSMTP();
        $phpmailer->Host       = $tech['smtp_host'];
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = !empty($tech['smtp_port']) ? $tech['smtp_port'] : 587;
        $phpmailer->Username   = !empty($tech['smtp_user']) ? $tech['smtp_user'] : '';
        $phpmailer->Password   = !empty($tech['smtp_pass']) ? $tech['smtp_pass'] : '';
        $phpmailer->SMTPSecure = !empty($tech['smtp_encryption']) ? $tech['smtp_encryption'] : 'tls';
        
        // Estetään timeoutit hitailla servereillä
        $phpmailer->Timeout    = 30;
    }

    /**
     * Pakottaa lähettäjän sähköpostiosoitteen property_info:sta.
     */
    public function set_custom_mail_from($original_email) {
        $info = $this->core->get_context('property_info', []);
        if (!empty($info['email']) && is_email($info['email'])) {
            return $info['email'];
        }
        return $original_email;
    }

    /**
     * Pakottaa lähettäjän nimen property_info:sta.
     */
    public function set_custom_mail_from_name($original_name) {
        $info = $this->core->get_context('property_info', []);
        if (!empty($info['name'])) {
            return $info['name'];
        }
        return $original_name;
    }
}