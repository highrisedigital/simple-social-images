<?php

/**
 * Returns the URL of the SSI API to generate an image.
 */
function hd_ssi_generate_api_url() {

	return apply_filters(
		'hd_ssi_generate_api_url',
		'https://simplesocialimages.com/ssi-api/v1/generate/'
	);

}

/**
 * Add the og:image size in WP.
 * Allows images to be cropped to og:image size.
 */
function hd_ssi_add_image_size() {

	// add the og:image size.
	add_image_size( 'hd_ssi_image', 1200, 630, true );

}

add_action( 'after_setup_theme', 'hd_ssi_add_image_size' );

/**
 * Outputs the custom variables on the settings page for the template preview.
 */
function hd_ssi_output_template_custom_properties() {

	// get all of the registered settings.
	$settings = hd_ssi_get_settings();

	// create an empty output var.
	$output = '';

	// if we have settings to output custom variables for.
	if ( ! empty( $settings ) ) {

		// loop through each setting.
		foreach ( $settings as $setting ) {

			// if this setting does not have a custom property declaration.
			if ( empty( $setting['custom_property'] ) ) {
				continue; // move to the next setting.
			}

			// get the setting value.
			$value = get_option( $setting['option_name'] );

			// if this has a value.
			if ( '' != $value ) {
				// output the custom variable for this settings.
				$output .= esc_attr( $setting['custom_property'] ) . ':' . wp_kses_post( $value ) . ';
				';

			}
			
		}

	}

	// if we have output
	if ( ! empty( $output ) ) {

		?>
		<style>
			.ssi-template {
				<?php echo wp_kses_post( $output ); ?>
			}
		</style>
		<?php

	}

}

/**
 * Generates the social media sharing image for a post.
 * Also saves the image to the media library and attaches it to the post.
 *
 * @param integer $post_id The ID of the post to generate an image for.
 * @return array           An array of image id and url on success
 *                         An error array with error message on failure.
 */
function hd_ssi_generate_social_image( $post_id = 0 ) {

	// if no post id then use the global post ID.
	if ( $post_id === 0 ) {
		global $post;
		$post_id = $post->ID;
	}

	// get the license key.
	$license_key = hd_ssi_get_license_key();

	// if we have no license key.
	if ( empty( $license_key ) ) {

		// output error.
		return array(
			'success' => false,
			'error'   => __( 'No license key provided.', 'simple-social-image-wpjm' ),
		);

	}

	$social_image_html_url = home_url( '/ssi/v1/generate-html/' );
	$social_image_html_url = add_query_arg(
		array(
			'post_id'   => absint( $post_id ),
			'timestamp' => time(),
		),
		$social_image_html_url
	);

	// set the URL of the simple social images api.
	$api_url = hd_ssi_generate_api_url();

	// add the paramters to the api_url.
	$api_url = add_query_arg(
		apply_filters(
			'hd_ssi_api_url_query_args',
			array(
				'license_key' => sanitize_text_field( $license_key ),
				'site_url'    => home_url(),
				'url'         => urlencode( $social_image_html_url ),
				'element'     => '.ssi-template',
				'ttl'         => 300,
			),
		),
		$api_url
	);

	// send the request to the api.
	$response = wp_remote_get(
		$api_url,
		array(
			'sslverify' => false,
			'timeout'   => 30,
		)
	);

	// if there was an error.
	if ( is_wp_error( $response ) ) {

		// output error.
		return array(
			'success' => false,
			'error'   => $response->get_error_message(),
		);

	}

	// get the body of the request, decoded as an array.
	$response = json_decode( wp_remote_retrieve_body( $response ), true );

	// if we have no url returned.
	if ( empty( $response['url'] ) ) {

		// output error.
		return $response;

	}

	// we are outside of WP Admin so need to include these files.
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	// grab the image and store in the media library.
	$image_id = media_sideload_image( $response['url'], absint( $post_id ), '', 'id' );

	// if we have an image set.
	if ( ! is_wp_error( $image_id ) ) {

		// save meta data indicating this is image generated by hd og images.
		update_post_meta( $image_id, 'hd_ssi_image', true );

		// get the current image id for the og:image.
		$current_image_id = get_post_meta( absint( $post_id ), 'hd_ssi_image_id', true );

		// if we have a current image.
		if ( ! empty( $current_image_id ) ) {

			// delete the attachment.
			wp_delete_attachment( $current_image_id );

		}

		// store the image ID as meta against the job.
		$result = update_post_meta( absint( $post_id ), 'hd_ssi_image_id', $image_id );

	}

	return apply_filters(
		'hd_ssi_generated_social_image',
		array(
			'success' => true,
			'id'      => $image_id,
			'url'     => wp_get_attachment_image_url( $image_id, 'ssi_image' ),
		),
		$post_id
	);

}

