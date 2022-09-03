<?php
/**
 * Plugin Name: Simple Social Images
 * Plugin URI: https://simplesocialimages.com
 * Description: Create automated, beautiful and branded images for posts in WordPress shared on social media channels. This plugin requires a license for <a href="https://simplesocialimages.com">Simple Social Images</a>.
 * Version: 0.2
 * Author: Highrise Digital
 * Author URI: https://highrise.digital/
 * Text Domain: simple-social-images
 * Domain Path: /languages/
 * License: GPL2+
 */

// define variable for path to this plugin file.
define( 'HD_SSI_LOCATION', dirname( __FILE__ ) );
define( 'HD_SSI_LOCATION_URL', plugins_url( '', __FILE__ ) );
define( 'HD_SSI_VERSION', '0.2' );

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
 * Sets up some options and flushes rewtire rules for the generate endpoint.
 */
function hd_ssi_on_activation() {

	// store the plugin version number on activation.
	update_option( 'hd_ssi_version', HD_SSI_VERSION );

	// add the option to redirect the user to settings.
	update_option( 'hd_ssi_activation_redirection', true );

	// add the option to force a permalink refresh.
	update_option( 'hd_ssi_plugin_permalinks_flushed', 0 );

	// get all of the registered settings.
	$settings = hd_ssi_get_settings();

	// if we have settings.
	if ( ! empty( $settings ) ) {

		// loop through each settings.
		foreach ( $settings as $setting ) {

			// if this setting does not have a default value.
			if ( empty( $setting['default_value'] ) ) {
				continue;
			}

			// create the default value.
			update_option( $setting['option_name'], $setting['default_value'] );

		}

	}

}

register_activation_hook( __FILE__, 'hd_ssi_on_activation' );

/**
 * Redirects the users to the settings screen, when the plugin is activated.
 */
function hd_ssi_redirect_to_settings_on_activation() {

	// if the plugin has just be activated.
	if ( get_option( 'hd_ssi_activation_redirection', false ) ) {

		// remove the activation option.
		delete_option( 'hd_ssi_activation_redirection' );

		// redirect the user to the settings page.
		wp_redirect( admin_url( 'options-general.php?page=hd-ssi-settings' ) );
		exit;

	}

}

add_action( 'admin_init', 'hd_ssi_redirect_to_settings_on_activation' );

/**
 * Flushes the redirect rules if the plugin is just activated.
 */
function hd_ssi_maybe_flush_rewrite_rules() {

	// if the rewrite rules have not been flushed.
	if ( 0 === absint( get_option( 'hd_ssi_plugin_permalinks_flushed', 1 ) ) ) {

		// flush the sites redirect rules.
		flush_rewrite_rules();

		// set the marker indicating that rewrite rules have been flushed.
        update_option( 'hd_ssi_plugin_permalinks_flushed', 1 );

	}

}

add_action( 'init', 'hd_ssi_maybe_flush_rewrite_rules', 99 );
