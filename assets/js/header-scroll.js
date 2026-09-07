/**
 * Header scroll behavior:
 *  - "is-scrolled" class (always on): adds a subtle shadow once you've
 *    scrolled past a small threshold — independent of the toggle below.
 *  - Hide-on-scroll-down / reveal-on-scroll-up (only when Theme Options →
 *    Header → Scroll Behavior is enabled, body.ren-header-autohide): the
 *    header follows the scroll 1:1 in real time (no CSS transition, no
 *    discrete "hidden" jump) — this avoids the stop-start/jerky feeling
 *    that comes from a timed CSS transition getting interrupted mid-animation
 *    by the next scroll event. Using position:fixed instead of sticky here
 *    also matters for smoothness: sticky forces the browser to recompute
 *    whether the element is "stuck" on every scroll frame, which competes
 *    with our own per-frame transform update and is the main cause of jank;
 *    fixed elements don't need that recalculation at all.
 *
 * Note: the space reserved below the header (body padding-top) is set
 * directly from Theme Options → Header → Header Height (see style.css /
 * inc/theme-options.php), NOT measured here — a JS measurement would
 * override that value every time (inline styles always beat stylesheet
 * rules), making the panel setting pointless.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var header = document.querySelector( '.ren-header' );
		if ( ! header ) {
			return;
		}

		var autohideEnabled = document.body.classList.contains( 'ren-header-autohide' );

		var shadowThreshold = 40;
		var revealThreshold = 80; // Snap fully visible near the very top, regardless of direction.

		var lastScrollY = window.scrollY;
		var pinOffset = 0; // 0 = fully visible, headerHeight = fully hidden.
		var ticking = false;

		var revealSpeed = getComputedStyle( document.documentElement ).getPropertyValue( '--ren-header-reveal-speed' ).trim() || '250ms';

		function onScroll() {
			var currentScrollY = Math.max( window.scrollY, 0 );
			var delta = currentScrollY - lastScrollY;

			header.classList.toggle( 'is-scrolled', currentScrollY > shadowThreshold );

			if ( autohideEnabled ) {
				var headerHeight = header.offsetHeight;

				if ( currentScrollY <= revealThreshold || delta < 0 ) {
					// Any upward movement (or being near the top) reveals the
					// header fully — animated at the configured speed, rather
					// than snapping instantly or following the scroll amount.
					header.style.transition = 'transform ' + revealSpeed + ' ease';
					pinOffset = 0;
				} else if ( delta > 0 ) {
					// Hiding stays transition-free and 1:1 with the scroll —
					// adding a timed transition here is what caused the
					// stop-start jank, since new values arrive every frame.
					header.style.transition = 'none';
					pinOffset = Math.min( pinOffset + delta, headerHeight );
				}

				header.style.transform = pinOffset > 0 ? 'translateY(-' + pinOffset + 'px)' : '';
			}

			lastScrollY = currentScrollY;
			ticking = false;
		}

		window.addEventListener(
			'scroll',
			function () {
				if ( ! ticking ) {
					window.requestAnimationFrame( onScroll );
					ticking = true;
				}
			},
			{ passive: true }
		);

		onScroll();
	} );
} )();
