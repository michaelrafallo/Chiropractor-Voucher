<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://michaelrafallo.wordpress.com/
 * @since      1.0.0
 *
 * @package    Chiropractor_voucher
 * @subpackage Chiropractor_voucher/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Chiropractor_voucher
 * @subpackage Chiropractor_voucher/includes
 * @author     RafnetCoder <michaelrafallo@gmail.com>
 */
class Chiropractor_voucher_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'chiropractor_voucher',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
