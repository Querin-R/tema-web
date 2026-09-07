( function ( $ ) {
	'use strict';

	$( function () {

		// Color picker (same convention as Theme Options: data-allow-empty
		// fields may have no value, meaning "fall back to the site-wide
		// Theme Options color").
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

		// Color vs Image toggle.
		function syncHeroPanels() {
			var mode = $( '.ren-hero-bg-type:checked' ).val();
			$( '.ren-hero-panel' ).each( function () {
				$( this ).toggle( $( this ).data( 'mode' ) === mode );
			} );
		}
		$( '.ren-hero-bg-type' ).on( 'change', syncHeroPanels );
		syncHeroPanels();

		// Hero image uploader.
		var frame;
		var $idField = $( '#ren_hero_bg_image' );
		var $preview = $( '#ren-hero-image-preview' );

		$( '#ren-hero-image-upload' ).on( 'click', function ( e ) {
			e.preventDefault();

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: 'Scegli immagine hero',
				button: { text: 'Usa questa immagine' },
				multiple: false,
				library: { type: 'image' },
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				$idField.val( attachment.id );
				$preview.html(
					'<img src="' + attachment.url + '" style="max-width:100%;height:auto;display:block;margin-bottom:0.5rem;" />'
				);
			} );

			frame.open();
		} );

		$( '#ren-hero-image-remove' ).on( 'click', function ( e ) {
			e.preventDefault();
			$idField.val( '' );
			$preview.html( '' );
		} );

	} );
} )( jQuery );
