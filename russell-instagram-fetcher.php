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
 * Add the admin menu; adapted from: https://www.sitepoint.com/wordpress-settings-api-build-custom-admin-page/
 */
// add_action('admin_menu', 'instagrabber_admin_menu');
/**
 * Create two options: IG username, and # of posts
 */
add_action('admin_init', 'instagrabber_settings_init');
/**
 * Create the shortcode
 */
add_shortcode('instagrabber', 'instagrabber_get_ig_posts');

function instagrabber_get_ig_posts($atts)
{
	$output = '';
	$options = get_option('instagrabber_settings');
	$ig_handle = $options['instagrabber_field_username']; // grab the username from WordPress options
	$post_limit = $options['instagrabber_field_num_posts']; // grab the post limit from WordPress options
	if (!$ig_handle) {
		return '<p>Error: No Instagram account name provided. Please provide one in Admin > Settings > Instagrabber.</p>';
	}
	if (!$post_limit) {
		$post_limit = 5; // set default to 5 if not specified rather than throw an error
	}
	// $output = "My IG handle is " . $ig_handle . " and here are my latest " . $post_limit . " posts:";
	$url = 'https://www.instagram.com/' . $ig_handle . '/?__a=1'; // could make this more elegant/RESTful…
	$response = file_get_contents($url);
	if ($response) {
		$response = json_decode($response, true); // 2nd arg 'true' forces array to be returned, not object

		$posts = $response['graphql']['user']['edge_owner_to_timeline_media']['edges']; // grab the array that we actually care about; also this data is assumed to be sorted reverse chronologically (not verified here)
		array_splice($posts, $post_limit); // chop off what we don't need (wasteful, I know)
		// var_dump($posts);
		foreach ($posts as $key => $value) {
			$thumbnail_url = $posts[$key]['node']['thumbnail_src'];
			$img_description = $posts[$key]['node']['edge_media_to_caption']['edges'][0]['node']['text'];
			$timestamp = $posts[$key]['node']['taken_at_timestamp'];
			$friendly_date = date_i18n(get_option('date_format'), $timestamp); // date formatted according to WordPress options; courtesy of: https://wordpress.stackexchange.com/questions/229474/converting-unix-timestamp-to-wordpress-date

			$output .= "<div class='rm-instagrabber'><img src=" . $thumbnail_url . " />";
			$output .= '<p>' . $img_description . ' <time class="entry-date published">' . $friendly_date . '</time></p></div>';
		};
	} else {
		return '<p>Error: Instagram account not found</p>';
	}
	return $output;
}

// function add_instagrabber_stylesheet()
// {
// 	wp_register_style('rm-instagrabber-styles', plugin_dir_url(__FILE__) . 'public/css/russell-instagram-fetcher-public.css');
// 	wp_enqueue_style('rm-instagrabber-styles');
// }

// add_action('wp_print_styles', 'add_instagrabber_stylesheet'); // from https://www.dummies.com/web-design-development/wordpress/enhance-wordpress-plugins-css-javascript/

// function instagrabber_admin_menu()
// {
// 	add_options_page('Instagrabber Settings', 'Instagrabber', 'manage_options', 'instagrabber-settings-page', 'instagrabber_settings_page'); // puts options in Settings > Instagrabber
// }

function instagrabber_settings_init()
{
	register_setting('instagrabberPlugin', 'instagrabber_settings');
	add_settings_section(
		'instagrabberPlugin_section',
		__('', 'wordpress'),
		'instagrabber_settings_section_callback',
		'instagrabberPlugin'
	);

	add_settings_field(
		'instagrabber_field_username',
		__('Instagram Username', 'wordpress'),
		'instagrabber_field_username_render',
		'instagrabberPlugin',
		'instagrabberPlugin_section'
	);

	add_settings_field(
		'instagrabber_field_num_posts',
		__('Number of Recent Posts to Display', 'wordpress'),
		'instagrabber_field_num_posts_render',
		'instagrabberPlugin',
		'instagrabberPlugin_section'
	);
}

function instagrabber_field_username_render()
{
	$options = get_option('instagrabber_settings');
	?>
	<input type='text' name='instagrabber_settings[instagrabber_field_username]' value='<?php echo $options['instagrabber_field_username']; ?>'>
<?php
}

function instagrabber_field_num_posts_render()
{
	$options = get_option('instagrabber_settings');
	?>
	<input type='text' name='instagrabber_settings[instagrabber_field_num_posts]' value='<?php echo $options['instagrabber_field_num_posts']; ?>'>
<?php
}

function instagrabber_settings_section_callback()
{
	echo __('Include the latest posts from the specified Instagram account in a page or post with the [instagrabber] shortcode.', 'wordpress');
}

function instagrabber_settings_page()
{
	?>
	<form action='options.php' method='post'>

		<h2>Instagrabber Settings</h2>

		<?php
			settings_fields('instagrabberPlugin');
			do_settings_sections('instagrabberPlugin');
			submit_button();
			?>

	</form>
<?php
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
