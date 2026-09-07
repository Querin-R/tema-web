/**
 * Scroll-triggered animations: [ren_counter] count-up, and the skill-bar
 * fills on the "Skills & Experience" page pattern. The [ren_hover] and
 * [ren_separator] shortcodes are pure CSS/markup and need no JS.
 */
( function () {
	'use strict';

	function animateCounter( el ) {
		var target = parseFloat( el.getAttribute( 'data-ren-counter' ) ) || 0;
		var decimals = parseInt( el.getAttribute( 'data-counter-decimals' ), 10 ) || 0;
		var prefix = el.getAttribute( 'data-counter-prefix' ) || '';
		var suffix = el.getAttribute( 'data-counter-suffix' ) || '';
		var duration = 1400;
		var start = null;

		function step( timestamp ) {
			if ( ! start ) {
				start = timestamp;
			}
			var progress = Math.min( ( timestamp - start ) / duration, 1 );
			var eased = 1 - Math.pow( 1 - progress, 3 );
			var current = ( target * eased ).toFixed( decimals );
			el.textContent = prefix + current + suffix;

			if ( progress < 1 ) {
				window.requestAnimationFrame( step );
			}
		}

		window.requestAnimationFrame( step );
	}

	function animateSkillBar( el ) {
		var target = parseFloat( el.getAttribute( 'data-ren-skill' ) ) || 0;
		var fill = el.querySelector( '.ren-skill__fill' );
		if ( fill ) {
			// Delay one frame so the CSS transition actually runs.
			window.requestAnimationFrame( function () {
				fill.style.width = target + '%';
			} );
		}
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var counters = document.querySelectorAll( '.ren-counter' );
		var skills = document.querySelectorAll( '.ren-skill' );

		if ( ! counters.length && ! skills.length ) {
			return;
		}

		if ( ! ( 'IntersectionObserver' in window ) ) {
			counters.forEach( animateCounter );
			skills.forEach( animateSkillBar );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries, obs ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						if ( entry.target.classList.contains( 'ren-counter' ) ) {
							animateCounter( entry.target );
						} else {
							animateSkillBar( entry.target );
						}
						obs.unobserve( entry.target );
					}
				} );
			},
			{ threshold: 0.5 }
		);

		counters.forEach( function ( el ) {
			observer.observe( el );
		} );
		skills.forEach( function ( el ) {
			observer.observe( el );
		} );
	} );
} )();
