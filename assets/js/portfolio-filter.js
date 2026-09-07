/**
 * Client-side filter + search for the Portfolio archive grid.
 * Works against whatever the Query Loop block has already rendered on the
 * page — no extra requests, so it also works with browser back/forward.
 *
 * Expects markup shaped like:
 *   <div class="ren-toolbar">
 *     <input id="ren-search" type="search">
 *     <select id="ren-category"><option value="all">…</option>…</select>
 *   </div>
 *   <ul class="ren-grid">
 *     <li class="ren-project-card" data-title="…" data-categories="branding,web-design">…</li>
 *   </ul>
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var searchInput = document.getElementById( 'ren-search' );
		var categorySelect = document.getElementById( 'ren-category' );
		var grid = document.querySelector( '.ren-grid' );

		if ( ! grid ) {
			return;
		}

		var cards = Array.prototype.slice.call( grid.querySelectorAll( '.ren-project-card' ) );
		var emptyState = document.querySelector( '.ren-empty' );

		function applyFilters() {
			var query = searchInput ? searchInput.value.trim().toLowerCase() : '';
			var category = categorySelect ? categorySelect.value : 'all';
			var visibleCount = 0;

			cards.forEach( function ( card ) {
				var title = ( card.getAttribute( 'data-title' ) || '' ).toLowerCase();
				var categories = ( card.getAttribute( 'data-categories' ) || '' ).toLowerCase();

				var matchesQuery = ! query || title.indexOf( query ) !== -1 || categories.indexOf( query ) !== -1;
				var matchesCategory = 'all' === category || categories.split( ',' ).indexOf( category ) !== -1;

				var visible = matchesQuery && matchesCategory;
				card.style.display = visible ? '' : 'none';
				if ( visible ) {
					visibleCount++;
				}
			} );

			if ( emptyState ) {
				emptyState.style.display = visibleCount === 0 ? '' : 'none';
			}

			// Keep the current filter state in the URL so it survives reloads/sharing.
			if ( window.history && window.history.replaceState ) {
				var params = new URLSearchParams( window.location.search );
				query ? params.set( 's', query ) : params.delete( 's' );
				( category && category !== 'all' ) ? params.set( 'cat', category ) : params.delete( 'cat' );
				var newUrl = window.location.pathname + ( params.toString() ? '?' + params.toString() : '' );
				window.history.replaceState( null, '', newUrl );
			}
		}

		// Restore filter state from the URL on load.
		var initialParams = new URLSearchParams( window.location.search );
		if ( searchInput && initialParams.get( 's' ) ) {
			searchInput.value = initialParams.get( 's' );
		}
		if ( categorySelect && initialParams.get( 'cat' ) ) {
			categorySelect.value = initialParams.get( 'cat' );
		}

		if ( searchInput ) {
			searchInput.addEventListener( 'input', applyFilters );
		}
		if ( categorySelect ) {
			categorySelect.addEventListener( 'change', applyFilters );
		}

		applyFilters();
	} );
} )();
