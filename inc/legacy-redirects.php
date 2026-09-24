<?php
/**
 * 301 redirect for article URLs that predate the /appunti-di-grafica/ permalink
 * prefix. Covers /nome-articolo/, /blog/nome-articolo/ and date-based paths
 * (/2025/08/nome-articolo/): on a 404, the last path segment is looked up as
 * a published post slug and, if found, the visitor is sent to its permalink.
 *
 * Complements WordPress's own redirect_guess_404_permalink(), which is
 * heuristic and can be disabled by plugins.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ren_legacy_post_redirect() {
	if ( ! is_404() ) {
		return;
	}

	$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( '' === $path ) {
		return;
	}

	$segments = explode( '/', $path );
	$slug     = sanitize_title( end( $segments ) );
	if ( '' === $slug || 4 < count( $segments ) ) {
		return;
	}

	$post = get_page_by_path( $slug, OBJECT, 'post' );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return;
	}

	$target = get_permalink( $post );
	if ( $target && untrailingslashit( wp_parse_url( $target, PHP_URL_PATH ) ) !== '/' . untrailingslashit( $path ) ) {
		wp_safe_redirect( $target, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'ren_legacy_post_redirect', 5 );