/**
 * Outputs the meta tags in the head for open graph and twitter images.
 */
function hd_ssi_render_tags() {

	// if this is not a single job.
	if ( ! is_singular( hd_ssi_get_supported_post_types() ) ) {
		return;
	}

	// get the social sharing image url.
	$ssi_image_url = hd_ssi_get_image_url();

	// if we have no URL.
	if ( '' === $ssi_image_url ) {
		return;
	}

	// set an array of tags to render.
	$tags = array();

	// if we are rendering open graph tags.
	if ( apply_filters( 'hd_ssi_render_og_image_tags', true ) === true ) {

		// merge the current tags with our tags array.
		$tags = array_merge(
			$tags,
			array(
				'og:image'        => $ssi_image_url,
				'og:image:width'  => 1200,
				'og:image:height' => 630,
			)
		);

	}

	// if we are rendering twitter tags.
	if ( apply_filters( 'hd_ssi_render_twitter_image_tags', true ) === true ) {

		// merge the current tags with our tags array.
		$tags = array_merge(
			$tags,
			array(
				'twitter:image' => $ssi_image_url,
				'twitter:card'  => 'summary_large_image',
			)
		);

	}

	// if we don't have any tags to output.
	if ( empty( $tags ) ) {
		return;
	}

	echo '<!-- Generated by Simple Social Images - https://simplesocialimages.com/ -->' . PHP_EOL;	

	// loop through each tag.
	foreach ( $tags as $property => $content ) {

		// create a filter name for this tag.
		$filter  = 'hd_ssi_meta_' . str_replace( ':', '_', $property );

		// filter the tag
		$content = apply_filters( $filter, $content );

		// set the meta tag to name for twitter and property for open graph.
		$label   = strpos( $property, 'twitter' ) === false ? 'property' : 'name';

		// if we have any content.
		if ( $content ) {

			// print the tag screen.
			printf(
				'<meta %1$s="%2$s" content="%3$s">' . PHP_EOL,
				esc_attr( $label ),
				esc_attr( $property ),
				esc_attr( $content )
			);

		}

	}

	echo '<!-- // Simple Social Images -->' . PHP_EOL;

}

add_action( 'wp_head', 'hd_ssi_render_tags' );

/**
 * Builds an array of classes for the template wrapper and returns them as a string.
 *
 * @return string The classes to add to the template wrapper div.
 */
function hd_ssi_output_template_wrapper_classes() {

	// create an array of wrapper classes.
	$classes = array(
		'ssi-template',
	);

	// if we have a background color set.
	if ( 1 === hd_ssi_has_background_color() ) {

		// add a background color class.
		$classes[] = 'ssi-template--has-bg-color';

	}

	// allow template classes to be filtered.
	$classes = apply_filters( 'hd_ssi_template_wrapper_classes', $classes );

	// return the classes string;
	return implode( ' ', $classes );

}

/**
 * Builds an array of classes for the template image and returns them as a string.
 *
 * @return string The classes to add to the template wrapper div.
 */
function hd_ssi_output_template_image_classes() {

	// create an array of classes.
	$classes = array(
		'ssi-template__image'
	);

	// get the image position setting.
	$image_position = hd_ssi_get_image_position();

	// if we have an image position.
	if ( ! empty( $image_position ) ) {

		// add classes for each position value.
		$classes[] = 'ssi--position--' . $image_position;

	}

	// if the image is hidden.
	if ( 0 === hd_ssi_use_image() ) {

		// add a hidden class.
		$classes[] = 'ssi-hidden';

	}

	// allow template classes to be filtered.
	$classes = apply_filters( 'hd_ssi_template_image_classes', $classes );

	// return the classes string;
	return implode( ' ', $classes );

}

/**
 * Builds an array of classes for the template logo and returns them as a string.
 *
 * @return string The classes to add to the template wrapper div.
 */
