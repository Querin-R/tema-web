<?php
/**
 * Small render-time helpers used by the block templates/patterns.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the <select> used by the portfolio filter bar, populated from the
 * portfolio_category taxonomy. Used inside patterns/portfolio-filter-bar.php.
 */
function ren_portfolio_category_dropdown() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'portfolio_category',
			'hide_empty' => true,
		)
	);

	echo '<select id="ren-category" class="ren-filter-select" aria-label="' . esc_attr__( 'Filter projects by category', 'ren' ) . '">';
	echo '<option value="all">' . esc_html__( 'All Work', 'ren' ) . '</option>';

	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			printf( '<option value="%1$s">%2$s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
		}
	}

	echo '</select>';
}

/**
 * Inject data-title / data-categories attributes onto the project-card
 * wrapper block so assets/js/portfolio-filter.js can filter/search it
 * client-side, without a custom block. Targets any block carrying the
 * "ren-project-card" className, wherever it sits inside a Query Loop —
 * global $post is correctly scoped here because Post Template renders each
 * item via the_post() before recursing into its inner blocks.
 *
 * @param string   $block_content Rendered block HTML.
 * @param array    $block         Parsed block data.
 * @return string
 */
function ren_tag_project_card_block( $block_content, $block ) {
	if ( empty( $block['attrs']['className'] ) || false === strpos( $block['attrs']['className'], 'ren-project-card' ) ) {
		return $block_content;
	}

	if ( 'portfolio' !== get_post_type() ) {
		return $block_content;
	}

	$title      = esc_attr( get_the_title() );
	$categories = wp_get_post_terms( get_the_ID(), 'portfolio_category', array( 'fields' => 'slugs' ) );
	$categories = is_wp_error( $categories ) ? '' : esc_attr( implode( ',', $categories ) );

	// Inject the attributes into the first opening tag only.
	$block_content = preg_replace(
		'/^(\s*<[a-zA-Z0-9]+)/',
		'$1 data-title="' . $title . '" data-categories="' . $categories . '"',
		$block_content,
		1
	);

	return $block_content;
}
add_filter( 'render_block', 'ren_tag_project_card_block', 10, 2 );

/**
 * Estimated reading time for the single post template (optimized-typography
 * requirement — shown above the title).
 *
 * @param int $post_id Optional. Defaults to current post.
 * @return int Minutes, minimum 1.
 */
function ren_reading_time( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$content = get_post_field( 'post_content', $post_id );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	return max( 1, (int) round( $word_count / 200 ) );
}

/**
 * [ren_copyright] — used by parts/footer.html since block templates are
 * static markup and can't run raw PHP for a dynamic year.
 */
function ren_copyright_shortcode() {
	return sprintf(
		/* translators: 1: current year, 2: site name */
		esc_html__( '© %1$s %2$s — All rights reserved.', 'ren' ),
		esc_html( gmdate( 'Y' ) ),
		esc_html( get_bloginfo( 'name' ) )
	);
}
add_shortcode( 'ren_copyright', 'ren_copyright_shortcode' );

/**
 * [ren_reading_time] — "X min read", used in templates/single.html.
 */
function ren_reading_time_shortcode() {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}
	/* translators: %d: number of minutes */
	return sprintf( esc_html__( '%d min read', 'ren' ), ren_reading_time() );
}
add_shortcode( 'ren_reading_time', 'ren_reading_time_shortcode' );

/**
 * [ren_project_specs] — renders the Project Specs meta box fields as a
 * definition list. Used by templates/single-portfolio.html.
 */
function ren_project_specs_shortcode() {
	if ( ! is_singular( 'portfolio' ) ) {
		return '';
	}

	$post_id = get_the_ID();
	$fields  = array(
		'ren_client' => __( 'Client', 'ren' ),
		'ren_role'   => __( 'Role', 'ren' ),
		'ren_year'   => __( 'Year', 'ren' ),
		'ren_tools'  => __( 'Tools', 'ren' ),
	);

	ob_start();
	echo '<dl class="ren-project-specs">';
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post_id, $key, true );
		if ( '' === $value ) {
			continue;
		}
		printf( '<dt>%1$s</dt><dd>%2$s</dd>', esc_html( $label ), esc_html( $value ) );
	}
	echo '</dl>';

	$project_url = get_post_meta( $post_id, 'ren_project_url', true );
	if ( $project_url ) {
		printf(
			'<a class="wp-element-button ren-project-visit" href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( $project_url ),
			esc_html__( 'Visit Live Project ↗', 'ren' )
		);
	}

	return ob_get_clean();
}
add_shortcode( 'ren_project_specs', 'ren_project_specs_shortcode' );
