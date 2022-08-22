<?php
/**
 * Functions for outputting sharing tags when using Yoast SEO plugin.
 */

/**
 * Check whether a given post has a custom open graph image set by Yoast SEO.
 *
 * @param  integer $post_id The post to check. Defaults to the current post.
 * @return bool             True if the post has a custom image and false otherwise.
 */
function hd_ssi_has_yoast_custom_og_image( $post_id = 0 ) {

	// if we don't have a post id to check.
	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	// return whether or not the post has a custom yoast og image.
	return ! empty( get_post_meta( $post_id, '_yoast_wpseo_opengraph-image', true ) );

}

/**
 * Check whether a given post has a custom twitter image set by Yoast SEO.
 *
 * @param  integer $post_id The post to check. Defaults to the current post.
 * @return bool             True if the post has a custom image and false otherwise.
 */
function hd_ssi_has_yoast_custom_twitter_image( $post_id = 0 ) {

	// if we don't have a post id to check.
	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	// return whether or not the post has a custom yoast twitter image.
	return ! empty( get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-image', true ) );

}

/**
 * Remove Yoast OpenGraph and Twitter tags if the images are not explicitly set.
 *
 * @param array $presenters \Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter[].
 * @return array \Yoast\WP\SEO\Presenters\Abstract_Indexable_Presenter[]
 */
function hd_ssi_maybe_remove_yoast_tags( $presenters ) {

	// if this is not a single job.
	if ( ! is_singular( hd_ssi_get_supported_post_types() ) ) {
		return $presenters;
	}

	// create an array in which to add classes we want to remove from yoast.
	$removed_presenters = array();

	// if we have a custom yoast open graph image added for this post.
	if ( hd_ssi_has_yoast_custom_og_image() ) {

		// prevent this plugin outputting an og image.
		add_filter( 'hd_ssi_render_og_image_tags', '__return_false' );
		
	} else {
		
		// add the open graph presenter class to our list of presenters to remove.
		$removed_presenters[] = 'Yoast\WP\SEO\Presenters\Open_Graph\Image_Presenter';

	}

	// if we have a custom yoast twitter image added for this post.
	if ( hd_ssi_has_yoast_custom_twitter_image() ) {

		// prevent this plugin outputting an og image.
		add_filter( 'hd_ssi_render_twitter_image_tags', '__return_false' );
		
	} else {
		
		// add the twitter presenter classes to our list of presenters to remove.
		$removed_presenters[] = 'Yoast\WP\SEO\Presenters\Twitter\Image_Presenter';
		$removed_presenters[] = 'Yoast\WP\SEO\Presenters\Twitter\Card_Presenter';

	}
	
	// return the maybe modified presenters array without any removed classes in.
	return array_filter(
		$presenters,
		function ( $presenter ) use ( $removed_presenters ) {
			return ! in_array( get_class( $presenter ), $removed_presenters, true );
		}
	);

}

add_filter( 'wpseo_frontend_presenters', 'hd_ssi_maybe_remove_yoast_tags' );
