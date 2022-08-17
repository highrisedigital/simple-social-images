<?php
/**
 * Modifies/adds various things in the plugin via filters.
 */

/**
 * Adds markup to the end of the settings page for the template preview.
 */
function hd_ssi_add_preview_markup_to_settings_page() {

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

	// output the custom properties.
	hd_ssi_output_template_custom_properties();

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
	return __( 'By', 'simple-social-images' ) . ' ' . esc_html( $author_display_name );

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
	//wp_var_dump( $post_date );

	return $post_date;

}

add_filter( 'hd_ssi_template_output_post_date', 'hd_ssi_output_template_post_date', 10, 3 );
