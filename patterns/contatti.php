<?php
/**
 * Title: Contatti
 * Slug: ren/contatti
 * Categories: ren-pages
 * Keywords: contatti, email, social, scrivimi
 * Description: Pagina Contatti: breve testo ed email in grande (dalle Theme Options → Footer), poi i social con il nome. Nessun modulo.
 */
?>
<!-- wp:group {"tagName":"section","className":"ren-home-section ren-contact-main","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-home-section ren-contact-main">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"3rem"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"28%"} -->
		<div class="wp-block-column" style="flex-basis:28%">
			<!-- wp:paragraph {"className":"ren-section-label"} -->
			<p class="ren-section-label">Scrivimi</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"72%"} -->
		<div class="wp-block-column" style="flex-basis:72%">
			<!-- wp:paragraph {"className":"ren-contact-intro"} -->
			<p class="ren-contact-intro">Un'osservazione su un articolo, una segnalazione, una domanda sul mestiere o semplicemente un saluto: il modo più diretto è l'email.</p>
			<!-- /wp:paragraph -->
			<!-- wp:shortcode -->
			[ren_email]
			<!-- /wp:shortcode -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"ren-home-section ren-contact-social","layout":{"type":"constrained","contentSize":"1280px"}} -->
<section class="wp-block-group ren-home-section ren-contact-social">
	<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"3rem"}}}} -->
	<div class="wp-block-columns">
		<!-- wp:column {"width":"28%"} -->
		<div class="wp-block-column" style="flex-basis:28%">
			<!-- wp:paragraph {"className":"ren-section-label"} -->
			<p class="ren-section-label">Altrove</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"72%"} -->
		<div class="wp-block-column" style="flex-basis:72%">
			<!-- wp:shortcode -->
			[ren_social_links labels="1"]
			<!-- /wp:shortcode -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
