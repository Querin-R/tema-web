<?php
/**
 * Appunti di grafica — archive enhancements:
 *
 * 1. Featured post. On page 1 of the posts page the latest article is shown
 *    large, across the full grid width (CSS in style.css). To keep full rows
 *    of cards, page 1 loads one extra post (featured + a normal page) and
 *    the following pages are offset by one; pagination and the "x–y di N"
 *    count are corrected accordingly.
 *
 * 2. Grid / list toggle. [ren_view_toggle] prints two buttons; the choice
 *    is remembered in the visitor's browser (localStorage) and applied
 *    before the first paint, so there is no flash of the other layout.
 *    It only switches the existing body classes ren-blog-layout-grid /
 *    ren-blog-layout-list, whose default comes from Theme Options → Blog.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True on the posts page (Appunti di grafica), any page number.
 */
function ren_is_blog_index() {
	return is_home() && ! is_front_page();
}

/**
 * True when the featured-post logic applies to the main query.
 *
 * @param WP_Query|null $query Query, defaults to the main one.
 */
function ren_blog_featured_applies( $query = null ) {
	$query = $query ? $query : $GLOBALS['wp_query'];
	return $query->is_main_query() && $query->is_home() && ! $query->is_front_page() && ! is_admin();
}

/**
 * Page 1: one extra post. Page n: shift by one.
 *
 * @param WP_Query $query Query.
 */
function ren_blog_featured_pre_get_posts( $query ) {
	if ( ! ren_blog_featured_applies( $query ) ) {
		return;
	}
	$ppp   = (int) get_option( 'posts_per_page' );
	$paged = max( 1, (int) $query->get( 'paged' ) );
	if ( 1 === $paged ) {
		$query->set( 'posts_per_page', $ppp + 1 );
	} else {
		$query->set( 'posts_per_page', $ppp );
		$query->set( 'offset', ( ( $paged - 1 ) * $ppp ) + 1 );
	}
}
add_action( 'pre_get_posts', 'ren_blog_featured_pre_get_posts' );

/** Correct page count: the first post is extra, the rest are paged normally. */
function ren_blog_featured_fix_pages() {
	global $wp_query;
	if ( ! ren_blog_featured_applies() ) {
		return;
	}
	$ppp   = (int) get_option( 'posts_per_page' );
	$found = (int) $wp_query->found_posts;
	$wp_query->max_num_pages = $found > 1 ? (int) ceil( ( $found - 1 ) / $ppp ) : 1;
}
add_action( 'template_redirect', 'ren_blog_featured_fix_pages', 1 );

/**
 * Range shown on the current page, used by [ren_archive_count].
 *
 * @return array{0:int,1:int}|null [start, end] or null when not applicable.
 */
function ren_blog_featured_range() {
	global $wp_query;
	if ( ! ren_blog_featured_applies() ) {
		return null;
	}
	$ppp   = (int) get_option( 'posts_per_page' );
	$found = (int) $wp_query->found_posts;
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	if ( 1 === $paged ) {
		return array( 1, min( $found, $ppp + 1 ) );
	}
	$start = ( ( $paged - 1 ) * $ppp ) + 2;
	return array( $start, min( $found, $start + $ppp - 1 ) );
}

/** Body class that turns the featured styling on (page 1 of the posts page only). */
function ren_blog_featured_body_class( $classes ) {
	if ( ren_blog_featured_applies() && ! is_paged() ) {
		$classes[] = 'ren-blog-has-featured';
	}
	return $classes;
}
add_filter( 'body_class', 'ren_blog_featured_body_class' );

/** Pages where the grid/list toggle is offered. */
function ren_blog_view_toggle_applies() {
	return ren_is_blog_index() || is_category() || is_tag() || is_date() || is_author();
}

/** [ren_view_toggle] */
function ren_view_toggle_shortcode() {
	$grid = '<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true"><rect x="2" y="2" width="7" height="7" rx="1"/><rect x="11" y="2" width="7" height="7" rx="1"/><rect x="2" y="11" width="7" height="7" rx="1"/><rect x="11" y="11" width="7" height="7" rx="1"/></svg>';
	$list = '<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true"><rect x="2" y="3" width="6" height="5" rx="1"/><rect x="10" y="4" width="8" height="1.6" rx=".8"/><rect x="10" y="6.4" width="5" height="1.6" rx=".8"/><rect x="2" y="12" width="6" height="5" rx="1"/><rect x="10" y="13" width="8" height="1.6" rx=".8"/><rect x="10" y="15.4" width="5" height="1.6" rx=".8"/></svg>';
	return '<div class="ren-view-toggle" role="group" aria-label="' . esc_attr__( 'Visualizzazione', 'ren' ) . '">'
		. '<button type="button" class="ren-view-toggle__btn" data-view="grid" aria-pressed="false" title="' . esc_attr__( 'Griglia', 'ren' ) . '">' . $grid . '<span class="screen-reader-text">' . esc_html__( 'Griglia', 'ren' ) . '</span></button>'
		. '<button type="button" class="ren-view-toggle__btn" data-view="list" aria-pressed="false" title="' . esc_attr__( 'Lista', 'ren' ) . '">' . $list . '<span class="screen-reader-text">' . esc_html__( 'Lista', 'ren' ) . '</span></button>'
		. '</div>';
}
add_shortcode( 'ren_view_toggle', 'ren_view_toggle_shortcode' );

/** Apply the remembered view before first paint. */
function ren_view_toggle_early_script() {
	if ( ! ren_blog_view_toggle_applies() ) {
		return;
	}
	echo "<script>(function(){try{var v=localStorage.getItem('ren-blog-view');if(v==='grid'||v==='list'){var b=document.body;b.classList.remove('ren-blog-layout-grid','ren-blog-layout-list');b.classList.add('ren-blog-layout-'+v);}}catch(e){}})();</script>\n";
}
add_action( 'wp_body_open', 'ren_view_toggle_early_script', 1 );

/** Toggle behaviour. */
function ren_view_toggle_footer_script() {
	if ( ! ren_blog_view_toggle_applies() ) {
		return;
	}
	?>
	<script>
	( function () {
		var b = document.body, btns = document.querySelectorAll( '.ren-view-toggle__btn' );
		if ( ! btns.length ) { return; }
		function sync() {
			var v = b.classList.contains( 'ren-blog-layout-list' ) ? 'list' : 'grid';
			btns.forEach( function ( x ) { x.setAttribute( 'aria-pressed', x.dataset.view === v ? 'true' : 'false' ); } );
		}
		btns.forEach( function ( x ) {
			x.addEventListener( 'click', function () {
				b.classList.remove( 'ren-blog-layout-grid', 'ren-blog-layout-list' );
				b.classList.add( 'ren-blog-layout-' + x.dataset.view );
				try { localStorage.setItem( 'ren-blog-view', x.dataset.view ); } catch ( e ) {}
				sync();
			} );
		} );
		sync();
	} )();
	</script>
	<?php
}
add_action( 'wp_footer', 'ren_view_toggle_footer_script' );
