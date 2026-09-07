<?php
/**
 * Social links + decorative shortcodes — ported from René's existing "Ren"
 * theme (inc/helpers.php). The layout-picker/SVG-thumbnail helpers from
 * that file were left out; they only served the classic-theme archive
 * layout switcher, which Ren Studio doesn't use.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Active social links as [ slug => url ], reading from Theme Options.
 *
 * @return array<string, string>
 */
function ren_get_social_links() {
	$options = ren_get_options();
	$slugs   = array( 'instagram', 'flickr', '500px', 'twitter', 'linkedin', 'github' );
	$links   = array();

	foreach ( $slugs as $slug ) {
		$key = 'social_' . $slug;
		if ( ! empty( $options[ $key ] ) ) {
			$links[ $slug ] = esc_url( $options[ $key ] );
		}
	}

	return $links;
}

/**
 * Inline SVG icon for a social network.
 *
 * @param string $slug Social slug (instagram, github, etc).
 * @param int    $size Pixel size.
 * @return string SVG markup, or '' if the slug is unknown.
 */
function ren_social_icon( $slug, $size = 20 ) {
	$icons = array(
		'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>',
		'github'    => '<path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>',
		'linkedin'  => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>',
		'twitter'   => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
		'flickr'    => '<path d="M0 12c0 3.074 2.494 5.568 5.568 5.568 3.075 0 5.569-2.494 5.569-5.568 0-3.075-2.494-5.568-5.569-5.568C2.494 6.432 0 8.925 0 12zm12.863 0c0 3.074 2.494 5.568 5.568 5.568C21.505 17.568 24 15.074 24 12c0-3.075-2.495-5.568-5.569-5.568-3.074 0-5.568 2.493-5.568 5.568z"/>',
		'500px'     => '<path d="M8.567 9.865c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zm0-1.25c1.38 0 2.5 1.12 2.5 2.5s-1.12 2.5-2.5 2.5-2.5-1.12-2.5-2.5 1.12-2.5 2.5-2.5zm8.683-1.05V6.28h-1.284V5h-1.284v1.28H13.4v1.285h1.282v1.283h1.284V7.565h1.284zm-8.683-3.05C4.49 4.515 2 7.004 2 10.065c0 3.06 2.49 5.55 5.567 5.55 1.798 0 3.397-.857 4.41-2.185l-.94-.94c-.78.98-1.96 1.625-3.47 1.625C5.175 14.115 3.284 12.224 3.284 10.065c0-2.158 1.891-4.05 4.283-4.05 2.158 0 3.94 1.566 4.23 3.638h1.287C12.79 7.032 10.71 4.515 8.567 4.515zm7.398 1.75h-2.567V4.98h3.852v7.7h-1.285V6.265z"/>',
	);

	$path = isset( $icons[ $slug ] ) ? $icons[ $slug ] : '';
	if ( ! $path ) {
		return '';
	}

	return sprintf(
		'<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">%2$s</svg>',
		$size,
		$path
	);
}

/**
 * [ren_social_links] — renders the configured social profiles as an icon row.
 */
function ren_shortcode_social_links() {
	$links = ren_get_social_links();
	if ( empty( $links ) ) {
		return '';
	}

	$labels = array(
		'instagram' => 'Instagram',
		'flickr'    => 'Flickr',
		'500px'     => '500px',
		'twitter'   => 'X / Twitter',
		'linkedin'  => 'LinkedIn',
		'github'    => 'GitHub',
	);

	$html = '<div class="ren-social-links">';
	foreach ( $links as $slug => $url ) {
		$label = isset( $labels[ $slug ] ) ? $labels[ $slug ] : ucfirst( $slug );
		$icon  = ren_social_icon( $slug, 18 );
		$html .= sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s" title="%s">%s</a>',
			esc_url( $url ),
			esc_attr( $label ),
			esc_attr( $label ),
			$icon
		);
	}
	$html .= '</div>';

	return $html;
}
add_shortcode( 'ren_social_links', 'ren_shortcode_social_links' );

/**
 * [ren_hover variant="fade|accent|gradient|zoom|blur|slide|border" url="…" title="…" category="…"]
 *   <img src="…" />
 * [/ren_hover]
 * Wraps an image with a hover overlay showing a title/category and a "view" arrow.
 */
