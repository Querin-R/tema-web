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

/**
 * [ren_email] — mailto link that never appears in the HTML.
 *
 * The address is split into user and domain, each reversed and base64
 * encoded into data attributes; a small script (ren_email_script) puts it
 * back together in the visitor's browser after the page loads. Harvesters
 * that read the HTML without running JavaScript find nothing usable.
 * Without JavaScript a short notice is shown instead.
 */
function ren_shortcode_email() {
	$options = ren_get_options();
	$email   = $options['footer_email'];
	if ( ! $email || ! is_email( $email ) ) {
		return '';
	}

	list( $user, $domain ) = explode( '@', $email, 2 );
	$GLOBALS['ren_email_used'] = true;

	return sprintf(
		'<a class="ren-email ren-email--protected" href="#" rel="nofollow" data-u="%1$s" data-d="%2$s"><span class="ren-email__fallback">%3$s</span></a>',
		esc_attr( base64_encode( strrev( $user ) ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- intentional, anti-harvesting.
		esc_attr( base64_encode( strrev( $domain ) ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		esc_html__( 'Indirizzo email protetto: abilita JavaScript per vederlo.', 'ren' )
	);
}
add_shortcode( 'ren_email', 'ren_shortcode_email' );

/** Rebuilds protected addresses; printed only on pages that use [ren_email]. */
function ren_email_script() {
	if ( empty( $GLOBALS['ren_email_used'] ) ) {
		return;
	}
	echo "<script>(function(){function d(s){try{return atob(s).split('').reverse().join('');}catch(e){return '';}}document.querySelectorAll('.ren-email--protected[data-u]').forEach(function(a){var e=d(a.dataset.u)+'@'+d(a.dataset.d);if(e.length<3){return;}a.href='mailto:'+e;a.textContent=e;a.classList.remove('ren-email--protected');a.removeAttribute('data-u');a.removeAttribute('data-d');});})();</script>\n";
}
add_action( 'wp_footer', 'ren_email_script', 99 );

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

/** [ren_footer_brand_image] — optional logo/marchio image (e.g. Paissangroup), linked if a URL is set. */
function ren_shortcode_footer_brand_image() {
	$options  = ren_get_options();
	$image_id = $options['footer_brand_image_id'];
	if ( ! $image_id ) {
		return '';
	}

	$img = wp_get_attachment_image( $image_id, 'medium', false, array( 'class' => 'ren-footer-brand-image', 'style' => 'max-height:32px;width:auto;' ) );
	if ( ! $img ) {
		return '';
	}

	if ( $options['footer_brand_url'] ) {
		$ext = ren_is_external_url( $options['footer_brand_url'] ) ? ' target="_blank" rel="noopener"' : '';
		return sprintf( '<a href="%1$s"%2$s>%3$s</a>', esc_url( $options['footer_brand_url'] ), $ext, $img );
	}

	return $img;
}
add_shortcode( 'ren_footer_brand_image', 'ren_shortcode_footer_brand_image' );

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
