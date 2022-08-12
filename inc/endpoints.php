<?php
/**
 * Registers the SSI API endpoints in WordPress.
 */
function hd_ssi_register_endpoints() {

	// add the endpoint for social image generator.
	add_rewrite_endpoint(
		'ssi/v1/generate-html', // this is the endpoint part of the url.
		EP_ROOT,
		'hd_ssi_generate' // this is var that is set when the endpoint is reached.
	);

}

add_action( 'init', 'hd_ssi_register_endpoints' );

/**
 * Makes sure that the endpoint variable has a true value when set.
 *
 * @param array $vars The current query vars.
 */
function hd_ssi_fix_api_endpoint_requests( $vars ) {

	// if the endpoint var is set.
	if ( isset( $vars['hd_ssi_generate'] ) ) {

		// make sure it is always equal to true.
		$vars['hd_ssi_generate'] = true;

	} else { // if the endpoint var is not set.

		// make sure it always is unset completely and not empty.
		unset( $vars['hd_ssi_generate'] );

	}

	// return modified vars.
	return $vars;

}

add_filter( 'request', 'hd_ssi_fix_api_endpoint_requests' );

/**
 * When the endpoint for generate is visited load the correct template file.
 *
 * @param  string $template The current template WordPress will load from the theme.
 * @return string           The modified tempalte string WordPress will load.
 */
function hd_ssi_load_generate_html_endpoint_template( $template ) {

	// check the endpoint var is set to true - if not pass back original template.
	if ( true !== get_query_var( 'hd_ssi_generate' ) ) {
		return $template;
	}

	// check for a app push template file in the theme folder.
	if ( file_exists( STYLESHEETPATH . '/ssi/generate-html.php' ) ) {

		// load the file from the theme folder.
		$template = STYLESHEETPATH . '/ssi/generate-html.php';

	} else { // file not in theme folder.

		// load the timetables file from the plugin.
		$template = HD_SSI_LOCATION . '/endpoints/generate-html.php';

	}

	return $template;

}

add_filter( 'template_include', 'hd_ssi_load_generate_html_endpoint_template' );

/**
 * Registers a custom rest endpoint which gets the image for a post.
 */
function hd_ssi_register_rest_endpoint() {

	// register a new rest route or endpoint for getting slot posts.
	register_rest_route(
		'ssi/v1',
		'/getimage/',
		array(
			'methods'             => 'GET',
			'callback'            => 'hd_ssi_generate_endpoint_output',
			'permission_callback' => '__return_true',
		)
	);

}

add_action( 'rest_api_init', 'hd_ssi_register_rest_endpoint' );

/**
 * Callback function used for the registered rest route for getting latest post content.
 *
 * @param  \WP_REST_Request $request The paramters passed to the endpoint url.
 * @return mixed                     THe HTML outputs for the requested slots posts.
 */
function hd_ssi_generate_endpoint_output( \WP_REST_Request $request ) {

	// if no post ID is present.
	if ( empty( $request['post_id'] ) ) {
		
		// output error.
		return array(
			'success' => false,
			'error' => __( 'No post ID provided.', 'simple-social-image-wpjm' ),
		);

	}

	return hd_ssi_generate_social_image( $request['post_id'] );

}
