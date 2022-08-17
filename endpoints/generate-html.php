<?php
// default job post.
$post_id = 0;

// if a job post is available to generate the html of.
if ( ! empty( $_GET['post_id'] ) ) {

	// sanitize and set as the job post.
	$post_id = absint( $_GET['post_id'] );

}

// get the template.
$template = hd_ssi_get_template();

?>
<!doctype html>
<html class="no-js" lang="">

	<head>

		<link rel="stylesheet" href="<?php echo esc_url( HD_SSI_LOCATION_URL . '/assets/css/hd-ssi-generate.css' ); ?>" />
		
		<?php

		// output the custom properties.
		hd_ssi_output_template_custom_properties();

		// if we have a google font url.
		if ( ! empty( hd_ssi_get_google_font_family() ) && ! empty( hd_ssi_get_google_font_url() ) ) {

			// output the link elements to load the font.
			?>

			<link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="<?php echo esc_url( hd_ssi_get_google_font_url() ); ?>" rel="stylesheet">

			<?php

		}

	?>

	</head>

<?php

// start output buffering.
ob_start();

// if our template exists.
if ( file_exists( $template ) ) {

	// load the template markup, passing our args.
	load_template( $template, true );

}

// get the contents of the buffer, the template markup and clean the buffer.
$text = ob_get_clean();

$text = hd_ssi_render_template(
	$text,
	$post_id
);

echo $text . '</body></html>';
