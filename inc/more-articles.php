<?php
/**
 * The "Altri articoli" query loop in parts/post-more-articles.html shows
 * the 4 most recent posts — this excludes the post currently being viewed
 * from that list, using core's dedicated filter for customizing Query
 * Loop block queries (added in WP 6.1) rather than a general pre_get_posts
 * hook, so it can't accidentally affect any other query on the site.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param array         $query Args that will be merged into the block's WP_Query.
 * @param WP_Block      $block The block instance, so we can scope this to
 *                             just parts/post-more-articles.html's query
 *                             loop (className "ren-more-articles") instead
 *                             of affecting every query loop on the site.
 * @return array
 */
function ren_more_articles_exclude_current( $query, $block ) {
	$class_name = $block->context['query']['className'] ?? ( $block->parsed_block['attrs']['className'] ?? '' );

	if ( is_singular( 'post' ) && false !== strpos( $class_name, 'ren-more-articles' ) ) {
		$query['post__not_in'] = array( get_the_ID() );
	}
	return $query;
}
add_filter( 'query_loop_block_query_vars', 'ren_more_articles_exclude_current', 10, 2 );