function hd_ssi_output_template_logo_classes() {

	// create an array of classes.
	$classes = array(
		'ssi-template__logo'
	);

	// get the logo position setting.
	$logo_position = hd_ssi_get_logo_position();

	// if we have an logo position.
	if ( ! empty( $logo_position ) ) {

		// add classes for each position value.
		$classes[] = 'ssi--position--' . $logo_position;

	}

	// if the logo is hidden.
	if ( 0 === hd_ssi_use_logo() ) {

		// add a hidden class.
		$classes[] = 'ssi-hidden';

	}

	// allow template classes to be filtered.
	$classes = apply_filters( 'hd_ssi_template_logo_classes', $classes );

	// return the classes string;
	return implode( ' ', $classes );

}

/**
 * Builds an array of classes for the template title and returns them as a string.
 *
 * @return string The classes to add to the template wrapper div.
 */
function hd_ssi_output_template_title_wrapper_classes() {

	// create an array of classes.
	$classes = array(
		'ssi-template__title-wrapper'
	);

	// get the title position setting.
	$title_position = hd_ssi_get_title_position();

	// if we have an title position.
	if ( ! empty( $title_position ) ) {

		// add classes for each position value.
		$classes[] = 'ssi--position--' . $title_position;

	}

	// if the title is hidden.
	if ( 0 === hd_ssi_use_title() ) {

		// add a hidden class.
		$classes[] = 'ssi-hidden';

	}

	// allow template classes to be filtered.
	$classes = apply_filters( 'hd_ssi_template_title_wrapper_classes', $classes );

	// return the classes string;
	return implode( ' ', $classes );

}

/**
 * Builds an array of classes for the template title and returns them as a string.
 *
 * @return string The classes to add to the template wrapper div.
 */
function hd_ssi_output_template_title_classes() {

	// create an array of classes.
	$classes = array(
		'ssi-template__title'
	);

	// get the title background type.
	$title_bg_type = hd_ssi_get_title_background_type();

	// add the background type class.
	$classes[] = 'ssi-background--' . $title_bg_type;

	// if the background type is gradient.
	if ( 'gradient' === $title_bg_type ) {

		// add a class.
		$classes[] = 'gradient--' . hd_ssi_get_title_background_gradient();

	}

	// allow template classes to be filtered.
	$classes = apply_filters( 'hd_ssi_template_title_classes', $classes );

	// return the classes string;
	return implode( ' ', $classes );

}

/**
 * Returns the background image for a particular post.
 *
 * @param integer $post_id The post ID to return the image of.
 * @return string          The URL of the image to use.
 */
function hd_ssi_get_post_background_image_url( $post_id = 0 ) {

	// get the status of whether we are using images.
	$use_image = hd_ssi_use_image();

	// if we have not using image.
	if ( 0 === $use_image ) {
		return '';
	}

	$image_url = '';

	// if the current post has a featured image.
	if ( has_post_thumbnail( $post_id ) && 1 === hd_ssi_use_featured_images() ) {

		// set the image URL to the featured image URL.
		$image_url = get_the_post_thumbnail_url( $post_id, 'hd_ssi_image' );

	} else {

		// set the image url to a random image from settings.
		$image_url = wp_get_attachment_image_url(
			hd_ssi_get_random_image_id(),
			'hd_ssi_image',
		);

		// if we don't have an image added to settings.
		if ( empty( $image_url ) ) {

			// use the default image URL.
			$image_url = HD_SSI_LOCATION_URL . '/assets/img/image-placeholder.jpeg';

		}

	}

	// return the image url.
	return apply_filters(
		'hd_ssi_post_background_image_url',
		$image_url,
		$post_id
	);

}

/**
 * Renders a templatem replacing the merge tags with actual values.
 *
 * @param string $text The contents of the template htnml file.
 * @param array  $args Some arguments to work with. See $defaults.
 *
 * @return string      The rendered template outut including merge tags replaced.
 */
