<?php
/**
 * Timeline for the Esperienza page (patterns/esperienza.php, CSS in style.css):
 *
 * - The Competenze column stays in view while the timeline scrolls, but
 *   only when it actually fits in the window; otherwise it scrolls normally
 *   (a sticky block taller than the screen would hide its own bottom).
 * - The timeline item currently in the middle of the screen gets
 *   .is-active (filled marker, accent date).
 *
 * Printed only on pages whose content uses the .ren-tl timeline.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ren_experience_timeline_script() {
	if ( ! is_singular() ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post || false === strpos( (string) $post->post_content, 'ren-tl-item' ) ) {
		return;
	}
	?>
	<script>
	( function () {
		var skills = document.querySelector( '.ren-exp-skills' );
		function fit() {
			if ( ! skills ) { return; }
			var room = window.innerHeight - 140;
			skills.classList.toggle( 'is-sticky', window.innerWidth > 781 && skills.scrollHeight < room );
		}
		fit();
		window.addEventListener( 'resize', fit );

		var items = document.querySelectorAll( '.ren-tl-item' );
		if ( ! items.length || ! ( 'IntersectionObserver' in window ) ) { return; }
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( e ) {
				if ( e.isIntersecting ) {
					items.forEach( function ( i ) { i.classList.remove( 'is-active' ); } );
					e.target.classList.add( 'is-active' );
				}
			} );
		}, { rootMargin: '-45% 0px -50% 0px' } );
		items.forEach( function ( i ) { io.observe( i ); } );
		items[0].classList.add( 'is-active' );
	} )();
	</script>
	<?php
}
add_action( 'wp_footer', 'ren_experience_timeline_script' );
