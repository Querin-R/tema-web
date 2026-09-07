<?php
/**
 * Title: Latest News (Stacked List)
 * Slug: ren/latest-news
 * Categories: ren-home
 */
?>
<!-- wp:group {"tagName":"section","className":"ren-latest-news","style":{"spacing":{"padding":{"top":"var:custom|spacing|section-gap","bottom":"var:custom|spacing|section-gap","left":"1.5rem","right":"1.5rem"}}},"backgroundColor":"background","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-latest-news has-background-background-color has-background" style="padding-top:var(--wp--custom--spacing--section-gap);padding-right:1.5rem;padding-bottom:var(--wp--custom--spacing--section-gap);padding-left:1.5rem">

	<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"margin":{"bottom":"3rem"}}}} -->
	<div class="wp-block-group" style="margin-bottom:3rem">
		<!-- wp:heading -->
		<h2 class="wp-block-heading">Latest News</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size"><a href="/blog/">View all articles →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:query {"queryId":6,"query":{"perPage":5,"postType":"post","inherit":true},"className":"ren-latest-news-list","layout":{"type":"default"}} -->
	<div class="wp-block-query ren-latest-news-list">
		<!-- wp:post-template -->

			<!-- wp:group {"tagName":"article","className":"ren-news-item","style":{"spacing":{"blockGap":"1.25rem","margin":{"bottom":"var:custom|spacing|section-gap"}}},"layout":{"type":"constrained"}} -->
			<article class="wp-block-group ren-news-item" style="margin-bottom:var(--wp--custom--spacing--section-gap)">

				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","style":{"border":{"radius":"6px"}}} /-->

				<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"x-large","style":{"spacing":{"margin":{"top":"0","bottom":"0.5rem"}}}} /-->

				<!-- wp:group {"layout":{"type":"flex"},"style":{"spacing":{"margin":{"bottom":"0.75rem"}}}} -->
				<div class="wp-block-group" style="margin-bottom:0.75rem">
					<!-- wp:paragraph {"fontSize":"small","textColor":"muted"} -->
					<p class="has-muted-color has-text-color has-small-font-size">By <?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:post-terms {"term":"category","fontSize":"small","textColor":"accent"} /-->
				</div>
				<!-- /wp:group -->

				<!-- wp:post-excerpt {"excerptLength":30,"textColor":"muted"} /-->

			</article>
			<!-- /wp:group -->

		<!-- /wp:post-template -->

		<!-- wp:query-no-results -->
			<!-- wp:paragraph {"textColor":"muted"} -->
			<p class="has-muted-color has-text-color">Nessun articolo pubblicato ancora.</p>
			<!-- /wp:paragraph -->
		<!-- /wp:query-no-results -->
	</div>
	<!-- /wp:query -->

</section>
<!-- /wp:group -->
