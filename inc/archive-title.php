<?php
/**
 * [ren_archive_title] — used in templates/archive.html in place of the
 * native Query Title block (core/query-title / get_the_archive_title()).
 *
 * Two separate problems solved here, not one:
 *
 * 1. get_the_archive_title() is unreliable on the main blog posts page
 *    specifically (the page assigned in Impostazioni → Lettura → Pagina
 *    articoli) — a known WordPress/Gutenberg limitation (see
 *    https://github.com/WordPress/gutenberg/issues/38574), not something
 *    specific to this theme. On that one page it can render empty, or
 *    even show the first post's title instead of the page's own. Fixed
 *    by pulling the real page title directly on is_home().
 *
 * 2. For category/tag/taxonomy archives, get_the_archive_title() returns
 *    a string like 'Categoria: <span>Grafica</span>' — note the raw
 *    HTML *inside* the string. The previous version of this function ran
 *    esc_html() over that entire string, which correctly escapes HTML
 *    that shouldn't be there — except this HTML is legitimate, coming
 *    from WordPress core itself, so escaping it made the literal
 *    characters "<span>" show up as visible text on the page. Rather
 *    than un-escape core's HTML (fragile — its exact shape isn't a
 *    documented, stable contract), this pulls just the plain term name
 *    directly for the archive types that matter here, with no prefix
 *    and no wrapper markup at all — which is also plainly nicer as a
 *    page title than "Categoria: Grafica" anyway.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ren_archive_title_shortcode() {
	if ( is_home() && ! is_front_page() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		$title         = $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Blog', 'ren' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_author() ) {
		$title = get_the_author();
	} else {
		// Any other archive type (date, post type archive, search, …):
		// strip_all_tags as a safety net, so a future WordPress version
		// embedding different markup in get_the_archive_title() can
		// never reproduce the visible "<span>" bug this replaced.
		$title = wp_strip_all_tags( get_the_archive_title() );
	}

	if ( ! $title ) {
		return '';
	}

	return '<h1 class="wp-block-heading">' . esc_html( $title ) . '</h1>';
}
add_shortcode( 'ren_archive_title', 'ren_archive_title_shortcode' );
