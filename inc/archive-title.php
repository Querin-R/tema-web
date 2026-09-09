<?php
/**
 * [ren_archive_title] — used in templates/archive.html in place of the
 * native Query Title block (core/query-title / get_the_archive_title()).
 *
 * Why this exists: get_the_archive_title() works fine for category, tag,
 * author and date archives ("Categoria: Grafica", etc.), but is unreliable
 * on the main blog posts page specifically (the page assigned in
 * Impostazioni → Lettura → Pagina articoli) — it's a known WordPress/
 * Gutenberg limitation (see https://github.com/WordPress/gutenberg/issues/38574),
 * not something specific to this theme. On that one page it can render
 * empty, or even show the first post's title instead of the page's own.
 *
 * This shortcode keeps the native, reliable behaviour everywhere else,
 * and only special-cases is_home(): there, it pulls the real title of
 * whichever page is assigned as the posts page, so editing that page's
 * title in wp-admin actually changes what visitors (and Google) see.
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
	} else {
		$title = get_the_archive_title();
	}

	if ( ! $title ) {
		return '';
	}

	return '<h1 class="wp-block-heading">' . esc_html( $title ) . '</h1>';
}
add_shortcode( 'ren_archive_title', 'ren_archive_title_shortcode' );
