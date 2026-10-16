<?php
/**
 * End of article (templates/single.html):
 *
 * - [ren_post_contact]: a line with the protected email ([ren_email]) in
 *   place of the comment form. Shown only when an email is set in
 *   Theme Options → Footer.
 * - [ren_print_url]: the article's address, hidden on screen and printed
 *   at the end of the text, for prints made without the browser's headers.
 * - Comments are closed on posts: no new comments or pingbacks, also for
 *   bots posting straight to wp-comments-post.php. Comments already
 *   published stay visible (the Comments block in single.html).
 *
 * Output is single-line: the Shortcode block runs wpautop() on its result.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** [ren_post_contact] */
function ren_post_contact_shortcode() {
	if ( ! is_singular( 'post' ) || ! function_exists( 'ren_shortcode_email' ) ) {
		return '';
	}
	$email = ren_shortcode_email();
	if ( '' === $email ) {
		return '';
	}
	return '<p class="ren-post-contact">' . esc_html__( 'Un\'osservazione su questo articolo? Scrivimi:', 'ren' ) . ' ' . $email . '</p>';
}
add_shortcode( 'ren_post_contact', 'ren_post_contact_shortcode' );

/** [ren_print_url] */
function ren_print_url_shortcode() {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}
	$url = get_permalink( get_queried_object_id() );
	if ( ! $url ) {
		return '';
	}
	return '<p class="ren-print-url">' . esc_html__( 'Articolo pubblicato su:', 'ren' ) . ' <span>' . esc_html( $url ) . '</span></p>';
}
add_shortcode( 'ren_print_url', 'ren_print_url_shortcode' );

/**
 * Close comments and pings on posts.
 *
 * @param bool $open    Whether open.
 * @param int  $post_id Post ID.
 */
function ren_close_post_comments( $open, $post_id ) {
	return 'post' === get_post_type( $post_id ) ? false : $open;
}
add_filter( 'comments_open', 'ren_close_post_comments', 20, 2 );
add_filter( 'pings_open', 'ren_close_post_comments', 20, 2 );
