<?php
/**
 * Footer + Contatti shortcodes, driven by Theme Options → Footer.
 *
 * Output is kept on a single line: the core Shortcode block runs wpautop()
 * on its result, and newlines would inject stray <p>/<br> tags.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Footer links parsed from the options textarea.
 *
 * @return array<int, array{label:string, url:string}>
 */
function ren_get_footer_links() {
	$options = ren_get_options();
	$links   = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $options['footer_links'] ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$links[] = array( 'label' => $parts[0], 'url' => $parts[1] );
		}
	}
	return $links;
}

/**
 * True when a URL points outside this site.
 *
 * @param string $url URL.
 */
function ren_is_external_url( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	return $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host;
}

/** [ren_footer_tagline] */
function ren_shortcode_footer_tagline() {
	$options = ren_get_options();
	return $options['footer_tagline'] ? '<span class="ren-footer-tagline">' . esc_html( $options['footer_tagline'] ) . '</span>' : '';
}
add_shortcode( 'ren_footer_tagline', 'ren_shortcode_footer_tagline' );

/** [ren_email] — mailto link, obfuscated with antispambot(). */
function ren_shortcode_email() {
	$options = ren_get_options();
	$email   = $options['footer_email'];
	if ( ! $email || ! is_email( $email ) ) {
		return '';
	}
	return sprintf( '<a class="ren-email" href="mailto:%1$s">%2$s</a>', antispambot( $email, 1 ), antispambot( $email ) );
}
add_shortcode( 'ren_email', 'ren_shortcode_email' );

/** [ren_footer_links] — extra links (Paissangroup, ecc.) as a vertical list. */
function ren_shortcode_footer_links() {
	$links = ren_get_footer_links();
	if ( empty( $links ) ) {
		return '';
	}
	$html = '<ul class="ren-footer-links">';
	foreach ( $links as $link ) {
		$ext   = ren_is_external_url( $link['url'] ) ? ' target="_blank" rel="noopener"' : '';
		$html .= sprintf( '<li><a href="%1$s"%2$s>%3$s</a></li>', esc_url( $link['url'] ), $ext, esc_html( $link['label'] ) );
	}
	return $html . '</ul>';
}
add_shortcode( 'ren_footer_links', 'ren_shortcode_footer_links' );

/** [ren_legal_links] — Privacy (WP setting) · Cookie (Theme Options). */
function ren_shortcode_legal_links() {
	$options = ren_get_options();
	$items   = array();
	$privacy = get_privacy_policy_url();
	if ( $privacy ) {
		$items[] = '<a href="' . esc_url( $privacy ) . '">Privacy</a>';
	}
	if ( $options['footer_cookie_url'] ) {
		$items[] = '<a href="' . esc_url( $options['footer_cookie_url'] ) . '">Cookie</a>';
	}
	return $items ? '<span class="ren-legal-links">' . implode( '<span aria-hidden="true"> · </span>', $items ) . '</span>' : '';
}
add_shortcode( 'ren_legal_links', 'ren_shortcode_legal_links' );
