<?php
/**
 * Adds the meta boxes required for the plugin.
 */

/**
 * Registers the Simple Social Images meta box.
 * Displays the meta box on the jobs post edit screen.
 */
function hd_ssi_add_ssi_jobs_meta_box() {

	// if we have no supported post types.
	if ( empty( hd_ssi_get_supported_post_types() ) ) {
		return;
	}

	add_meta_box(
		'hd_ssi',
		__( 'Simple Social Images', 'simple-social-image' ),
		'hd_ssi_jobs_meta_box_output',
		hd_ssi_get_supported_post_types(),
		'normal',
		'default'
	);

}

add_action( 'add_meta_boxes', 'hd_ssi_add_ssi_jobs_meta_box' );

/**
 * Output the contents of the Simple Social Images meta box.
 */
function hd_ssi_jobs_meta_box_output( $post ) {

	// if this is not a published post.
	if ( $post->post_status !== 'publish' ) {
		
		?>
		<p><?php esc_html_e( 'This job has not been published yet. Before you generate an image for social sharing for this job, please enter the job data and publish the job. Once you have done this, refresh this page to generate your image.', 'simple-social-images-wpjm' ); ?></p>
		<?php

	} else {

		// default social image id.
		$social_image_id = 0;

		// get the current social image for this post.
		$social_image_id = hd_ssi_has_image( $post->ID );

		// get the URL for the current social image.
		$social_image_url = wp_get_attachment_image_url( $social_image_id, 'hd_ssi_image' );

		// if we don't have a social image URL.
		if ( empty ( $social_image_url ) ) {

			// set the url of the image to the placeholder.
			$social_image_url = HD_SSI_LOCATION_URL . '/assets/img/social-placeholder.jpg';

		}

		// build the rest endpoint url.
		$endpoint_url = get_rest_url( null, 'ssi/v1/getimage' );
		$endpoint_url = add_query_arg(
			array(
				'post_id' => $post->ID,
				'_wpnonce'   => wp_create_nonce( 'wp_rest' ),
			),
			$endpoint_url
		);

		?>
		<p><?php _e( 'Use the button below to generate or delete the social sharing image for this job. You can <a target="_blank" href="' . esc_url( home_url( '/ssi/v1/generate-html/?post_id=' . $post->ID ) ) . '">preview it</a> first if you like.', 'simpe-social-images-wpjm' ); ?></p>

		<?php

		// set class for the gernate button.
		$generate_button_class = array(
			'button-secondary',
			'generate-ssi-image-button',
		);

		// if there is a current social image set.
		if ( $social_image_id !== 0 ) {

			// add the hidden class.
			$generate_button_class[] = 'ssi-hidden';

		}

		?>
		
		<button class="<?php echo esc_attr( implode( ' ', $generate_button_class ) ); ?>" id="generate-ssi-image" data-endpoint-url="<?php echo esc_url( $endpoint_url ); ?>">
			<?php esc_html_e( 'Generate Social Sharing Image', 'simple-social-images' ); ?>
		</button>

		<?php

		// set class for the delete button.
		$delete_button_class = array(
			'button-secondary',
			'delete-ssi-image-button',
			'ssi-hidden',
		);

		// if there is a current social image set.
		if ( $social_image_id !== 0 ) {

			// we should be showing the button so remove the hide class.
			$key = array_search( 'ssi-hidden', $delete_button_class, true );

			// if we have found the class.
			if ( $key !== false ) {

				// remove the class from the array.
				unset( $delete_button_class[ $key ] );

			}

		}

		// output the delete button.
		?>

		<button class="<?php echo esc_attr( implode( ' ', $delete_button_class ) ); ?>" id="delete-ssi-image" data-endpoint-url="<?php echo esc_url( get_rest_url( null, 'wp/v2/media/' ) ); ?>" data-media-id="<?php echo esc_attr( $social_image_id ); ?>" data-placeholder-img="<?php echo esc_url( HD_SSI_LOCATION_URL . '/assets/img/social-placeholder.jpg' ); ?>">
				<?php esc_html_e( 'Remove Social Sharing Image', 'simple-social-images' ); ?>
			</button>

		<?php

		?>

		<img class="hd-ssi-spinner" src="<?php echo esc_url( admin_url( '/images/spinner.gif' ) ); ?>" />

		<?php

		?>

		<img class="ssi-image" src="<?php echo esc_url( $social_image_url ); ?>" data-image-id="<?php echo esc_attr( absint( $social_image_id ) ); ?>" />

		<?php

	}

}
