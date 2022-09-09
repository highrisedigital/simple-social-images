<?php
/**
 * Plugin helper functions.
 */

/**
 * Gets the post types for the current site.
 *
 * @return array An array of post types where the key is the post type name
 *               and the value is the post type label.
 */
function hd_ssi_get_site_post_types() {

	// store the function return in here.
	$post_types = array();

	// get the sites post types.
	$site_post_types = get_post_types(
		array(
			'public' => true
		),
		'objects'
	);

	// remove attachments from the post type list.
	unset( $site_post_types['attachment'] );

	// if we have any post types.
	if ( ! empty( $site_post_types ) ) {

		// loop through each post type.
		foreach ( $site_post_types as $site_post_type ) {

			// add this post type to the array.
			$post_types[ $site_post_type->name ] = $site_post_type->label;

		}

	}

	// return the post types array.
	return apply_filters(
		'hd_ssi_site_post_types',
		$post_types
	);

}

/**
 * Gets an array of the posts types which simple social images is active for.
 *
 * @return array An array of support post type names or an empty array.
 */
function hd_ssi_get_supported_post_types() {

	// get the post types selected.
	$post_types = get_option( 'hd_ssi_post_types' );

	// if no post types are returned.
	if ( empty( $post_types ) ) {
		return array();
	}

	return apply_filters(
		'hd_hd_ssi_supported_post_types',
		$post_types
	);

}

/**
 * Checks whether a post has a current generated ssi image.
 *
 * @param  integer $post_id The post ID to check.
 * @return mixed   Zero if no image is present or the image ID is present.
 */
function hd_ssi_has_image( $post_id = 0 ) {

	// if we have no post id to check.
	if ( $post_id === 0 ) {

		// use current global post id.
		global $post;
		$post_id = $post->ID;

	}

	// get the image id stored as meta.
	$image_id = get_post_meta( $post_id, 'hd_ssi_image_id', true );

	// if we have no image id.
	if ( empty( $image_id ) ) {
		return 0;
	}

	// get the image url for the associated meta.
	$image_url = wp_get_attachment_image_url( $image_id, 'hd_ssi_image' );

	// if we have no image url.
	if ( $image_url === false ) {
		return 0;
	}

	// go this far, we must have an image.
	return apply_filters( 'hd_ssi_has_image', $image_id, $post_id );

}

/**
 * Returns the image url of a posts social image.
 *
 * @param  integer $post_id The ID of the post to return the image of. Defaults to current post.
 * @return string           The image URL or an empty string if the post does not have an image.
 */
function hd_ssi_get_image_url( $post_id = 0 ) {

	// if no post ID is provided.
	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	// if this post does not have an image.
	if ( 0 === hd_ssi_has_image( $post_id ) ) {
		return '';
	}

	// return the image url.
	return apply_filters(
		'hd_ssi_image_url',
		wp_get_attachment_image_url(
			get_post_meta( $post_id, 'hd_ssi_image_id', true ),
			'hd_ssi_image'
		),
		$post_id
	);

}

/**
 * Sorts an array by the order paramter.
 */
function hd_ssi_array_sort_by_order_key( $a, $b ) {
	
	// if no order paramter is provided.
	if ( ! isset( $a['order'] ) ) {

		// set the order to 10.
		$a['order'] = 10;

	}

	// if no order paramter is provided.
	if ( ! isset( $b['order'] ) ) {

		// set the order to 10.
		$b['order'] = 10;

	}

	// if the first array element is the same as the next.
	if ( $a['order'] === $b['order'] ) {
		return 0;
	}

	// return -1 is the first array element is less than the second, otherwise return 1.
	return ( $a['order'] < $b['order'] ) ? -1 : 1;

}

/**
 * Returns an array of all the registered settings.
 */
function hd_ssi_get_settings() {

	$settings = apply_filters(
		'hd_ssi_settings',
		array()
	);

	// sort the settings based on the order parameter.
	uasort( $settings, 'hd_ssi_array_sort_by_order_key' );

	// return the settings.
	return $settings;

}

