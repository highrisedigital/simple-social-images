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
function hd_ssi_add_template_custom_properties() {

	?>

	<style>
		.hdsmi-template{
			<?php
				if ( ! empty( hd_ssi_get_text_color() ) ) {
					echo "--hdsmi--text--color:" . esc_attr( hd_ssi_get_text_color() ) . ";";
				}

				if ( ! empty(  hd_ssi_get_text_bg_color() ) ) {
					echo "--hdsmi--text--background-color:" . esc_attr(  hd_ssi_get_text_bg_color() ) . ";";
				}

				if ( ! empty( hd_ssi_get_bg_color() ) ) {
					echo "--hdsmi--background-color:" . esc_attr( hd_ssi_get_bg_color() ) . ";";
				}

				if ( ! empty( hd_ssi_get_title_font_size() ) ) {
					echo "--hdsmi--title--font-size:" . esc_attr( hd_ssi_get_title_font_size() ) . ";";
				}

				if ( ! empty( hd_ssi_get_logo_size() ) ) {
					echo "--hdsmi--logo--height:" . esc_attr( hd_ssi_get_logo_size() ) . ";";
				}

				if ( ! empty( hd_ssi_get_google_font_family() ) ) {
					// using wp_kses_post as do not want to escape single quotes in the font family name.
					echo "--hdsmi--font-family:" . wp_kses_post( hd_ssi_get_google_font_family() ) . ";";
				}
			?>
		}
	</style>

	<?php

}

add_action( 'hd_ssi_before_settings_form_output', 'hd_ssi_add_template_custom_properties' );

/**
 * Adds markup to the end of the settings page for the template preview.
 */
function hd_ssi_add_preview_markup_to_settings_page() {

	// get the currently selected template.
	$selected_template = hd_ssi_get_template();

	// if the current template is from the plugin folder.
	if ( ! str_contains( $selected_template, HD_SSI_LOCATION ) ) {
		return;
	}

	// set a place holder transparent pixel to use as defaults.
	$placeholder_pixel = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
				
	// set the logo and background images to default to transparent pixel.
	$logo_src = $placeholder_pixel;
	$bg_img_src = $placeholder_pixel;

	// if we have a logo already added.
	if ( ! empty( hd_ssi_get_logo_id() ) ) {

		// set the logo src to the added logo image src.
		$logo_src = wp_get_attachment_image_url( hd_ssi_get_logo_id(), 'full' );

	}

	// if we have background images already added.
	if ( ! empty( hd_ssi_get_background_images() ) ) {

		// set a random background image src to the added background image src.
		$bg_img_src = wp_get_attachment_image_url( hd_ssi_get_random_image_id(), 'full' );

	}

	// set default title placeholder.
	$title_placeholder = __( 'Sample job title (click to edit)', 'simple-social-images' );

	// if the title placeholder has been set, use that one.
	if ( ! empty( hd_ssi_get_title_placeholder_text() ) ) {
		$title_placeholder = hd_ssi_get_title_placeholder_text();
	}

	// default template.
	$template = '1';

	// get the current template.
	if ( ! empty ( hd_ssi_get_template() ) ) {
		$template = hd_ssi_get_template();
	}

	?>
	
	<div class="hd-ssi-template-preview" data-title="<?php echo esc_attr( $title_placeholder ); ?>" data-logo="<?php echo esc_url( $logo_src ); ?>" data-bgimage="<?php echo esc_url( $bg_img_src ); ?>" data-template="<?php echo esc_attr( $template ); ?>">

		<?php

		ob_start();

		// load in the template.
		load_template( $template );

		// get the contents of the buffer, the template markup and clean the buffer.
		$preview = ob_get_clean();

		$preview = hd_ssi_render_template(
			$preview,
			array(
				'template' => $template,
				'logo'     => $logo_src,
				'image'    => $bg_img_src,
			)
		);

		echo $preview;

		?>

	</div>

	<?php

}

add_action( 'hd_ssi_after_settings_form_output', 'hd_ssi_add_preview_markup_to_settings_page' );

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
	$license_key = get_option( 'hd_ssi_license_key' );

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
				'element'     => '.hdsmi-template',
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
 * Renders a templatem replacing the merge tags with actual values.
 *
 * @param string $text The contents of the template htnml file.
 * @param array  $args Some arguments to work with. See $defaults.
 *
 * @return string      The rendered template outut including merge tags replaced.
 */
function hd_ssi_render_template( $text, $args ) {

	// create some default args.
	$defaults = array(
		'image'    => '',
		'logo'     => '',
		'template' => '1',
		'post_id'  => 0
	);

	// merge the args with the defaults.
	$args = wp_parse_args( $args, $defaults );

	// find all of the strings that need replacing. These are in square brackets.
	preg_match_all( "/\[[^\]]*\]/", $text, $matches );

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
				$match_value = get_post_meta( $args['post_id'], $match_key, true );

				// filter the match post value.
				$match_value = apply_filters( 'hd_ssi_template_' . $match_key, $match_value, $match_key );

				// replace the original text string with the new 
				$text = str_replace( $match, $match_value, $text );

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
							$args['post_id'],
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

				// filter the match post value.
				$match_value = apply_filters( 'hd_ssi_template_' . $match_key, $match_value, $match_key );

				// replace the original text string with the new 
				$text = str_replace( $match, $match_value, $text );

			}

			// if this is a post field replace.
			if ( strpos( $match_value, 'post' ) !== false ) {

				// remove the tax: string
				$match_key = str_replace( 'post:', '', $match_value );

				// get the value of this meta.
				$match_value = get_post_field( $match_key, $args['post_id'] );

				// if we have no job post id.
				if ( empty( $args['post_id'] ) ) {

					// set the match value to the site title.
					$match_value = get_bloginfo( 'title' );

				}

				// filter the match post value.
				$match_value = apply_filters( 'hd_ssi_template_' . $match_key, $match_value, $match_key, $args['post_id'] );

				// replace the original text string with the new 
				$text = str_replace( $match, $match_value, $text );

			}
			
			// if we have a logo.
			if ( empty( $args['logo'] ) ) {

				// set the logo to a transparent image.
				$args['logo'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

			}

			// if we have a image.
			if ( empty( $args['image'] ) ) {

				// set the image to a transparent image.
				$args['image'] = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

			}

			// if we have no template.
			if ( empty( $args['template'] ) ) {

				// default the template.
				$args['template'] = HD_SSI_LOCATION . '/templates/1.html';

			}
			
			// replace the logo string.
			$text = str_replace( '[logo]', $args['logo'], $text );

			// replace the template string.
			$text = str_replace( '[template]', str_replace( '.html', '', basename( $args['template'] ) ), $text );

			// replace the image string.
			$text = str_replace( '[image]', $args['image'], $text );

		}

	}

	return $text;

}