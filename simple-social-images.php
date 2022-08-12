<?php
/**
 * Plugin Name: Simple Social Images
 * Plugin URI: https://simplesocialimages.com
 * Description: Create automated, beautiful and branded images for posts in WordPress shared on social media channels. This plugin requires a license for <a href="https://simplesocialimages.com">Simple Social Images</a>.
 * Version: 1.0
 * Author: Highrise Digital
 * Author URI: https://highrise.digital/
 * Text Domain: simple-social-images
 * Domain Path: /languages/
 * License: GPL2+
 */

// define variable for path to this plugin file.
define( 'HD_SSI_LOCATION', dirname( __FILE__ ) );
define( 'HD_SSI_LOCATION_URL', plugins_url( '', __FILE__ ) );
define( 'HD_SSI_VERSION', '1.0' );

/**
 * Function to run on plugins load.
 */
function hd_ssi_wpjm_plugins_loaded() {

	$locale = apply_filters( 'plugin_locale', get_locale(), 'simple-social-images' );
	load_textdomain( 'simple-social-images', WP_LANG_DIR . '/simple-social-images/simple-social-images-' . $locale . '.mo' );
	load_plugin_textdomain( 'simple-social-images', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );

}

add_action( 'plugins_loaded', 'hd_ssi_wpjm_plugins_loaded' );

// load in the loader file which loads everything up.
require_once( dirname( __FILE__ ) . '/inc/loader.php' );
