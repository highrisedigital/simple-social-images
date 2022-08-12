<?php
/**
 * Functions for outputting sharing tags when using Rank Math SEO plugin.
 */

/**
 * Check whether a given post has a custom open graph image set by Yoast SEO.
 *
 * @param  integer $post_id The post to check. Defaults to the current post.
 * @return bool             True if the post has a custom image and false otherwise.
 */
function hd_ssi_has_rankmath_custom_og_image( $post_id = 0 ) {

	// if we don't have a post id to check.
	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	// return whether or not the post has a custom yoast og image.
	return ! empty( get_post_meta( $post_id, 'rank_math_facebook_image', true ) );

}

/**
 * Check whether a given post has a custom twitter image set by Yoast SEO.
 *
 * @param  integer $post_id The post to check. Defaults to the current post.
 * @return bool             True if the post has a custom image and false otherwise.
 */
function hd_ssi_has_rankmath_custom_twitter_image( $post_id = 0 ) {

	// if we don't have a post id to check.
	if ( 0 === $post_id ) {
		$post_id = get_the_ID();
	}

	// return whether or not the post has a custom yoast twitter image.
	return ! empty( get_post_meta( get_the_ID(), 'rank_math_twitter_image', true ) );

}

/**
 * Change open graph image URL if necessary.
 *
 * @param string $url URL of the image.
 */
function hd_ssi_rankmath_maybe_change_open_graph_image_url( $url ) {

	// if this is not a single job or we have a custom rank math open graph image.
	if ( ! is_singular( hd_ssi_get_supported_post_types() ) || hd_ssi_has_rankmath_custom_og_image() ) {

		// prevent this plugin outputting an og image.
		add_filter( 'hd_ssi_render_og_image_tags', '__return_false' );

		// return the original url from RankMath.
		return $url;

	}

	// return the social sharing image.
	return hd_ssi_get_image_url();

}

add_filter( 'rank_math/opengraph/facebook/og_image', 'hd_ssi_rankmath_maybe_change_open_graph_image_url' );
add_filter( 'rank_math/opengraph/facebook/og_image_secure_url', 'hd_ssi_wpjm_rankmath_maybe_change_open_graph_image_url' );

/**
 * Change Twitter image URL if necessary.
 *
 * @param string $url URL of the image.
 */
function hd_ssi_rankmath_maybe_change_twitter_image_url( $url ) {

	// if this is not a singular supported post or we have a custom rank math twitter image.
	if ( ! is_singular( hd_ssi_get_supported_post_types() ) || hd_ssi_has_rankmath_custom_twitter_image() ) {

		// prevent this plugin outputting a twitter image.
		add_filter( 'hd_ssi_render_twitter_image_tags', '__return_false' );

		// return the original url from RankMath.
		return $url;
	}

	// return the social sharing image.
	return hd_ssi_get_image_url();
}

add_filter( 'rank_math/opengraph/twitter/image', 'hd_ssi_rankmath_maybe_change_twitter_image_url' );
