<?php
/**
 * Title: Contatti (minimale)
 * Slug: ren/contatti
 * Categories: ren-pages
 * Description: Pagina Contatti essenziale: breve testo, email e social. Nessun modulo.
 */
?>
<!-- wp:group {"className":"ren-contatti","layout":{"type":"constrained"},"style":{"spacing":{"blockGap":"1.5rem"}}} -->
<div class="wp-block-group ren-contatti">

	<!-- wp:paragraph {"fontSize":"medium"} -->
	<p class="has-medium-font-size">Per uno scambio di idee su un articolo, una segnalazione o semplicemente per un saluto, il modo più diretto è scrivermi.</p>
	<!-- /wp:paragraph -->

	<!-- wp:shortcode -->
	[ren_email]
	<!-- /wp:shortcode -->

	<!-- wp:shortcode -->
	[ren_social_links]
	<!-- /wp:shortcode -->

</div>
<!-- /wp:group -->