/**
 * Returns an array of logo positions options.
 */
function hd_ssi_get_position_options() {

	return apply_filters(
		'hd_ssi_position_options',
		array(
			'top-left'       => __( 'Top Left', 'simple-social-images' ),
			'top-right'      => __( 'Top Right', 'simple-social-images' ),
			'top-center'     => __( 'Top Center', 'simple-social-images' ),
			'bottom-left'    => __( 'Bottom Left', 'simple-social-images' ),
			'bottom-right'   => __( 'Bottom Right', 'simple-social-images' ),
			'bottom-center'  => __( 'Bottom Center', 'simple-social-images' ),
			'middle-left'    => __( 'Middle Left', 'simple-social-images' ),
			'middle-right'   => __( 'Middle Right', 'simple-social-images' ),
			'middle-center'  => __( 'Middle Center', 'simple-social-images' ),
		)
	);

}

/**
 * Gets the current Google font URL.
 */
function hd_ssi_get_google_font_url() {

	return apply_filters(
		'hd_ssi_google_font_url',
		get_option( 'hd_ssi_google_font_url' )
	);

}

/**
 * Gets the current font family.
 */
function hd_ssi_get_font_family() {

	// get the font family from settings.
	$font_family = get_option( 'hd_ssi_font_family' );

	// if the font family is empty.
	if ( empty( $font_family ) ) {

		// set to default system family for sans serif.
		$font_family = 'sans-serif;';

	}

	return apply_filters(
		'hd_ssi_font_family',
		$font_family
	);

}

/**
 * Gets the current image position.
 */
function hd_ssi_get_image_position() {

	return apply_filters(
		'hd_ssi_image_position',
		get_option( 'hd_ssi_image_position' )
	);

}

/**
 * Gets the current image width.
 */
function hd_ssi_get_image_width() {

	return apply_filters(
		'hd_ssi_image_width',
		get_option( 'hd_ssi_image_width' )
	);

}

/**
 * Gets the current image height.
 */
function hd_ssi_get_image_height() {

	return apply_filters(
		'hd_ssi_image_height',
		get_option( 'hd_ssi_image_height' )
	);

}

/**
 * Gets the currently uploaded logo attachment IDs as an array.
 *
 * @return array An array of attachment IDs or an empty array if no images added.
 */
function hd_ssi_get_background_images() {

	// get the background images.
	$bg_images = get_option( 'hd_ssi_background_images' );

	// if we have no bg images.
	if ( empty( $bg_images ) ) {
		return array();
	}

	return apply_filters(
		'hd_ssi_background_images',
		explode( ',', $bg_images )
	);

}

/**
 * Grabs a random image ID from those added to the settings page.
 */
function hd_ssi_get_random_image_id() {

	// get the image ids from options.
	$images = hd_ssi_get_background_images();
	
	// if we have images.
	if ( ! empty( $images ) ) {

		$image_id_key = array_rand( $images, 1 );
		$image_id = $images[ $image_id_key ];

	} else {

		// set the image id to zero.
		$image_id = 0;
	
	}	

	return absint( $image_id );

}

/**
 * Gets the current title font size.
 */
function hd_ssi_get_title_font_size() {

	return apply_filters(
		'hd_ssi_title_font_size',
		get_option( 'hd_ssi_title_font_size' )
	);

}

/**
 * Gets the current title title weight.
 */
function hd_ssi_get_title_weight() {

	return apply_filters(
		'hd_ssi_title_weight',
		get_option( 'hd_ssi_title_weight' )
	);

}

/**
 * Gets the current title font style.
 */
function hd_ssi_get_title_style() {

	return apply_filters(
		'hd_ssi_title_style',
		get_option( 'hd_ssi_title_style' )
	);

}

/**
 * Gets the current title text alignment.
 */
