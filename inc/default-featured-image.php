<?php
/**
 * Default Featured Image — Opzioni Tema → Blog. A single site-wide
 * fallback image used everywhere a post, page or portfolio project is
 * missing its own featured image, instead of leaving a blank space
 * (the "Altri articoli" grid, the blog archive, the Previous/Next card,
 * etc.). Reuses the same option (post_nav_placeholder_id) that used to
 * cover only the Previous/Next card — see inc/theme-options.php's Blog
 * tab, where the field was renamed accordingly.
 *
 * Implemented as a single get_post_metadata filter on '_thumbnail_id'
 * rather than editing every template individually: this makes
 * has_post_thumbnail(), get_the_post_thumbnail(), and the native
 * Featured Image block all transparently see the fallback as if it were
 * the post's own thumbnail, with no per-template special-casing needed.
 * (This also means inc/post-nav-thumbs.php no longer needs its own
 * separate fallback branch — has_post_thumbnail() already accounts for
 * it automatically now.)
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param mixed  $value     The value to return, a single metadata value, or an array of values.
 * @param int    $object_id Post ID.
 * @param string $meta_key  Metadata key.
 * @param bool   $single    Whether to return a single value.
 * @return mixed
 */
function ren_default_featured_image_fallback( $value, $object_id, $meta_key, $single ) {
	if ( '_thumbnail_id' !== $meta_key ) {
		return $value;
	}

	// Re-entrancy guard: get_post_meta() below would otherwise trigger
	// this same filter again, since it also reads '_thumbnail_id'.
	static $resolving = false;
	if ( $resolving ) {
		return $value;
	}

	$resolving = true;
	$existing  = get_post_meta( $object_id, '_thumbnail_id', true );
	$resolving = false;

	// A real featured image is already set — leave WordPress's normal
	// behaviour untouched.
	if ( $existing ) {
		return $value;
	}

	$fallback_id = (int) ren_get_options()['post_nav_placeholder_id'];
	if ( ! $fallback_id ) {
		return $value;
	}

	return $single ? (string) $fallback_id : array( (string) $fallback_id );
}
add_filter( 'get_post_metadata', 'ren_default_featured_image_fallback', 10, 4 );
