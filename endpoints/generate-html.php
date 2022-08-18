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
<html>

	<head>

		<?php

		/**
		 * Fires an action in the head of the endpoint.
		 *
		 * @hooked hd_ssi_output_generate_css - 10
		 * @hooked hd_ssi_output_generate_custom_properties - 20
		 * @hookwd hd_ssi_output_generate_google_font_data - 30
		 */
		do_action( 'hd_ssi_generate_html_head', $post_id );

		?>

	</head>

	<body>

	<?php

	// render the template.
	echo hd_ssi_render_template( $post_id );

	?>

</body>
</html>