function hd_ssi_get_title_alignment() {

	return apply_filters(
		'hd_ssi_title_align',
		get_option( 'hd_ssi_title_align' )
	);

}

/**
 * Gets the current title width.
 */
function hd_ssi_get_title_width() {

	return apply_filters(
		'hd_ssi_title_width',
		get_option( 'hd_ssi_title_width' )
	);

}

/**
 * Gets the current title margin.
 */
function hd_ssi_get_title_margin() {

	return apply_filters(
		'hd_ssi_title_margin',
		get_option( 'hd_ssi_title_margin' )
	);

}

/**
 * Gets the current title text transform.
 */
function hd_ssi_get_title_text_transform() {

	return apply_filters(
		'hd_ssi_title_text_transform',
		get_option( 'hd_ssi_title_text_transform' )
	);

}

/**
 * Gets the current active text color.
 */
function hd_ssi_get_title_color() {

	return apply_filters(
		'hd_ssi_title_color',
		get_option( 'hd_ssi_title_color' )
	);

}

/**
 * Gets the current title background color.
 */
function hd_ssi_get_title_background_color() {

	return apply_filters(
		'hd_ssi_title_bg_color',
		get_option( 'hd_ssi_title_bg_color' )
	);

}

/**
 * Gets the current title background type.
 */
function hd_ssi_get_title_background_type() {

	return apply_filters(
		'hd_ssi_title_background_type',
		get_option( 'hd_ssi_title_background_type' )
	);

}

/**
 * Gets the current title background type.
 */
function hd_ssi_get_title_background_gradient() {

	return apply_filters(
		'hd_ssi_title_background_gradient',
		get_option( 'hd_ssi_title_background_gradient' )
	);

}

/**
 * Gets the current title position.
 */
function hd_ssi_get_title_position() {

	return apply_filters(
		'hd_ssi_title_position',
		get_option( 'hd_ssi_title_position' )
	);

}

/**
 * Gets the current title text alignment.
 */
function hd_ssi_get_placeholder_title() {

	return apply_filters(
		'hd_ssi_placeholder_title',
		get_option( 'hd_ssi_placeholder_title' )
	);

}

/**
 * Checks whether a text colour has been set.
 *
 * @return integer 1 if a background colour is set and zero otherwise.
 */
function hd_ssi_has_title_color() {

	// default to no background.
	$output = 0;

	// get the text color.
	$text_color = hd_ssi_get_title_color();

	// if we have a text color set.
	if ( ! empty( $text_color ) ) {
		
		// set the output to true.
		$output = 1;
		
	}

	return apply_filters(
		'hd_ssi_has_title_color',
		absint( $output )
	);

}

/**
 * Gets the current active background color.
 */
function hd_ssi_get_background_color() {

	return apply_filters(
		'hd_ssi_background_color',
		get_option( 'hd_ssi_background_color' )
	);

}

/**
 * Checks whether a background colour has been set.
 *
 * @return integer 1 if a background colour is set and zero otherwise.
 */
function hd_ssi_has_background_color() {

	// default to no background.
	$output = 0;

	// get the background color.
	$bg_color = hd_ssi_get_background_color();

	// if we have a background color set.
	if ( ! empty( $bg_color ) ) {
		
		// set the output to true.
		$output = 1;
		
	}

	return apply_filters(
		'hd_ssi_has_background_color',
		absint( $output )
	);

}

/**
 * Returns 1 if logo is being used and zero otherwise.
 */
function hd_ssi_use_logo() {

	return apply_filters(
		'hd_ssi_use_logo',
		absint( get_option( 'hd_ssi_use_logo' ) )
	);

}

/**
 * Gets the currently uploaded logo attachment ID.
 */
function hd_ssi_get_logo_id() {

	return apply_filters(
		'hd_ssi_logo_id',
		get_option( 'hd_ssi_logo' )
	);

}

