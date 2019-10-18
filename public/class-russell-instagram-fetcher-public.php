<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://russellmcwhae.ca
 * @since      1.0.0
 *
 * @package    Russell_Instagram_Fetcher
 * @subpackage Russell_Instagram_Fetcher/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Russell_Instagram_Fetcher
 * @subpackage Russell_Instagram_Fetcher/public
 * @author     Russell McWhae <russell.mcwhae@gmail.com>
 */
class Russell_Instagram_Fetcher_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_shortcode('instagrabber', array($this, 'instagrabber_get_ig_posts'));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Russell_Instagram_Fetcher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Russell_Instagram_Fetcher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/russell-instagram-fetcher-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Russell_Instagram_Fetcher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Russell_Instagram_Fetcher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/russell-instagram-fetcher-public.js', array('jquery'), $this->version, false);
	}

		/**
		 * This function makes the Instagram API call and outputs the formatted images, dates, and descriptions appropriately
		 *
		 */

	public function instagrabber_get_ig_posts($atts)
	{
		$output = '';

		// Grab all options
		$options = get_option($this->plugin_name);

		// Isolate the two that we're concernec about
		$ig_username = $options['ig_username'];
		$ig_post_limit =  $options['ig_post_limit'];
		// $ig_username = $options['instagrabber_field_username']; // grab the username from WordPress options
		// $ig_post_limit = $options['instagrabber_field_num_posts']; // grab the post limit from WordPress options
		if (!$ig_username) {
			return '<p>Error: No Instagram account name provided. Please provide one in Admin > Settings > Instagrabber.</p>';
		}
		if (!$ig_post_limit) {
			$ig_post_limit = 5; // set default to 5 if not specified rather than throw an error
		}
		// $output = "My IG handle is " . $ig_username . " and here are my latest " . $ig_post_limit . " posts:";
		$url = 'https://www.instagram.com/' . $ig_username . '/?__a=1'; // could make this more elegant/RESTful…
		$response = file_get_contents($url);
		if ($response) {
			$response = json_decode($response, true); // 2nd arg 'true' forces array to be returned, not object

			$posts = $response['graphql']['user']['edge_owner_to_timeline_media']['edges']; // grab the array that we actually care about; also this data is assumed to be sorted reverse chronologically (not verified here)
			array_splice($posts, $ig_post_limit); // chop off what we don't need (wasteful, I know)
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
}
