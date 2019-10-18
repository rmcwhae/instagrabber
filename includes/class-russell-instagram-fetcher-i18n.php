<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://russellmcwhae.ca
 * @since      1.0.0
 *
 * @package    Russell_Instagram_Fetcher
 * @subpackage Russell_Instagram_Fetcher/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Russell_Instagram_Fetcher
 * @subpackage Russell_Instagram_Fetcher/includes
 * @author     Russell McWhae <russell.mcwhae@gmail.com>
 */
class Russell_Instagram_Fetcher_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'russell-instagram-fetcher',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
