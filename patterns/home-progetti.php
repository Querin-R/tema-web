<?php
/**
 * Title: Home – Progetti
 * Slug: ren/home-progetti
 * Categories: ren-pages
 * Description: Sezione "Progetti" per la Home, con gli ultimi 3 lavori del portfolio. Da inserire quando il portfolio sarà online.
 */
?>
<!-- wp:group {"tagName":"section","className":"ren-home-section ren-home-projects","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-home-section ren-home-projects">
	<!-- wp:group {"className":"ren-home-section-head","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group ren-home-section-head">
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"},"style":{"spacing":{"blockGap":"0.5rem"}}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"className":"ren-section-label"} -->
			<p class="ren-section-label">Progetti</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"fontSize":"x-large","className":"ren-section-title"} -->
			<h2 class="wp-block-heading ren-section-title has-x-large-font-size">Alcuni lavori</h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"ren-more-link"} -->
		<p class="ren-more-link"><a href="/progetti/">Tutti i progetti →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:query {"queryId":12,"query":{"perPage":3,"pages":0,"offset":0,"postType":"portfolio","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"ren-home-query ren-home-projects-query"} -->
	<div class="wp-block-query ren-home-query ren-home-projects-query">
		<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
			<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/5","style":{"border":{"radius":"6px"}}} /-->
			<!-- wp:post-terms {"term":"portfolio_category","fontSize":"small","textColor":"accent","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.06em"},"spacing":{"margin":{"top":"1rem"}}}} /-->
			<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"medium","style":{"spacing":{"margin":{"top":"0.4rem"}}}} /-->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</section>
<!-- /wp:group -->