function ren_shortcode_hover( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'variant'  => 'fade',
			'url'      => '',
			'title'    => '',
			'category' => '',
		),
		$atts,
		'ren_hover'
	);

	$variant  = sanitize_html_class( $atts['variant'] );
	$url      = esc_url( $atts['url'] );
	$title    = esc_html( $atts['title'] );
	$category = esc_html( $atts['category'] );

	$overlay = '';
	if ( $title ) {
		$overlay .= '<span class="ren-hover__title">' . $title . '</span>';
	}
	if ( $category ) {
		$overlay .= '<span class="ren-hover__category">' . $category . '</span>';
	}
	if ( $url ) {
		$overlay .= '<div class="ren-hover__icons"><a href="' . $url . '" class="ren-hover__icon" aria-label="' . esc_attr__( 'View project', 'ren' ) . '">'
			. '<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1 7h12M7 1l6 6-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
			. '</a></div>';
	}

	$tag  = $url ? 'a' : 'div';
	$href = $url ? ' href="' . $url . '"' : '';

	return sprintf(
		'<%1$s class="ren-hover ren-hover--%2$s"%3$s>%4$s<div class="ren-hover__overlay">%5$s</div></%1$s>',
		$tag,
		$variant,
		$href,
		do_shortcode( $content ? $content : '' ),
		$overlay
	);
}
add_shortcode( 'ren_hover', 'ren_shortcode_hover' );

/**
 * [ren_counter value="40" prefix="" suffix="k+" decimals="0"]
 * Animated count-up number (see assets/js/decorative-shortcodes.js).
 */
function ren_shortcode_counter( $atts ) {
	$atts = shortcode_atts(
		array(
			'value'    => '0',
			'prefix'   => '',
			'suffix'   => '',
			'decimals' => '0',
		),
		$atts,
		'ren_counter'
	);

	return sprintf(
		'<span class="ren-counter" data-ren-counter="%1$s" data-counter-prefix="%2$s" data-counter-suffix="%3$s" data-counter-decimals="%4$s">%5$s%6$s%7$s</span>',
		esc_attr( $atts['value'] ),
		esc_attr( $atts['prefix'] ),
		esc_attr( $atts['suffix'] ),
		esc_attr( $atts['decimals'] ),
		esc_html( $atts['prefix'] ),
		esc_html( $atts['value'] ),
		esc_html( $atts['suffix'] )
	);
}
add_shortcode( 'ren_counter', 'ren_shortcode_counter' );

/**
 * [ren_separator type="wave|triangle|slash|curve" color="#000000" position="top|bottom" height="60"]
 * Decorative SVG divider between sections.
 */
function ren_shortcode_separator( $atts ) {
	$atts = shortcode_atts(
		array(
			'type'     => 'wave',
			'color'    => '#0a0a0a',
			'position' => 'bottom',
			'height'   => '60',
		),
		$atts,
		'ren_separator'
	);

	$color    = esc_attr( $atts['color'] );
	$position = 'top' === $atts['position'] ? ' ren-separator--top' : '';
	$height   = absint( $atts['height'] );

	$shapes = array(
		'wave'     => '<path d="M0,30 C150,60 350,0 500,30 C650,60 850,0 1000,30 L1000,60 L0,60 Z" fill="' . $color . '"/>',
		'triangle' => '<polygon points="0,60 500,0 1000,60" fill="' . $color . '"/>',
		'slash'    => '<polygon points="0,60 0,20 1000,0 1000,60" fill="' . $color . '"/>',
		'curve'    => '<path d="M0,60 Q500,0 1000,60 Z" fill="' . $color . '"/>',
	);

	$shape = isset( $shapes[ $atts['type'] ] ) ? $shapes[ $atts['type'] ] : $shapes['wave'];

	return sprintf(
		'<div class="ren-separator%1$s" style="height:%2$dpx"><svg viewBox="0 0 1000 60" preserveAspectRatio="none">%3$s</svg></div>',
		$position,
		$height,
		$shape
	);
}
add_shortcode( 'ren_separator', 'ren_shortcode_separator' );
