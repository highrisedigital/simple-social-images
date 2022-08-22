<?php
// load in all the required inc files.
$includes_files = glob( plugin_dir_path( __FILE__ ) . '*.php' );

// if we have any includes files.
if ( ! empty( $includes_files ) ) {

	// loop through each file.
	foreach ( $includes_files as $includes_file ) {

		// if this file in the loop is this file we are now in.
		if ( strpos( $includes_file, 'loader.php' ) !== false ) {
			continue; // move to the next file.
		}

		// require this file in the plugin.
		require_once( $includes_file );

	}
}

// load in all the required plugin files.
$plugins_fies = glob( plugin_dir_path( __FILE__ ) . '/plugins/*.php' );

// if we have any plugin files.
if ( ! empty( $plugins_fies ) ) {

	// loop through each file.
	foreach ( $plugins_fies as $plugin_file ) {

		// require this file in the plugin.
		require_once( $plugin_file );

	}
}
