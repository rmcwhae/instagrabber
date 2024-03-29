<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://russellmcwhae.ca
 * @since             1.0.0
 * @package           Russell_Instagram_Fetcher
 *
 * @wordpress-plugin
 * Plugin Name:       Russell’s Instagrabber
 * Plugin URI:        https://github.com/rmcwhae/instagrabber
 * Description:       A simple Instagram fetcher.
 * Version:           1.0.0
 * Author:            Russell McWhae
 * Author URI:        https://russellmcwhae.ca
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       russell-instagram-fetcher
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('RUSSELL_INSTAGRAM_FETCHER_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-russell-instagram-fetcher-activator.php
 */
function activate_russell_instagram_fetcher()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-russell-instagram-fetcher-activator.php';
	Russell_Instagram_Fetcher_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-russell-instagram-fetcher-deactivator.php
 */
function deactivate_russell_instagram_fetcher()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-russell-instagram-fetcher-deactivator.php';
	Russell_Instagram_Fetcher_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_russell_instagram_fetcher');
register_deactivation_hook(__FILE__, 'deactivate_russell_instagram_fetcher');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-russell-instagram-fetcher.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_russell_instagram_fetcher()
{

	$plugin = new Russell_Instagram_Fetcher();
	$plugin->run();
}
run_russell_instagram_fetcher();