/**
 * Gets the currently uploaded logo URL.
 *
 * @return string An empty string if logo not being used or the logo URL.
 */
function hd_ssi_get_logo_url() {

	// get the logo usage status.
	$use_logo = hd_ssi_use_logo();

	// if we are not using a logo.
	if ( 0 === $use_logo ) {
		return '';
	}

	// get the URL of the logo added to settings.
	$logo_url = wp_get_attachment_image_url(
		hd_ssi_get_logo_id(),
		'full'
	);

	// if we don't have a logo added to settings.
	if ( empty( $logo_url ) ) {

		// set the logo URL to the default.
		$logo_url = HD_SSI_LOCATION_URL . '/assets/img/logo-placeholder.svg';

	}

	return apply_filters(
		'hd_ssi_logo_url',
		$logo_url
	);

}

/**
 * Gets the currently set logo size.
 */
function hd_ssi_get_logo_size() {

	return apply_filters(
		'hd_ssi_logo_size',
		get_option( 'hd_ssi_logo_size' )
	);

}

/**
 * Gets the currently set logo position.
 */
function hd_ssi_get_logo_position() {

	return apply_filters(
		'hd_ssi_logo_position',
		get_option( 'hd_ssi_logo_position' )
	);

}

/**
 * Gets the currently set logo position.
 */
function hd_ssi_get_logo_marginn() {

	return apply_filters(
		'hd_ssi_logo_margin',
		get_option( 'hd_ssi_logo_margin' )
	);

}

/**
 * Returns 1 if image is being used and zero otherwise.
 */
function hd_ssi_use_image() {

	return apply_filters(
		'hd_ssi_use_image',
		absint( get_option( 'hd_ssi_use_image' ) )
	);

}

/**
 * Gets the use featured images setting.
 */
function hd_ssi_use_featured_images() {

	return apply_filters(
		'hd_ssi_use_featured_image',
		absint( get_option( 'hd_ssi_use_featured_image' ) )
	);

}

/**
 * Returns the URL of the simple social images store.
 */
function hd_ssi_get_store_url() {
	return apply_filters(
		'hd_ssi_store_url',
		'https://simplesocialimages.com'
	);
}

/**
 * Returns the ID of the simple social images product in the store.
 */
function hd_ssi_get_store_product_id() {
	return apply_filters(
		'hd_ssi_get_store_product_id',
		361
	);
}

/**
 * Checks the status of the license key entered into the settings page.
 */
function hd_ssi_check_license_key() {

	// default the license status to invalid.
	$license_status = 'invalid';

	// setup some parameters to pass to the remote post request.
	$api_params = array(
		'edd_action' => 'check_license',
		'license'    => hd_ssi_get_license_key(),
		'item_id'    => hd_ssi_get_store_product_id(),
		'url'        => home_url()
	);

	// run the remote post to get license info.
	$response = wp_remote_post(
		hd_ssi_get_store_url(),
		array(
			'body'      => $api_params,
			'timeout'   => 15,
			'sslverify' => false
		)
	);

	// if the remote post did not error.
	if ( ! is_wp_error( $response ) ) {
		
		// convert the license data in the response from json to a php array.
		$license_data = json_decode(
			wp_remote_retrieve_body( $response )
		);

		// get the license status.
		$license_status = $license_data->license;
		
	}

	// update the database option with the license key status.
	update_option( 'hd_ssi_license_status', $license_status );

	// get the license status.
	return apply_filters(
		'hd_ssi_license_status',
		$license_status
	);

}

/**
 * Gets the license key added to settings.
 */
function hd_ssi_get_license_key() {

	return apply_filters(
		'hd_ssi_license_key',
		get_option( 'hd_ssi_license_key' )
	);

}

/**
 * Gets the license key status as stored from the last license check.
 */
function hd_ssi_get_license_key_status() {
	return apply_filters(
		'hd_ssi_license_key_status',
		get_option( 'hd_ssi_license_status' )
	);
}
