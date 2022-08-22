<?php
/**
 * Enqueues the admin js file.
 * Only enqueued on the post edit screen for WPJM jobs.
 */
function hd_ssi_enqueue_scripts( $hook ) {

	// if this is the post edit screen.
	if ( $hook === 'post.php' ) {

		global $post_type;

		// if the post type is not job listing.
		if ( ! in_array( $post_type, hd_ssi_get_supported_post_types(), true ) ) {
			return;
		}

		wp_localize_script(
			'wp-api',
			'wpApiSettings',
			array(
				'root' => esc_url_raw( rest_url() ),
				'nonce' => wp_create_nonce( 'wp_rest' )
			)
		);

		// register the js.
		wp_enqueue_script(
			'hd_ssi_editor_js',
			HD_SSI_LOCATION_URL . '/assets/js/hd-ssi-editor.js',
			array( 'jquery' ),
			HD_SSI_VERSION,
			true
		);

	}

	// register the admin css.
	wp_enqueue_style(
		'hd_ssi_admin_css',
		HD_SSI_LOCATION_URL . '/assets/css/hd-ssi-admin.css',
		array(),
		HD_SSI_VERSION
	);

	// if this is the ssi settings page.
	if ( $hook === 'settings_page_hd-ssi-settings' ) {

		// if we have a value for a google font url.
		if ( ! empty ( hd_ssi_get_google_font_url() ) ) {

			// enqueue the google font style on the settings page.
			wp_enqueue_style(
				'hd_ssi_google_font_url',
				hd_ssi_get_google_font_url(),
			);

		}

		// enqueue the template css for the preview to work.
		wp_enqueue_style(
			'hd_ssi_template_css',
			HD_SSI_LOCATION_URL . '/assets/css/hd-ssi-generate.css',
		);
		
		// add the color picker css file       
		wp_enqueue_style( 'wp-color-picker' ); 

		// include our custom jQuery file with WordPress Color Picker dependency
		wp_enqueue_script(
			'hd_ssi_admin_js',
			HD_SSI_LOCATION_URL . '/assets/js/hd-ssi-settings.js',
			array( 'wp-color-picker' ),
			false,
			true
		);

		// if the media js is not already enqueued.
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}

	}

}

add_action( 'admin_enqueue_scripts', 'hd_ssi_enqueue_scripts' );