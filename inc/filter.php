<?php
/**
 * Modifies/adds various things in the plugin via filters.
 */

/**
 * Adds markup to the end of the settings page for the template preview.
 */
function hd_ssi_add_preview_markup_to_settings_page() {

	// output the custom properties.
	hd_ssi_output_template_custom_properties();

	?>
	
	<div class="hd-ssi-template-preview">

		<?php

		// render the template.
		echo hd_ssi_render_template();

		?>

	</div>

	<?php

}

add_action( 'hd_ssi_after_settings_form_output', 'hd_ssi_add_preview_markup_to_settings_page' );

/**
 * Filters the output of the author information to display the author display name.
 *
 * @param integer $author_id The ID of the author of the post.
 * @param string  $match_key The matches key string to replace in the template (post_author)
 * @param integer $post_id   The ID of the current post.
 *
 * @return string            An empty string if no author could be found or the author display name.
 */
function hd_ssi_output_template_author_display_name( $author_id, $match_key, $post_id ) {

	// get the author display name.
	$author_display_name = get_the_author_meta( 'display_name', $author_id );

	// if we don't have an author display name.
	if ( empty( $author_display_name ) ) {
		return '';
	}

	// get the author display name.
	return esc_html( $author_display_name );

}

add_filter( 'hd_ssi_template_output_post_author', 'hd_ssi_output_template_author_display_name', 10, 3 );

/**
 * Filters the output of the data information to display the posted post date.
 *
 * @param integer $post_date The post date from the posts table.
 * @param string  $match_key The matches key string to replace in the template (post_date)
 * @param integer $post_id   The ID of the current post.
 *
 * @return string            The posted date in the correct date format.
 */
function hd_ssi_output_template_post_date( $post_date, $match_key, $post_id ) {

	// get the post date in the date format for this site.
	$post_date = get_the_date(
		get_option( 'date_format' ),
		$post_id
	);

	return $post_date;

}

add_filter( 'hd_ssi_template_output_post_date', 'hd_ssi_output_template_post_date', 10, 3 );

/**
 * Filters the output of the data information to display the posted post date.
 *
 * @param integer $post_date The post date from the posts table.
 * @param string  $match_key The matches key string to replace in the template (post_date)
 * @param integer $post_id   The ID of the current post.
 *
 * @return string            The posted date in the correct date format.
 */
function hd_ssi_output_template_author_avatar( $match_value, $match_key, $post_id ) {

	// get the author ID of the post.
	$author_id = get_post_field( 'post_author', $post_id );
	
	// get the avatar image tag for this author.
	$author_avatar = get_avatar( $author_id, '200', '' );
	
	// if we have an author avatar.
	if ( ! empty( $author_avatar ) ) {

		// set the avatar image to the match value.
		$match_value = $author_avatar;

	}

	// return the matched value.
	return $match_value;

}

add_filter( 'hd_ssi_template_output_author_avatar', 'hd_ssi_output_template_author_avatar', 10, 3 );

/**
 * Outputs the necessary stylesheet link on the generate HTML endpoint.
 *
 * @param integer $post_id The ID of the post were are generating the HTML for.
 */
function hd_ssi_output_generate_css( $post_id ) {

	?>
	<link rel="stylesheet" href="<?php echo esc_url( HD_SSI_LOCATION_URL . '/assets/css/hd-ssi-generate.css' ); ?>" />
	<?php

}

add_action( 'hd_ssi_generate_html_head', 'hd_ssi_output_generate_css', 10, 1 );

/**
 * Outputs the custom properties in the head of the generate html endpoint.
 *
 * @param integer $post_id The ID of the post were are generating the HTML for.
 */
function hd_ssi_output_generate_custom_properties( $post_id ) {

	// output the template custom properties
	hd_ssi_output_template_custom_properties();

}

add_action( 'hd_ssi_generate_html_head', 'hd_ssi_output_generate_custom_properties', 20, 1 );

/**
 * Outputs the google font url and font family data in the head of the generate html endpoint.
 *
 * @param integer $post_id The ID of the post were are generating the HTML for.
 */
function hd_ssi_output_generate_google_font_data( $post_id ) {

	// if we have a google font url.
	if ( ! empty( hd_ssi_get_google_font_family() ) && ! empty( hd_ssi_get_google_font_url() ) ) {

		// output the link elements to load the font.
		?>

		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="<?php echo esc_url( hd_ssi_get_google_font_url() ); ?>" rel="stylesheet">

		<?php

	}

}

add_action( 'hd_ssi_generate_html_head', 'hd_ssi_output_generate_google_font_data', 30, 1 );