function hd_ssi_render_template( $post_id = 0 ) {

	// get the logo and image urls.
	$image_url = hd_ssi_get_post_background_image_url( $post_id );
	$logo_url = hd_ssi_get_logo_url();

	// if we have a logo.
	if ( empty( $logo_url ) ) {

		// set the logo to a transparent image.
		$logo_url = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

	}

	// if we have a image.
	if ( empty( $image_url ) ) {

		// set the image to a transparent image.
		$image_url = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

	}

	// set the template location to the plugin file.
	$template = HD_SSI_LOCATION . '/templates/default.php';

	// if the theme has a template that exists.
	if ( file_exists( STYLESHEETPATH . '/ssi/template.php' ) ) {

		// set the template path to the theme.
		$template = STYLESHEETPATH . '/ssi/template.php';

	}

	ob_start();

	// load the template markup, passing our args.
	load_template( $template, true );

	// get the template markup
	$template_markup = apply_filters( 'hd_ssi_template_markup', ob_get_clean() );

	// find all of the strings that need replacing. These are in square brackets.
	preg_match_all( "/\[[^\]]*\]/", $template_markup, $matches );

	// if we have matches.
	if ( $matches !== false ) {

		// loop through the matches.
		foreach ( $matches[0] as $match ) {

			// remove the brackets for the string.
			$match_value = str_replace( '[', '', $match );
			$match_value = str_replace( ']', '', $match_value );

			// if this is a meta replace.
			if ( strpos( $match_value, 'meta' ) !== false ) {

				// remove the meta: string
				$match_key = str_replace( 'meta:', '', $match_value );

				// get the value of this meta.
				$match_value = get_post_meta( $post_id, $match_key, true );

			}

			// if this is a tax replace.
			if ( strpos( $match_value, 'tax' ) !== false ) {

				// remove the tax: string
				$match_key = str_replace( 'tax:', '', $match_value );

				// if the taxonomy exists.
				if ( taxonomy_exists( $match_key ) ) {
					
					// get the value of this meta.
					$match_value = wp_strip_all_tags(
						get_the_term_list(
							$post_id,
							$match_key,
							'',
							', ',
							''
						)
					);

				} else {

					// set empty match value.
					$match_value = '';

				}

			}

			// if this is a post field replace.
			if ( strpos( $match_value, 'post' ) !== false ) {

				// remove the tax: string
				$match_key = str_replace( 'post:', '', $match_value );

				// get the value of this meta.
				$match_value = get_post_field( $match_key, $post_id );

				// if we have no job post id.
				if ( empty( $match_value ) && 0 === $post_id ) {

					// if we have a placeholder text.
					if ( ! empty( hd_ssi_get_placeholder_title() ) ) {

						// set match value to the placeholder.
						$match_value = hd_ssi_get_placeholder_title();
						
					} else {

						// set the match value to the site title.
						$match_value = get_bloginfo( 'title' );

					}

				}

			}

			// if this is a post field replace.
			if ( strpos( $match_value, 'siteinfo' ) !== false ) {

				// remove the siteinfo: string
				$match_key = str_replace( 'siteinfo:', '', $match_value );

				// allowed list of keys.
				$allowed_siteinfo = array(
					'name',
					'description',
					'url',
				);

				// if the match key is allowed.
				if ( in_array( $match_key, $allowed_siteinfo, true ) ) {

					// get the value of this meta.
					$match_value = get_bloginfo( $match_key, $post_id );
				
				} else {

					// set the match value to nothing.
					$match_value = '';
					
				}

			}

			// if this is a post field replace.
			if ( strpos( $match_value, 'ssi' ) !== false ) {

				// remove the ssi: string
				$match_key = str_replace( 'ssi:', '', $match_value );

				// if the match key is the logo.
				if ( 'logo_url' === $match_key ) {

					// get the logo url.
					$match_value = $logo_url;

					// replace the logo string.
					$template_markup = str_replace( '[ssi:logo_url]', $logo_url, $template_markup );

				}

				// if the match key is the image.
				if ( 'image_url' === $match_key ) {

					// get the image url.
					$match_value = $image_url;

					// replace the logo string.
					$template_markup = str_replace( '[ssi:image_url]', $image_url, $template_markup );

				}

				/**
				 * Note: author avatar handled through the filter below.
				 */

			}

			// filter the match post value.
			$match_value = apply_filters( 'hd_ssi_template_output_' . $match_key, $match_value, $match_key, $post_id );

			// replace the original template_markup string with the new 
			$template_markup = str_replace( $match, $match_value, $template_markup );

		}

	}

	return '<div class="' . esc_attr( hd_ssi_output_template_wrapper_classes() ) . '">' . $template_markup . '</div>';

}
