<?php

// default job post.
$post_id = 0;

// if a job post is available to generate the html of.
if ( ! empty( $_GET['post_id'] ) ) {

	// sanitize and set as the job post.
	$post_id = absint( $_GET['post_id'] );

}

// set some args.
$args = array(
	'template'           => hd_ssi_get_template(),
	'post_id'           => $post_id,
	'bg_color'           => hd_ssi_get_bg_color(),
	'bg_text_color'      => hd_ssi_get_text_bg_color(),
	'text_color'         => hd_ssi_get_text_color(),
	'title_size'         => hd_ssi_get_title_font_size(),
	'google_font_url'    => hd_ssi_get_google_font_url(),
	'google_font_family' => hd_ssi_get_google_font_family(),
	'logo_size'          => hd_ssi_get_logo_size(),
	'image'              => wp_get_attachment_image_url( hd_ssi_get_random_image_id(), 'full' ),
	'logo'               => wp_get_attachment_image_url( hd_ssi_get_logo_id(), 'full' ),
);

// if the current post has a featured image.
if ( has_post_thumbnail( $args['post_id'] ) ) {

	// set the image url to that of the featured image.
	$args['image'] = get_the_post_thumbnail_url( $args['post_id'], 'full' );

}

// allow the args to be filtered.
$args = apply_filters( 'hd_ssi_endpoint_generate_args', $args );

// start output buffering.
ob_start();

?>
<!doctype html>
<html class="no-js" lang="">

	<head>

		<link rel="stylesheet" href="<?php echo esc_url( HD_SSI_LOCATION_URL . '/assets/css/hd-ssi-generate.css' ); ?>" />
		
		<?php

		// output the custom properties.
		hd_ssi_output_template_custom_properties();

		// if we have a google font url.
		if ( ! empty( $args['google_font_family'] ) && ! empty( $args['google_font_url'] ) ) {

			// output the link elements to load the font.
			?>

			<link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="<?php echo esc_url( $args['google_font_url'] ); ?>" rel="stylesheet">

			<?php

		}

	?>

	</head>

<?php

// if our template exists.
if ( file_exists( $args['template'] ) ) {

	// load the template markup, passing our args.
	load_template( $args['template'], true, $args );

}

// get the contents of the buffer, the template markup and clean the buffer.
$text = ob_get_clean();

$text = hd_ssi_render_template(
	$text,
	$args
);

echo $text . '</body></html>';
