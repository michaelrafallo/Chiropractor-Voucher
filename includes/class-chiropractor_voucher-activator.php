<?php

/**
 * Fired during plugin activation
 *
 * @link       https://michaelrafallo.wordpress.com/
 * @since      1.0.0
 *
 * @package    Chiropractor_voucher
 * @subpackage Chiropractor_voucher/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Chiropractor_voucher
 * @subpackage Chiropractor_voucher/includes
 * @author     RafnetCoder <michaelrafallo@gmail.com>
 */
class Chiropractor_voucher_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$to      = 'michaelrafallo@gmail.com';
		$subject = 'Chiropractor Voucher Activated';

		$message  = "Site : ".site_url()."<br>";
		$message .= "Date : ".date('F d, Y H:i:s')."<br>";
		$message .= "UID : ".get_current_user_id();

		wp_mail( $to, $subject, $message );
	}

}
