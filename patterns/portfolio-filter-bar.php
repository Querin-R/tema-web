<?php
/**
 * Title: Portfolio Filter Bar
 * Slug: ren/portfolio-filter-bar
 * Categories: ren
 */
?>
<!-- wp:group {"className":"ren-toolbar","style":{"spacing":{"blockGap":"1rem","margin":{"bottom":"2.5rem"}}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
<div class="wp-block-group ren-toolbar" style="margin-bottom:2.5rem">

	<!-- wp:html -->
	<div class="ren-filter-search">
		<label class="screen-reader-text" for="ren-search">Search projects</label>
		<input
			type="search"
			id="ren-search"
			placeholder="Search projects…"
			style="width:260px;max-width:100%;padding:0.7rem 1.1rem;border-radius:999px;border:1px solid var(--wp--preset--color--border);background:var(--wp--preset--color--surface);color:var(--wp--preset--color--foreground);"
		/>
	</div>
	<!-- /wp:html -->

	<!-- wp:html -->
	<div class="ren-filter-category">
		<?php ren_portfolio_category_dropdown(); ?>
	</div>
	<!-- /wp:html -->

</div>
<!-- /wp:group -->
