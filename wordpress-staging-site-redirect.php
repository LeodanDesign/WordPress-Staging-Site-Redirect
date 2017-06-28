<?php
/*
Plugin Name: WordPress Staging Site Redirect
Plugin URI: https://leodandesign.co.uk
Description: Stops Search Engines Indexing Staging Sites & Redirects To Main Site
Author: Ben Matthews / Leodan:Design
Author URI: https://benjaminmatthews.me.uk
Version: 1.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function wp_staging_add_meta_noindex() {
	$meta_tag = '<meta name="robots" content="noindex">';
	echo $meta_tag;
}

add_action('wp_head', 'wp_staging_add_meta_noindex', 1);

function wp_staging_redirect() {
	if (!isset($_GET['guest_mode']) ) {
		if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_user_logged_in() && !is_admin()) {
			$plugin_options = get_option( 'wp_staging_site_settings' );
			$main_site_url = $plugin_options['wp_staging_site_main_site_url'];
			$main_site_url = rtrim($main_site_url ,"/");
			wp_redirect($main_site_url . $_SERVER['REQUEST_URI'], 301);
			exit;
		}
	}
} 

add_action('init', 'wp_staging_redirect');


add_action( 'admin_menu', 'wp_staging_site_add_admin_menu' );
add_action( 'admin_init', 'wp_staging_site_settings_init' );


function wp_staging_site_add_admin_menu(  ) { 

	add_options_page( 'WP Staging Site', 'WP Staging Site', 'manage_options', 'wp_staging_site', 'wp_staging_site_options_page' );

}


function wp_staging_site_settings_init(  ) { 

	register_setting( 'wp_staging_site', 'wp_staging_site_settings' );

	add_settings_section(
		'wp_staging_site_wp_staging_site_section', 
		__( 'Configure The Redirects On The Staging Site', 'wp_staging_site' ), 
		'wp_staging_site_settings_section_callback', 
		'wp_staging_site'
	);

	add_settings_field( 
		'wp_staging_site_main_site_url', 
		__( 'Main Site URL', 'wp_staging_site' ), 
		'wp_staging_site_main_site_url_render', 
		'wp_staging_site', 
		'wp_staging_site_wp_staging_site_section' 
	);


}


function wp_staging_site_main_site_url_render(  ) { 

	$options = get_option( 'wp_staging_site_settings' );
	?>
	<input type='url' name='wp_staging_site_settings[wp_staging_site_main_site_url]' value='<?php echo $options['wp_staging_site_main_site_url']; ?>'>
	<?php

}


function wp_staging_site_settings_section_callback(  ) { 

	echo __( 'Enter the details of the main site that this site reflects.', 'wp_staging_site' );

}


function wp_staging_site_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>WP Staging Site</h2>

		<?php
		settings_fields( 'wp_staging_site' );
		do_settings_sections( 'wp_staging_site' );
		?>
		<p>To access the site without logging in, add ?guest_mode to the end of your URL.</p>
		<?php
		submit_button();
		?>

	</form>
	<?php

}

?>
