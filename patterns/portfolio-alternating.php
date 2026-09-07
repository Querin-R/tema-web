<?php
/**
 * Title: Recent Work (Alternating)
 * Slug: ren/portfolio-alternating
 * Categories: ren-home
 */
?>
<!-- wp:group {"tagName":"section","className":"ren-recent-work","style":{"spacing":{"padding":{"top":"var:custom|spacing|section-gap","bottom":"var:custom|spacing|section-gap","left":"1.5rem","right":"1.5rem"}}},"backgroundColor":"background","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-recent-work has-background-background-color has-background" style="padding-top:var(--wp--custom--spacing--section-gap);padding-right:1.5rem;padding-bottom:var(--wp--custom--spacing--section-gap);padding-left:1.5rem">

	<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"},"style":{"spacing":{"margin":{"bottom":"3rem"}}}} -->
	<div class="wp-block-group" style="margin-bottom:3rem">
		<!-- wp:heading -->
		<h2 class="wp-block-heading">Recent Work</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size"><a href="/work/">View all projects →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:query {"queryId":1,"query":{"perPage":4,"postType":"portfolio","orderBy":"menu_order","order":"asc","inherit":false},"className":"ren-alt"} -->
	<div class="wp-block-query ren-alt">
		<!-- wp:post-template -->

			<!-- wp:group {"className":"ren-alt-row","style":{"spacing":{"blockGap":"3rem","margin":{"bottom":"var:custom|spacing|section-gap"}}},"layout":{"type":"flex","flexWrap":"wrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group ren-alt-row" style="margin-bottom:var(--wp--custom--spacing--section-gap)">

				<!-- wp:group {"style":{"layout":{"flexSize":"55%"}},"layout":{"type":"constrained"}} -->
				<div class="wp-block-group" style="flex-basis:55%">
					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"border":{"radius":"6px"}}} /-->
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"style":{"layout":{"flexSize":"40%"}},"layout":{"type":"constrained"}} -->
				<div class="wp-block-group" style="flex-basis:40%">

					<!-- wp:post-terms {"term":"portfolio_category","textColor":"accent","fontSize":"small","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.08em"}}} /-->

					<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"x-large","style":{"spacing":{"margin":{"top":"0.75rem","bottom":"1rem"}}}} /-->

					<!-- wp:post-excerpt {"excerptLength":20,"textColor":"muted"} /-->

				</div>
				<!-- /wp:group -->

			</div>
			<!-- /wp:group -->

		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->

</section>
<!-- /wp:group -->
