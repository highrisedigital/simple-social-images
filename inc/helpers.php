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
 * Gets all of the available template locations.
 *
 * @param boolean $names False to return an array of template locations.
 *                       True to return an array of filenames only.
 * @return array         An array of template locations (default) or names.
 */
function hd_ssi_get_templates() {

	// set the location of the templates.
	$template_locations = apply_filters(
		'hd_ssi_template_location',
		array(
			HD_SSI_LOCATION . '/templates/',
			STYLESHEETPATH . '/ssi/templates/',
		)

	);

	// to store template names in.
	$templates = array();

	// if we have template paths.
	if ( ! empty( $template_locations ) ) {

		// loop through each template location.
		foreach ( $template_locations as $template_location ) {

			// get all of the template files locations.
			$template_files = glob( $template_location . '*.html' );

			// loop through the template files locations.
			foreach( $template_files as $template_file ) {

				// add this file to the array.
				$templates[ $template_file ] = basename( $template_file, '.html' );

			}

		}

	}

	// sort the list numerically.
	asort( $templates );

	// return the templates.
	return $templates;

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
 * Gets the current Google font family.
 */
function hd_ssi_get_google_font_family() {

	// get the font family from settings.
	$font_family = get_option( 'hd_ssi_google_font_family' );

	// if the font family is empty.
	if ( empty( $font_family ) ) {

		// set to default system family for sans serif.
		$font_family = 'sans-serif;';

	}

	return apply_filters(
		'hd_ssi_google_font_family',
		$font_family
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
 * Gets the current active template selected.
 *
 * @return string The template name.
 */
function hd_ssi_get_template( $return = 'location' ) {

	// get the template.
	$template = get_option( 'hd_ssi_template' );

	// if we are returning the name only.
	if ( 'name' === $return ) {

		// get the template file name.
		$template = basename( $template );

		// remove the file type.
		$template = str_replace( '.html', '', $template );

	}

	return apply_filters(
		'hd_ssi_template',
		$template,
		$return
	);

}

/**
 * Gets the current title font size.
 */
function hd_ssi_get_font_size() {

	return apply_filters(
		'hd_ssi_font_size',
		get_option( 'hd_ssi_font_size' )
	);

}

/**
 * Gets the current title font weight.
 */
function hd_ssi_get_font_weight() {

	return apply_filters(
		'hd_ssi_font_weight',
		get_option( 'hd_ssi_font_weight' )
	);

}

/**
 * Gets the current title font style.
 */
function hd_ssi_get_font_style() {

	return apply_filters(
		'hd_ssi_font_style',
		get_option( 'hd_ssi_font_style' )
	);

}

/**
 * Gets the current title text alignment.
 */
function hd_ssi_get_text_alignment() {

	return apply_filters(
		'hd_ssi_text_align',
		get_option( 'hd_ssi_text_align' )
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
 * Gets the current title text alignment.
 */
function hd_ssi_is_template_reversed() {

	return apply_filters(
		'hd_ssi_template_reversed',
		absint( get_option( 'hd_ssi_template_reversed' ) )
	);

}

/**
 * Gets the current active text color.
 */
function hd_ssi_get_text_color() {

	return apply_filters(
		'hd_ssi_text_color',
		get_option( 'hd_ssi_text_color' )
	);

}

/**
 * Gets the current active text background color.
 */
function hd_ssi_get_text_bg_color() {

	return apply_filters(
		'hd_ssi_text_bg_color',
		get_option( 'hd_ssi_text_bg_color' )
	);

}

/**
 * Gets the current active background color.
 */
function hd_ssi_get_bg_color() {

	return apply_filters(
		'hd_ssi_bg_color',
		get_option( 'hd_ssi_bg_color' )
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
 * Gets the currently set logo size.
 */
function hd_ssi_get_logo_size() {

	return apply_filters(
		'hd_ssi_logo_size',
		get_option( 'hd_ssi_logo_size' )
	);

}
