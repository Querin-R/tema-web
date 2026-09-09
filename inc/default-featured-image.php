<?php
/**
 * Default Featured Image — Opzioni Tema → Blog. A single site-wide
 * fallback image used everywhere a post, page or portfolio project is
 * missing its own featured image, instead of leaving a blank space
 * (the "Altri articoli" grid, the blog archive, the Previous/Next card).
 * Reuses the same option (post_nav_placeholder_id) that used to cover
 * only the Previous/Next card — see inc/theme-options.php's Blog tab,
 * where the field was renamed accordingly.
 *
 * A first version of this hooked `get_post_metadata` on '_thumbnail_id'
 * to make has_post_thumbnail() see the fallback transparently everywhere
 * at once. On paper it should have worked, but in practice it didn't
 * reliably fill in the grids — likely something to do with how WP_Query
 * primes the meta cache for a list of posts, though it wasn't possible
 * to pin down exactly why without a live environment to step through.
 * Rather than keep debugging a mechanism that touches core meta
 * internals for every post meta read on the site, this hooks the render
 * output of the specific block instead (core/post-featured-image) — a
 * narrower, easier-to-verify point that only affects what's actually
 * displayed, and covers the archive grid and "Altri articoli" grid in
 * one place since they both use that same block. The Previous/Next card
 * doesn't use that block at all (it's built with plain PHP in
 * inc/post-nav-thumbs.php), so it gets its own explicit fallback there.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param string $block_content The block's rendered HTML — empty string
 *                               when the post has no featured image.
 * @param array  $block         Parsed block data (attrs, etc.).
 * @return string
 */
function ren_default_featured_image_render( $block_content, $block ) {
	if ( '' !== trim( $block_content ) ) {
		return $block_content;
	}

	$fallback_id = (int) ren_get_options()['post_nav_placeholder_id'];
	if ( ! $fallback_id ) {
		return $block_content;
	}

	$size = isset( $block['attrs']['sizeSlug'] ) ? $block['attrs']['sizeSlug'] : 'large';
	$url  = wp_get_attachment_image_url( $fallback_id, $size );
	if ( ! $url ) {
		return $block_content;
	}

	$style = '';
	if ( ! empty( $block['attrs']['aspectRatio'] ) ) {
		$style .= 'aspect-ratio:' . esc_attr( $block['attrs']['aspectRatio'] ) . ';width:100%;object-fit:cover;';
	}
	if ( ! empty( $block['attrs']['style']['border']['radius'] ) ) {
		$style .= 'border-radius:' . esc_attr( $block['attrs']['style']['border']['radius'] ) . ';';
	}

	$img = '<img src="' . esc_url( $url ) . '" alt="" style="' . esc_attr( $style ) . '" />';

	if ( ! empty( $block['attrs']['isLink'] ) ) {
		$post_id = get_the_ID();
		if ( $post_id ) {
			$img = '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . $img . '</a>';
		}
	}

	return '<figure class="wp-block-post-featured-image">' . $img . '</figure>';
}
add_filter( 'render_block_core/post-featured-image', 'ren_default_featured_image_render', 10, 2 );
