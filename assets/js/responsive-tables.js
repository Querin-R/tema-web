/**
 * For any table using the "ren-table" class (including the core Table
 * block, via its Advanced → Additional CSS class(es) field), read the
 * header cell text and copy it onto each body cell as a data-label
 * attribute. style.css uses that attribute to show column labels when
 * the table stacks into cards on narrow screens — so editors never have
 * to hand-write data-label on every cell.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var tables = document.querySelectorAll( '.ren-table, .wp-block-table.ren-table table' );

		tables.forEach( function ( table ) {
			// If "table" is actually the wrapping .ren-table div/figure, find the real <table>.
			var el = 'TABLE' === table.tagName ? table : table.querySelector( 'table' );
			if ( ! el ) {
				return;
			}

			var headerCells = el.querySelectorAll( 'thead th' );
			if ( ! headerCells.length ) {
				return;
			}

			var labels = Array.prototype.map.call( headerCells, function ( th ) {
				return th.textContent.trim();
			} );

			var rows = el.querySelectorAll( 'tbody tr' );
			rows.forEach( function ( row ) {
				var cells = row.children;
				for ( var i = 0; i < cells.length && i < labels.length; i++ ) {
					if ( ! cells[ i ].hasAttribute( 'data-label' ) ) {
						cells[ i ].setAttribute( 'data-label', labels[ i ] );
					}
				}
			} );
		} );
	} );
} )();
