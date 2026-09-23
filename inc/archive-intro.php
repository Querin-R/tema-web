<?php
/**
 * [ren_archive_intro] — outputs the actual content the user wrote in
 * Gutenberg on the page assigned in Impostazioni → Lettura → Pagina
 * articoli (page_for_posts).
 *
 * WordPress never renders that page's own content on the front end: the
 * URL is served by the blog-listing template (index.html here), which
 * only runs the posts query loop — the page's post_content is simply
 * never touched. This is standard WordPress behaviour on every theme,
 * not a bug, but it means any text typed on the Blog page in the editor
 * silently disappears. Same fix pattern as ren_archive_title_shortcode()
 * in archive-title.php: read the real page's content directly and run
 * it through the_content filters so its own blocks (paragraphs,
 * headings, images, …) render normally.
 *
 * Only applies on is_home() — every other archive type (category, tag,
 * author, search, …) has no equivalent "page" to pull content from, so
 * the shortcode simply outputs nothing there.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ren_archive_intro_shortcode() {
	if ( ! is_home() || is_front_page() ) {
		return '';
	}

	$posts_page_id = (int) get_option( 'page_for_posts' );
	if ( ! $posts_page_id ) {
		return '';
	}

	$page = get_post( $posts_page_id );
	if ( ! $page || '' === trim( $page->post_content ) ) {
		return '';
	}

	return apply_filters( 'the_content', $page->post_content );
}
add_shortcode( 'ren_archive_intro', 'ren_archive_intro_shortcode' );
