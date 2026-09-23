<?php
/**
 * [ren_category_filter] and [ren_archive_count] — used on the blog listing
 * (index.html) and category archives (archive.html).
 *
 * ren_category_filter_shortcode() outputs a row of pill links: "Tutti"
 * (back to the main Blog page) plus one link per category that actually
 * has at least one published post — get_categories() already excludes
 * empty categories by default (hide_empty defaults to true), so no extra
 * filtering is needed here. This is a plain link-based filter (each pill
 * is a real URL to that category's own archive), not a client-side/AJAX
 * filter: with real pagination (12 posts per page) a same-page JS filter
 * would only ever affect the 12 posts already loaded, so it could never
 * show "all" matching posts across pages anyway — a normal link keeps
 * the existing archive template, pagination and SEO-indexable URLs all
 * working with no extra code.
 *
 * ren_archive_count_shortcode() prints a small "12–23 di 47 articoli"
 * style line (as seen on creativeboom.com), computed from the *current*
 * main query, so it stays correct on every page of the pagination and
 * for whichever category is currently selected.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ren_category_filter_shortcode() {
	$categories = get_categories( array( 'hide_empty' => true ) );
	if ( empty( $categories ) ) {
		return '';
	}

	$posts_page_id = (int) get_option( 'page_for_posts' );
	$all_url       = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
	$all_active    = is_home() && ! is_category();

	$out  = '<nav class="ren-category-filter" aria-label="' . esc_attr__( 'Filtra per categoria', 'ren' ) . '">';
	$out .= '<a href="' . esc_url( $all_url ) . '" class="ren-category-filter__link' . ( $all_active ? ' is-active' : '' ) . '">' . esc_html__( 'Tutti', 'ren' ) . '</a>';

	foreach ( $categories as $category ) {
		$is_active = is_category( $category->term_id );
		$out      .= '<a href="' . esc_url( get_category_link( $category ) ) . '" class="ren-category-filter__link' . ( $is_active ? ' is-active' : '' ) . '">' . esc_html( $category->name ) . '</a>';
	}

	$out .= '</nav>';

	return $out;
}
add_shortcode( 'ren_category_filter', 'ren_category_filter_shortcode' );

function ren_archive_count_shortcode() {
	global $wp_query;

	$found = (int) $wp_query->found_posts;
	if ( $found < 1 ) {
		return '';
	}

	$per_page = (int) $wp_query->get( 'posts_per_page' );
	if ( $per_page < 1 ) {
		$per_page = (int) get_option( 'posts_per_page' );
	}

	$paged = (int) get_query_var( 'paged' );
	if ( $paged < 1 ) {
		$paged = (int) get_query_var( 'page' );
	}
	$paged = max( 1, $paged );

	$start = ( ( $paged - 1 ) * $per_page ) + 1;
	$end   = min( $found, $paged * $per_page );

	$label = _n( 'articolo', 'articoli', $found, 'ren' );
	$text  = ( $start === $end )
		? sprintf( '%1$d di %2$d %3$s', $start, $found, $label )
		: sprintf( '%1$d–%2$d di %3$d %4$s', $start, $end, $found, $label );

	return '<p class="ren-archive-count has-muted-color has-text-color has-small-font-size">' . esc_html( $text ) . '</p>';
}
add_shortcode( 'ren_archive_count', 'ren_archive_count_shortcode' );
