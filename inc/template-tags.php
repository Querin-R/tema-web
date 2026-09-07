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
 * [ren_post_date] — calendar icon + date, used in templates/single.html
 * in place of the core wp:post-date block. Rendering it ourselves (instead
 * of relying on the block's fontSize/textColor attributes) is what makes
 * the icon and the exact size/color reliable: WordPress's global styles
 * print inline CSS that sets font-size/font-weight directly on core block
 * markup, and that wins over the block's own attributes more often than
 * not — see ren_reading_time_shortcode below for the same reasoning. The
 * icon's color is set as an inline style (not just a stylesheet class) so
 * it renders correctly regardless of the surrounding CSS cascade — e.g.
 * inside the Hero band, which force-sets a white color on every
 * descendant for contrast against arbitrary photos.
 */
function ren_meta_date_shortcode() {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}

	$icon = '<svg class="ren-meta-icon" style="color:var(--wp--preset--color--accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';

	return sprintf(
		'<span class="ren-meta-item">%1$s %2$s</span>',
		$icon,
		esc_html( get_the_date() )
	);
}
add_shortcode( 'ren_post_date', 'ren_meta_date_shortcode' );

/**
 * [ren_post_author] — user icon + author name, used in templates/single.html
 * in place of the core wp:post-author block. Looks up the display name
 * directly from the post's author ID (get_post_field + get_the_author_meta)
 * instead of calling get_the_author(), which reads the global $authordata —
 * that global isn't reliably populated at the exact moment a shortcode
 * inside a block template renders, which is what made the name disappear
 * while the icon (which needs no post data) still showed up fine.
 */
function ren_meta_author_shortcode() {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}

	$post_id   = get_the_ID();
	$author_id = (int) get_post_field( 'post_author', $post_id );
	$name      = $author_id ? get_the_author_meta( 'display_name', $author_id ) : '';

	if ( ! $name ) {
		return '';
	}

	$icon = '<svg class="ren-meta-icon" style="color:var(--wp--preset--color--accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';

	return sprintf(
		'<span class="ren-meta-item">%1$s %2$s</span>',
		$icon,
		esc_html( $name )
	);
}
add_shortcode( 'ren_post_author', 'ren_meta_author_shortcode' );

/**
 * [ren_reading_time] — clock icon + number + "min", used in
 * templates/single.html. Previously plain English text ("X min read");
 * the icon avoids mixing English into an otherwise Italian article header.
 * Shares .ren-meta-item/.ren-meta-icon with the date/author shortcodes
 * above so all three are sized and colored by one CSS rule in style.css.
 */
function ren_reading_time_shortcode() {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}

	$icon = '<svg class="ren-meta-icon" style="color:var(--wp--preset--color--accent)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"></circle><polyline points="12 7 12 12 15.5 14"></polyline></svg>';

	return sprintf(
		'<span class="ren-meta-item">%1$s %2$d min</span>',
		$icon,
		ren_reading_time()
	);
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
