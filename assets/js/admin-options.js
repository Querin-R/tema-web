( function ( $ ) {
	'use strict';

	$( function () {

		// ── Sidebar tabs ──────────────────────────────────────
		var $tabs = $( '.ren-options-sidebar .ren-nav-item' );
		var $panels = $( '.ren-tab-panel' );

		function activateTab( slug ) {
			if ( ! slug || ! $( '#ren-tab-' + slug ).length ) {
				slug = $tabs.first().data( 'tab' );
			}
			$tabs.removeClass( 'is-active' );
			$tabs.filter( '[data-tab="' + slug + '"]' ).addClass( 'is-active' );
			$panels.hide();
			$( '#ren-tab-' + slug ).show();
			try {
				window.localStorage.setItem( 'renOptionsActiveTab', slug );
			} catch ( err ) {
				/* localStorage unavailable — tab just won't persist across reloads. */
			}
		}

		$tabs.on( 'click', function ( e ) {
			e.preventDefault();
			activateTab( $( this ).data( 'tab' ) );
		} );

		var initialTab = window.location.hash ? window.location.hash.replace( '#ren-tab-', '' ) : null;
		if ( ! initialTab ) {
			try {
				initialTab = window.localStorage.getItem( 'renOptionsActiveTab' );
			} catch ( err ) {
				initialTab = null;
			}
		}
		activateTab( initialTab );

		// Reset-to-defaults confirmation.
		$( '#ren-reset-options' ).on( 'click', function ( e ) {
			if ( ! window.confirm( 'Reset all Theme Options to their defaults? This cannot be undone.' ) ) {
				e.preventDefault();
			}
		} );

		// Color pickers. Fields marked data-allow-empty may have no value
		// (meaning "automatic, follow the Color Scheme") — default the
		// picker's own swatch to a neutral color in that case so Iris
		// doesn't complain, without writing that color into the field.
		$( '.ren-color-picker' ).each( function () {
			var $input = $( this );
			var allowEmpty = $input.data( 'allow-empty' );
			$input.wpColorPicker( {
				defaultColor: allowEmpty ? '#000000' : false,
				change: function ( event, ui ) {
					if ( allowEmpty ) {
						$input.val( ui.color.toString() );
					}
				},
			} );
		} );

		$( '.ren-color-clear' ).on( 'click', function ( e ) {
			e.preventDefault();
			var $wrapper = $( this ).closest( '.ren-clearable-color' );
			var $input = $wrapper.find( '.ren-color-picker' );
			$input.val( '' );
			$input.wpColorPicker( 'color', '' );
			$wrapper.find( '.wp-color-result' ).css( 'background', 'transparent' );
		} );

		// Logo media uploader.
		var frame;
		var $idField = $( '#ren-logo-id' );
		var $preview = $( '#ren-logo-preview' );

		$( '#ren-logo-upload' ).on( 'click', function ( e ) {
			e.preventDefault();

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: 'Choose Logo',
				button: { text: 'Use this logo' },
				multiple: false,
				library: { type: 'image' },
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				$idField.val( attachment.id );
				$preview.html(
					'<img src="' + attachment.url + '" style="max-width:200px;height:auto;display:block;" />'
				);
			} );

			frame.open();
		} );

		$( '#ren-logo-remove' ).on( 'click', function ( e ) {
			e.preventDefault();
			$idField.val( '' );
			$preview.html( '' );
		} );

		// Previous/Next placeholder image media uploader — same pattern as
		// the logo uploader above, kept as its own separate frame/vars so
		// the two don't fight over "frame" being open at the same time.
		var navPlaceholderFrame;
		var $navPlaceholderIdField = $( '#ren-post-nav-placeholder-id' );
		var $navPlaceholderPreview = $( '#ren-post-nav-placeholder-preview' );

		$( '#ren-post-nav-placeholder-upload' ).on( 'click', function ( e ) {
			e.preventDefault();

			if ( navPlaceholderFrame ) {
				navPlaceholderFrame.open();
				return;
			}

			navPlaceholderFrame = wp.media( {
				title: 'Choose Placeholder Image',
				button: { text: 'Use this image' },
				multiple: false,
				library: { type: 'image' },
			} );

			navPlaceholderFrame.on( 'select', function () {
				var attachment = navPlaceholderFrame.state().get( 'selection' ).first().toJSON();
				$navPlaceholderIdField.val( attachment.id );
				$navPlaceholderPreview.html(
					'<img src="' + attachment.url + '" style="width:80px;height:80px;object-fit:cover;border-radius:4px;display:block;" />'
				);
			} );

			navPlaceholderFrame.open();
		} );

		$( '#ren-post-nav-placeholder-remove' ).on( 'click', function ( e ) {
			e.preventDefault();
			$navPlaceholderIdField.val( '' );
			$navPlaceholderPreview.html( '' );
		} );

		// Font mode toggle: show only the panel matching the selected radio.
		$( '.ren-font-field' ).each( function () {
			var $field = $( this );

			function syncPanels() {
				var mode = $field.find( '.ren-font-mode:checked' ).val();
				$field.find( '.ren-font-mode-panel' ).each( function () {
					$( this ).toggle( $( this ).data( 'mode' ) === mode );
				} );
			}

			$field.find( '.ren-font-mode' ).on( 'change', syncPanels );
			syncPanels();
		} );

		// Font file uploader (WOFF2/WOFF/TTF/OTF from the Media Library).
		var fontFrames = {};
		$( '.ren-font-file-row' ).each( function () {
			var $row = $( this );
			var role = $row.data( 'role' );
			var slot = $row.data( 'slot' );
			var format = $row.data( 'format' );
			var key = role + '-' + slot + '-' + format;
			var $input = $row.find( '.ren-font-file-input' );
			var $filename = $row.find( '.ren-font-filename' );

			$row.find( '.ren-font-file-upload' ).on( 'click', function ( e ) {
				e.preventDefault();

				if ( fontFrames[ key ] ) {
					fontFrames[ key ].open();
					return;
				}

				fontFrames[ key ] = wp.media( {
					title: 'Choose ' + format.toUpperCase() + ' File',
					button: { text: 'Use this file' },
					multiple: false,
				} );

				fontFrames[ key ].on( 'select', function () {
					var attachment = fontFrames[ key ].state().get( 'selection' ).first().toJSON();
					$input.val( attachment.url );
					$filename.text( attachment.filename || attachment.url ).css( 'color', '#2E7D32' );
				} );

				fontFrames[ key ].open();
			} );

			$row.find( '.ren-font-file-remove' ).on( 'click', function ( e ) {
				e.preventDefault();
				$input.val( '' );
				$filename.text( 'No file' ).css( 'color', '#aaa' );
			} );
		} );
	} );
} )( jQuery );
