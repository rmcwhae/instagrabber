<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://russellmcwhae.ca
 * @since      1.0.0
 *
 * @package    Russell_Instagram_Fetcher
 * @subpackage Russell_Instagram_Fetcher/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h2><?php echo esc_html(get_admin_page_title()); ?></h2>


<form action='options.php' method='post' name='instagrabber-options'>

  <?php
  // Courtesy of https://scotch.io/tutorials/how-to-build-a-wordpress-plugin-part-1
  // Grab all options
  $options = get_option($this->plugin_name);

  // Isolate the two that we're concernec about
  $ig_username = $options['ig_username'];
  $ig_post_limit =  $options['ig_post_limit'];
  
  settings_fields($this->plugin_name);
  do_settings_sections($this->plugin_name);

  ?>

  Include the latest posts from the specified Instagram account in a page or post with the [instagrabber] shortcode.
  <table class="form-table" role="presentation">
    <tr>
      <th scope="row"><label for="<?php echo $this->plugin_name; ?>-ig_username">Instagram Username</label></th>
      <td> <input id="<?php echo $this->plugin_name; ?>-ig_username" type='text' name="<?php echo $this->plugin_name; ?>[ig_username]" value="<?php echo $ig_username; ?>">
      </td>
    </tr>
    <tr>
      <th scope="row"><label for="<?php echo $this->plugin_name; ?>-ig_post_limit">Number of Recent Posts to Display</label></th>
      <td> <input id="<?php echo $this->plugin_name; ?>-ig_post_limit" type='text' name="<?php echo $this->plugin_name; ?>[ig_post_limit]" value="<?php echo $ig_post_limit; ?>">
      </td>
    </tr>
  </table>
  <?php submit_button(); ?>
</form>