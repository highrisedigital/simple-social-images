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

/**
 * Function to run when the plugin is activated.
 */
function hd_ssi_on_activation() {

	// store the plugin version number on activation.
	update_option( 'hd_ssi_version', HD_SSI_VERSION );

	// if we have no template saved already.
	if ( empty( hd_ssi_get_template() ) ) {

		// set the template.
		update_option( 'hd_ssi_template', HD_SSI_LOCATION . '/templates/1.html' );

	}

	// if we have no text color.
	if ( empty( hd_ssi_get_text_color() ) ) {

		// set the default text color.
		update_option( 'hd_ssi_text_color', '#FFFFFF' );

	}

	// if we have no text bg color.
	if ( empty( hd_ssi_get_text_bg_color() ) ) {

		// set the default text bg color.
		update_option( 'hd_ssi_text_bg_color', '#B108AD' );

	}

	// if we have no bg color.
	if ( empty( hd_ssi_get_bg_color() ) ) {

		// set the default bg color.
		update_option( 'hd_ssi_bg_color', '#FB2767' );

	}

	// if we have no font weight.
	if ( empty( hd_ssi_get_font_weight() ) ) {

		// set the default bg color.
		update_option( 'hd_ssi_font_weight', '400' );

	}

	// flush the rewrite rules.
	flush_rewrite_rules();

}

register_activation_hook( __FILE__, 'hd_ssi_on_activation' );