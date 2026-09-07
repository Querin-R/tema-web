<?php
/**
 * Title: Newsletter CTA
 * Slug: ren/newsletter-cta
 * Categories: ren-home
 */
?>
<!-- wp:group {"tagName":"section","className":"ren-newsletter","style":{"spacing":{"padding":{"top":"var:custom|spacing|section-gap","bottom":"var:custom|spacing|section-gap","left":"1.5rem","right":"1.5rem"}}},"gradient":"violet-fade","backgroundColor":"surface","layout":{"type":"constrained","contentSize":"800px","justifyContent":"center"}} -->
<section class="wp-block-group ren-newsletter has-surface-background-color has-violet-fade-gradient-background has-background" style="padding-top:var(--wp--custom--spacing--section-gap);padding-right:1.5rem;padding-bottom:var(--wp--custom--spacing--section-gap);padding-left:1.5rem">

	<!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
	<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Occasional dispatches, no spam.</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","textColor":"muted","style":{"spacing":{"margin":{"bottom":"2rem"}}}} -->
	<p class="has-text-align-center has-muted-color has-text-color" style="margin-bottom:2rem">New projects, photo essays, and the odd behind-the-scenes note — straight to your inbox.</p>
	<!-- /wp:paragraph -->

	<!-- wp:html -->
	<form class="ren-newsletter-form" action="#" method="post" style="display:flex;gap:0.75rem;flex-wrap:wrap;justify-content:center;">
		<label class="screen-reader-text" for="ren-newsletter-email">Email address</label>
		<input type="email" id="ren-newsletter-email" name="email" placeholder="you@example.com" required
			style="flex:1 1 260px;padding:0.9rem 1.25rem;border-radius:999px;border:1px solid var(--wp--preset--color--border);background:var(--wp--preset--color--background);color:var(--wp--preset--color--foreground);" />
		<button type="submit" class="wp-element-button" style="border-radius:999px;padding:0.9rem 1.75rem;background:var(--wp--preset--color--accent);color:var(--wp--preset--color--background);border:0;font-weight:600;cursor:pointer;">
			Subscribe
		</button>
	</form>
	<!-- /wp:html -->

	<!-- wp:paragraph {"align":"center","fontSize":"small","textColor":"muted","style":{"spacing":{"margin":{"top":"1rem"}}}} -->
	<p class="has-text-align-center has-muted-color has-text-color has-small-font-size" style="margin-top:1rem">Connect your email provider (e.g. Mailchimp/Brevo) to wire this form up — see the notes in README.md.</p>
	<!-- /wp:paragraph -->

</section>
<!-- /wp:group -->
