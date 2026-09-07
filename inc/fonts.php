<?php
/**
 * Typography engine — ported from René's existing "Ren" theme panel.
 * Supports two independent modes per role (heading/body): a curated
 * Google Fonts list, or an uploaded local font file. Each local slot
 * accepts WOFF2/WOFF/TTF/OTF (matching the old Ren theme's format
 * options) — only WOFF2 is required, the rest are optional fallbacks
 * added to the same @font-face's src list.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allow font file uploads in the Media Library (woff2/ttf/otf are off by
 * default in core; woff is usually already allowed).
 */
function ren_allow_font_mimes( $mimes ) {
	$mimes['woff2'] = 'font/woff2';
	$mimes['woff']  = 'font/woff';
	$mimes['ttf']   = 'font/ttf';
	$mimes['otf']   = 'font/otf';
	return $mimes;
}
add_filter( 'upload_mimes', 'ren_allow_font_mimes' );

/**
 * Some WP/PHP builds mis-detect font MIME types from the file signature;
 * fix them explicitly by extension so uploads aren't rejected.
 */
function ren_fix_font_filetype( $data, $file, $filename, $mimes ) {
	$ext   = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	$types = array(
		'woff2' => 'font/woff2',
		'woff'  => 'font/woff',
		'ttf'   => 'font/ttf',
		'otf'   => 'font/otf',
	);
	if ( isset( $types[ $ext ] ) ) {
		$data['ext']  = $ext;
		$data['type'] = $types[ $ext ];
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'ren_fix_font_filetype', 10, 4 );

/**
 * Curated Google Fonts, grouped by category — ported verbatim from the Ren
 * theme so the same names/organization are familiar across both themes.
 */
function ren_get_google_fonts_list() {
	return array(
		'Serif (titoli, eleganza)'              => array( 'Playfair Display', 'Cormorant Garamond', 'Libre Baskerville', 'Lora', 'Merriweather', 'Crimson Pro', 'Bitter', 'PT Serif' ),
		'Sans-serif (testo, modernità)'          => array( 'Inter', 'Bai Jamjuree', 'Poppins', 'Montserrat', 'Work Sans', 'Karla', 'Source Sans Pro', 'Nunito Sans', 'DM Sans', 'Manrope', 'Space Grotesk', 'Outfit', 'Sora' ),
		'Sans-serif classici'                    => array( 'Roboto', 'Open Sans', 'Lato', 'Raleway', 'Mukta', 'Rubik' ),
		'Display / decorativi (solo titoli)'     => array( 'Bebas Neue', 'Oswald', 'Abril Fatface', 'Fraunces', 'Archivo Black', 'Anton' ),
		'Monospace (codice, dettagli tecnici)'   => array( 'JetBrains Mono', 'Space Mono', 'IBM Plex Mono', 'Roboto Mono' ),
	);
}

/**
 * Flattened list of every curated Google Font name, for quick validation.
 */
function ren_get_google_fonts_flat() {
	$flat = array();
	foreach ( ren_get_google_fonts_list() as $fonts ) {
		foreach ( $fonts as $font ) {
			$flat[] = $font;
		}
	}
	return $flat;
}

/**
 * Resolve the active font-family name for a role, whichever mode is active.
 *
 * @param string $role heading|body.
 * @return string Font family name (may be empty if local mode has no name yet).
 */
function ren_get_active_font_family( $role ) {
	$options = ren_get_options();
	$mode    = $options[ "font_{$role}_mode" ];

	if ( 'local' === $mode ) {
		return $options[ "font_{$role}_family_local" ];
	}

	return $options[ "font_{$role}_family_google" ];
}

/**
 * Full CSS font-family value (family name + generic fallback stack) for use
 * directly in a CSS custom property, e.g. --wp--preset--font-family--body.
 *
 * @param string $role heading|body.
 * @return string e.g. '"Inter", -apple-system, BlinkMacSystemFont, sans-serif'
 */
function ren_font_css_stack( $role ) {
	$family   = ren_get_active_font_family( $role );
	$fallback = 'heading' === $role
		? '-apple-system, BlinkMacSystemFont, sans-serif'
		: '-apple-system, BlinkMacSystemFont, sans-serif';

	if ( ! $family ) {
		$family = 'heading' === $role ? 'Space Grotesk' : 'Inter';
	}

	return '"' . esc_attr( $family ) . '", ' . $fallback;
}

/**
 * Build the Google Fonts stylesheet URL for whichever roles are in Google
 * mode. Returns '' if both roles use local fonts (or are empty).
 */
function ren_get_google_fonts_url() {
	$options = ren_get_options();
	$fonts   = array();

	foreach ( array( 'heading', 'body' ) as $role ) {
		if ( 'google' === $options[ "font_{$role}_mode" ] ) {
			$family = $options[ "font_{$role}_family_google" ];
			if ( $family && in_array( $family, ren_get_google_fonts_flat(), true ) ) {
				$fonts[] = $family;
			}
		}
	}

	$fonts = array_unique( $fonts );
	if ( empty( $fonts ) ) {
		return '';
	}

	$families_param = implode(
		'&family=',
		array_map(
			function ( $f ) {
				return str_replace( ' ', '+', $f ) . ':wght@400;500;600;700';
			},
			$fonts
		)
	);

	return 'https://fonts.googleapis.com/css2?family=' . $families_param . '&display=swap';
}

/**
 * Enqueue the Google Fonts stylesheet (with preconnect) if any role needs it.
 */
function ren_enqueue_google_fonts() {
	$url = ren_get_google_fonts_url();
	if ( ! $url ) {
		return;
	}
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	echo '<link rel="stylesheet" href="' . esc_url( $url ) . '">' . "\n";
}
add_action( 'wp_head', 'ren_enqueue_google_fonts', 3 );

/**
 * Build the @font-face rules (if any) for whichever roles are in local mode
 * and have an uploaded WOFF2 file. Returns a plain CSS string — used both
 * for the front-end <style> tag and for injection into the block editor's
 * preview iframe (see ren_inject_editor_styles() in inc/theme-options.php).
 *
 * @return string
 */
function ren_get_font_face_css() {
	$options = ren_get_options();
	$rules   = array();

	if ( 'local' === $options['font_heading_mode'] && $options['font_heading_family_local'] ) {
		$sources = ren_get_font_slot_sources( $options, 'heading', 'regular' );
		if ( ! empty( $sources ) ) {
			$rules[] = ren_build_font_face_rule( $options['font_heading_family_local'], $sources, $options['font_heading_local_weight'], 'normal' );
		}
	}

	if ( 'local' === $options['font_body_mode'] && $options['font_body_family_local'] ) {
		$regular_sources = ren_get_font_slot_sources( $options, 'body', 'regular' );
		if ( ! empty( $regular_sources ) ) {
			$rules[] = ren_build_font_face_rule( $options['font_body_family_local'], $regular_sources, '400', 'normal' );
		}

		$bold_sources = ren_get_font_slot_sources( $options, 'body', 'bold' );
		if ( ! empty( $bold_sources ) ) {
			$rules[] = ren_build_font_face_rule( $options['font_body_family_local'], $bold_sources, '700', 'normal' );
		}
	}

	return implode( "\n", $rules );
}

/**
 * Gather every uploaded format URL for one role/slot, in browser-priority
 * order (WOFF2 first, then WOFF, TTF, OTF — the browser uses the first
 * format it understands from the resulting src list).
 *
 * @param array  $options Saved theme options.
 * @param string $role    heading|body.
 * @param string $slot    regular|bold.
 * @return array<string,string> [ format => url ], only formats that have a file.
 */
function ren_get_font_slot_sources( $options, $role, $slot ) {
	$sources = array();
	foreach ( array( 'woff2', 'woff', 'ttf', 'otf' ) as $format ) {
		$key = "font_{$role}_{$slot}_{$format}";
		if ( ! empty( $options[ $key ] ) ) {
			$sources[ $format ] = $options[ $key ];
		}
	}
	return $sources;
}

/**
 * Output @font-face rules on the front end.
 */
function ren_output_font_face() {
	$css = ren_get_font_face_css();
	if ( '' === $css ) {
		return;
	}
	echo "\n<style id=\"ren-font-face\">\n" . $css . "\n</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- built from sanitized/escaped values in ren_build_font_face_rule().
}
add_action( 'wp_head', 'ren_output_font_face', 4 );

/**
 * @param string               $family  Font family name.
 * @param array<string,string> $sources [ format => url ], e.g. [ 'woff2' => '…', 'ttf' => '…' ].
 * @param string               $weight  Numeric font-weight.
 * @param string               $style   normal|italic.
 * @return string One @font-face block with a multi-format src list.
 */
function ren_build_font_face_rule( $family, $sources, $weight, $style ) {
	$format_names = array(
		'woff2' => 'woff2',
		'woff'  => 'woff',
		'ttf'   => 'truetype',
		'otf'   => 'opentype',
	);

	$src_parts = array();
	foreach ( $sources as $format => $url ) {
		if ( isset( $format_names[ $format ] ) ) {
			$src_parts[] = "url('" . esc_url( $url ) . "') format('" . $format_names[ $format ] . "')";
		}
	}

	return "@font-face {\n"
		. "  font-family: '" . esc_attr( $family ) . "';\n"
		. '  src: ' . implode( ",\n       ", $src_parts ) . ";\n"
		. "  font-weight: {$weight};\n"
		. "  font-style: {$style};\n"
		. "  font-display: swap;\n"
		. '}';
}
