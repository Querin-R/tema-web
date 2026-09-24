<?php
/**
 * Title: Home Q-design
 * Slug: ren/home
 * Categories: ren-pages
 * Description: Contenuto della Home (la testata/hero si imposta nel riquadro Hero della pagina): introduzione, ultimi Appunti di grafica, presentazione con ritratto, chiusura.
 */
?>
<!-- wp:group {"tagName":"section","className":"ren-home-section ren-home-intro","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-home-section ren-home-intro">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"3rem"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"28%"} -->
		<div class="wp-block-column" style="flex-basis:28%">
			<!-- wp:paragraph {"className":"ren-section-label"} -->
			<p class="ren-section-label">Questo sito</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"72%"} -->
		<div class="wp-block-column" style="flex-basis:72%">
			<!-- wp:paragraph {"fontSize":"large"} -->
			<p class="has-large-font-size">Non è la vetrina di uno studio, ma un luogo dove mettere in ordine trent'anni di lavoro tra progettazione e prestampa, e dove continuare a scrivere di ciò che della grafica trovo ancora interessante: come nasce un'identità, come si prepara un file per la stampa, perché certe scelte funzionano.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"ren-home-section ren-home-notes","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-home-section ren-home-notes">
	<!-- wp:group {"className":"ren-home-section-head","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group ren-home-section-head">
		<!-- wp:heading {"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size">Appunti di grafica</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"ren-more-link"} -->
		<p class="ren-more-link"><a href="/appunti-di-grafica/">Tutti gli appunti →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:query {"queryId":11,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"className":"ren-blog-query ren-home-query"} -->
	<div class="wp-block-query ren-blog-query ren-home-query">
		<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
			<!-- wp:group {"className":"ren-blog-card","style":{"border":{"radius":"6px"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
			<div class="wp-block-group ren-blog-card has-surface-background-color has-background" style="border-radius:6px">
				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","sizeSlug":"ren-blog-card"} /-->
				<!-- wp:group {"style":{"spacing":{"padding":{"top":"1.25rem","bottom":"1.5rem","left":"1.25rem","right":"1.25rem"}}}} -->
				<div class="wp-block-group" style="padding-top:1.25rem;padding-right:1.25rem;padding-bottom:1.5rem;padding-left:1.25rem">
					<!-- wp:post-terms {"term":"category","fontSize":"small","className":"ren-category-badge"} /-->
					<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"large","style":{"spacing":{"margin":{"top":"0.5rem","bottom":"0.75rem"}}}} /-->
					<!-- wp:post-date {"fontSize":"small","textColor":"muted","style":{"spacing":{"margin":{"top":"1rem"}}}} /-->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"ren-home-section ren-home-about","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-home-section ren-home-about">
	<!-- wp:media-text {"mediaWidth":36,"verticalAlignment":"center","imageFill":false,"className":"ren-home-portrait"} -->
	<div class="wp-block-media-text is-stacked-on-mobile is-vertically-aligned-center ren-home-portrait" style="grid-template-columns:36% auto"><figure class="wp-block-media-text__media"></figure><div class="wp-block-media-text__content">
		<!-- wp:paragraph {"className":"ren-section-label"} -->
		<p class="ren-section-label">Chi scrive</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"medium"} -->
		<p class="has-medium-font-size">Ho cominciato quando la grafica passava dalla pellicola al digitale, e ho attraversato tutto il cambiamento del mestiere: dal progetto alla prestampa, fino al file che arriva in macchina. È questo doppio punto di vista, progettuale e produttivo, che provo a raccontare anche negli articoli.</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"ren-more-link"} -->
		<p class="ren-more-link"><a href="/esperienza/">Esperienza →</a>   <a href="/chi-sono/">Chi sono →</a></p>
		<!-- /wp:paragraph -->
	</div></div>
	<!-- /wp:media-text -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"ren-home-section ren-home-closing","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-home-section ren-home-closing">
	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size">Un'osservazione su un articolo, una segnalazione, o solo un saluto: scrivimi.</p>
	<!-- /wp:paragraph -->
	<!-- wp:shortcode -->
	[ren_email]
	<!-- /wp:shortcode -->
</section>
<!-- /wp:group -->
