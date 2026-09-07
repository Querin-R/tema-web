<?php
/**
 * Fullscreen search overlay — a search icon in the header nav ([ren_search_toggle])
 * opens a fullscreen overlay ([ren_search_overlay]) instead of the old sidebar
 * search widget. Toggling is handled by assets/js/search-overlay.js.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [ren_search_toggle] — the magnifying-glass button shown in the header nav.
 */
function ren_search_toggle_shortcode() {
	ob_start();
	?>
	<button type="button" id="ren-search-toggle" class="ren-search-toggle" aria-label="<?php esc_attr_e( 'Cerca', 'ren' ); ?>" aria-expanded="false" aria-controls="ren-search-overlay">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
			<circle cx="11" cy="11" r="7"></circle>
			<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
		</svg>
	</button>
	<?php
	// See inc/post-nav-thumbs.php for why: multi-line indented PHP templates
	// like this one leave real newlines in the output, and wpautop() (run on
	// shortcode content elsewhere in the render pipeline) converts each into
	// a literal <br />. Collapsing whitespace between tags only (not inside
	// text nodes) removes anything wpautop could act on.
	return preg_replace( '/\s*\n\s*/', ' ', trim( ob_get_clean() ) );
}
add_shortcode( 'ren_search_toggle', 'ren_search_toggle_shortcode' );

/**
 * [ren_search_overlay] used to be rendered here, but header.html gets
 * wrapped in <header class="ren-header"> by the calling template-part
 * (tagName:"header"), and .ren-header has `will-change: transform` — which
 * creates a new containing block for any position:fixed descendant. That
 * silently shrank the overlay to the header's own box instead of the
 * viewport. Rendered on wp_footer instead, so it hangs directly off <body>. */
function ren_search_overlay_shortcode() {
	ob_start();
	?>
	<div id="ren-search-overlay" class="ren-search-overlay" aria-hidden="true">
		<button type="button" class="ren-search-overlay__close" aria-label="<?php esc_attr_e( 'Chiudi ricerca', 'ren' ); ?>">
			<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<line x1="18" y1="6" x2="6" y2="18"></line>
				<line x1="6" y1="6" x2="18" y2="18"></line>
			</svg>
		</button>
		<form role="search" method="get" class="ren-search-overlay__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" class="ren-search-overlay__input" name="s" placeholder="<?php esc_attr_e( 'Cerca nel sito…', 'ren' ); ?>" autocomplete="off" value="<?php echo esc_attr( get_search_query() ); ?>">
			<button type="submit" class="ren-search-overlay__submit" aria-label="<?php esc_attr_e( 'Avvia ricerca', 'ren' ); ?>">
				<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="5" y1="12" x2="19" y2="12"></line>
					<polyline points="12 5 19 12 12 19"></polyline>
				</svg>
			</button>
		</form>
	</div>
	<?php
	// Rendered on wp_footer (not passed through wpautop the way shortcode
	// blocks are), but collapsed the same way anyway for consistency and as
	// cheap insurance — see inc/post-nav-thumbs.php for the full story.
	echo preg_replace( '/\s*\n\s*/', ' ', trim( ob_get_clean() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup above is already escaped field-by-field.
}
add_action( 'wp_footer', 'ren_search_overlay_shortcode' );
