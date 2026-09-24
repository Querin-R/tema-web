<?php
/**
 * "Fascia a tutta larghezza" — a style for the core Group block.
 *
 * The group keeps its content inside the 1280px grid, while its background
 * (palette color, custom color or gradient, chosen in the editor's Color
 * panel) extends edge to edge of the browser window.
 *
 * Technique: the background is painted with border-image outset, which is
 * "ink overflow" — it never creates a horizontal scrollbar, unlike 100vw
 * boxes. The value comes from a --ren-band custom property that
 * ren_band_render() below injects from the block's own color attributes,
 * so any color picked in the editor works without touching the theme.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ren_register_band_style() {
	register_block_style(
		'core/group',
		array(
			'name'  => 'ren-band',
			'label' => __( 'Fascia a tutta larghezza', 'ren' ),
		)
	);
}
add_action( 'init', 'ren_register_band_style' );

/**
 * Button styles matching the .ren-btn CSS classes (see style.css), so the
 * same look is available both as a class on any link and from the Styles
 * panel of the core Button block.
 */
function ren_register_button_styles() {
	register_block_style(
		'core/button',
		array(
			'name'  => 'ren-fill',
			'label' => __( 'Pieno con freccia', 'ren' ),
		)
	);
	register_block_style(
		'core/button',
		array(
			'name'  => 'ren-text',
			'label' => __( 'Testo con freccia', 'ren' ),
		)
	);
}
add_action( 'init', 'ren_register_button_styles' );

/**
 * Background of a Group block as a CSS <image> (colors become a flat gradient).
 *
 * @param array $attrs Block attributes.
 * @return string Empty when the block has no background.
 */
function ren_band_background( $attrs ) {
	$style = isset( $attrs['style']['color'] ) ? $attrs['style']['color'] : array();

	if ( ! empty( $style['gradient'] ) ) {
		return $style['gradient'];
	}
	if ( ! empty( $attrs['gradient'] ) ) {
		return 'var(--wp--preset--gradient--' . sanitize_key( $attrs['gradient'] ) . ')';
	}

	$color = '';
	if ( ! empty( $style['background'] ) ) {
		$color = $style['background'];
		// Colors picked from the palette through the custom picker are
		// stored as "var:preset|color|slug".
		if ( 0 === strpos( $color, 'var:preset|color|' ) ) {
			$color = 'var(--wp--preset--color--' . sanitize_key( substr( $color, 17 ) ) . ')';
		}
	} elseif ( ! empty( $attrs['backgroundColor'] ) ) {
		$color = 'var(--wp--preset--color--' . sanitize_key( $attrs['backgroundColor'] ) . ')';
	}

	return $color ? 'linear-gradient(' . $color . ',' . $color . ')' : '';
}

/**
 * Inject --ren-band on groups using the band style.
 *
 * @param string $html  Rendered block.
 * @param array  $block Parsed block.
 */
function ren_band_render( $html, $block ) {
	if ( empty( $block['attrs']['className'] ) || false === strpos( $block['attrs']['className'], 'is-style-ren-band' ) ) {
		return $html;
	}

	$bg = ren_band_background( $block['attrs'] );
	if ( '' === $bg || preg_match( '/[<>"{};]/', $bg ) ) {
		return $html;
	}

	$processor = new WP_HTML_Tag_Processor( $html );
	if ( $processor->next_tag() ) {
		$current = (string) $processor->get_attribute( 'style' );
		$processor->set_attribute( 'style', '--ren-band:' . $bg . ';' . $current );
		return $processor->get_updated_html();
	}
	return $html;
}
add_filter( 'render_block_core/group', 'ren_band_render', 10, 2 );
