<?php
/**
 * Outputs the Custom CSS / Custom JS fields from Theme Options on the
 * front end. Ported from René's existing "Ren" theme (inc/custom-css-js.php).
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom CSS → <head>, after all other styles so it can override them.
 */
function ren_output_custom_css() {
	$options = ren_get_options();
	$css     = trim( $options['custom_css'] );

	if ( '' === $css ) {
		return;
	}

	echo "\n<style id=\"ren-custom-css\">\n" . $css . "\n</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- sanitized in ren_sanitize_custom_css().
}
add_action( 'wp_head', 'ren_output_custom_css', 99 );

/**
 * Custom JS → <head>.
 */
function ren_output_custom_js_head() {
	$options = ren_get_options();
	$js      = trim( $options['custom_js_head'] );

	if ( '' === $js ) {
		return;
	}

	echo "\n<script id=\"ren-custom-js-head\">\n" . $js . "\n</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- sanitized in ren_sanitize_custom_js().
}
add_action( 'wp_head', 'ren_output_custom_js_head', 100 );

/**
 * Custom JS → footer, right before </body>.
 */
function ren_output_custom_js_footer() {
	$options = ren_get_options();
	$js      = trim( $options['custom_js_footer'] );

	if ( '' === $js ) {
		return;
	}

	echo "\n<script id=\"ren-custom-js-footer\">\n" . $js . "\n</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- sanitized in ren_sanitize_custom_js().
}
add_action( 'wp_footer', 'ren_output_custom_js_footer', 100 );
