<?php
/**
 * Previous/next navigation with thumbnails — [ren_post_nav]. Works on any
 * singular post type (posts, portfolio projects) with no per-type setup:
 * get_adjacent_post() already scopes its lookup to the current post's own
 * post type. Inspired by the "add thumbnails to next/previous navigation"
 * pattern common in several themes (e.g. Kadence), reimplemented natively
 * here since Ren Studio doesn't use Kadence's filter hooks.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render one side (previous or next) of the nav — its own function so the
 * "no thumbnail" and "no adjacent post" fallbacks aren't duplicated.
 *
 * @param WP_Post|null $adjacent  The adjacent post, or null if there isn't one.
 * @param string       $direction 'prev' or 'next'.
 * @return string
 */
function ren_post_nav_render_side( $adjacent, $direction ) {
	if ( ! $adjacent ) {
		return '<span class="ren-post-nav__item ren-post-nav__item--empty"></span>';
	}

	$label = 'prev' === $direction
		? __( 'Articolo precedente', 'ren' )
		: __( 'Articolo successivo', 'ren' );

	if ( 'portfolio' === get_post_type( $adjacent ) ) {
		$label = 'prev' === $direction
			? __( 'Progetto precedente', 'ren' )
			: __( 'Progetto successivo', 'ren' );
	}

	$arrow = 'prev' === $direction ? '&larr;' : '&rarr;';

	ob_start();
	?>
	<a href="<?php echo esc_url( get_permalink( $adjacent ) ); ?>" class="ren-post-nav__item ren-post-nav__item--<?php echo esc_attr( $direction ); ?>">
		<span class="ren-post-nav__label">
			<?php if ( 'prev' === $direction ) : ?>
				<span aria-hidden="true"><?php echo $arrow; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed HTML entity, not user input */ ?></span> <?php echo esc_html( $label ); ?>
			<?php else : ?>
				<?php echo esc_html( $label ); ?> <span aria-hidden="true"><?php echo $arrow; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed HTML entity, not user input */ ?></span>
			<?php endif; ?>
		</span>
		<?php if ( has_post_thumbnail( $adjacent ) ) : ?>
			<span class="ren-post-nav__thumb">
				<?php echo get_the_post_thumbnail( $adjacent, 'medium', array( 'loading' => 'lazy', 'alt' => get_the_title( $adjacent ) ) ); ?>
			</span>
		<?php else :
			$placeholder_id  = (int) ren_get_options()['post_nav_placeholder_id'];
			$placeholder_url = $placeholder_id ? wp_get_attachment_image_url( $placeholder_id, 'medium' ) : '';
			if ( $placeholder_url ) :
				?>
				<span class="ren-post-nav__thumb ren-post-nav__thumb--placeholder">
					<img src="<?php echo esc_url( $placeholder_url ); ?>" loading="lazy" alt="" />
				</span>
			<?php endif; ?>
		<?php endif; ?>
		<span class="ren-post-nav__title"><?php echo esc_html( get_the_title( $adjacent ) ); ?></span>
	</a>
	<?php
	// This template is written multi-line/indented for readability, but that
	// whitespace ends up in the output — and WordPress's wpautop() (run on
	// shortcode output elsewhere in the page-render pipeline) converts each
	// newline between tags into a literal <br />. Each <br> then becomes its
	// own flex item inside .ren-post-nav__item's column layout, multiplying
	// the .ren-post-nav__item gap between every element instead of applying
	// it once.
	//
	// An earlier version of this fix only collapsed whitespace strictly
	// BETWEEN two tags (`/>\s+</`) — which happened to fix the "prev" side
	// (its span starts with a nested <span> right away) but missed the
	// "next" side, where the newline after <span class="...label"> is
	// followed by plain text ("Articolo successivo"), not another tag, so
	// that pattern never matched it. Matching every literal newline
	// character directly — regardless of what's on either side of it — and
	// replacing it (plus its surrounding indentation) with a single space
	// removes anything wpautop could act on, full stop, rather than only
	// the specific tag-to-tag case.
	return preg_replace( '/\s*\n\s*/', ' ', trim( ob_get_clean() ) );
}

/**
 * [ren_post_nav]
 */
function ren_post_nav_shortcode() {
	if ( ! is_singular() ) {
		return '';
	}

	$prev = get_adjacent_post( false, '', true );
	$next = get_adjacent_post( false, '', false );

	if ( ! $prev && ! $next ) {
		return '';
	}

	return '<div class="ren-post-nav">'
		. ren_post_nav_render_side( $prev, 'prev' )
		. ren_post_nav_render_side( $next, 'next' )
		. '</div>';
}
add_shortcode( 'ren_post_nav', 'ren_post_nav_shortcode' );
