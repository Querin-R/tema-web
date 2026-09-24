<?php
/**
 * Page heading: optional kicker, alternative H1 and lead text for Pages,
 * edited in the "Hero" meta box and printed by [ren_page_heading] in
 * templates/page.html and templates/front-page.html in place of the plain
 * post-title block. With no fields set it outputs exactly the page title,
 * so existing pages look unchanged.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Allowed markup in the lead text. */
function ren_page_heading_lead_kses() {
	return array(
		'a'      => array( 'href' => true, 'target' => true, 'rel' => true ),
		'strong' => array(),
		'em'     => array(),
		'br'     => array(),
	);
}

function ren_register_page_heading_meta() {
	register_post_meta(
		'page',
		'ren_hero_portrait',
		array(
			'type'          => 'integer',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_pages' );
			},
		)
	);
	foreach ( array( 'ren_hero_kicker', 'ren_hero_title', 'ren_hero_lead' ) as $key ) {
		register_post_meta(
			'page',
			$key,
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => function () {
					return current_user_can( 'edit_pages' );
				},
			)
		);
	}
}
add_action( 'init', 'ren_register_page_heading_meta' );

/**
 * Extra fields appended to the Hero meta box (pages only).
 *
 * @param WP_Post $post Page.
 */
function ren_render_page_heading_fields( $post ) {
	$kicker = get_post_meta( $post->ID, 'ren_hero_kicker', true );
	$title  = get_post_meta( $post->ID, 'ren_hero_title', true );
	$lead   = get_post_meta( $post->ID, 'ren_hero_lead', true );
	?>
	<hr />
	<p><strong><?php esc_html_e( 'Testi del titolo', 'ren' ); ?></strong><br />
	<span class="description"><?php esc_html_e( 'Valgono con o senza fascia hero. Vuoti = solo il titolo della pagina.', 'ren' ); ?></span></p>
	<p>
		<label for="ren_hero_kicker"><?php esc_html_e( 'Occhiello', 'ren' ); ?></label><br />
		<input type="text" id="ren_hero_kicker" name="ren_hero_kicker" value="<?php echo esc_attr( $kicker ); ?>" style="width:100%;" />
	</p>
	<p>
		<label for="ren_hero_title"><?php esc_html_e( 'Titolo H1 alternativo', 'ren' ); ?></label><br />
		<textarea id="ren_hero_title" name="ren_hero_title" rows="2" style="width:100%;"><?php echo esc_textarea( $title ); ?></textarea>
		<span class="description"><?php esc_html_e( 'Sostituisce il titolo della pagina solo nella testata.', 'ren' ); ?></span>
	</p>
	<p>
		<label for="ren_hero_lead"><?php esc_html_e( 'Testo sotto il titolo', 'ren' ); ?></label><br />
		<textarea id="ren_hero_lead" name="ren_hero_lead" rows="3" style="width:100%;"><?php echo esc_textarea( $lead ); ?></textarea>
		<span class="description"><?php esc_html_e( 'Ammessi link, grassetto e corsivo (HTML).', 'ren' ); ?></span>
	</p>
	<?php
	$portrait = (int) get_post_meta( $post->ID, 'ren_hero_portrait', true );
	$src      = $portrait ? wp_get_attachment_image_url( $portrait, 'medium' ) : '';
	?>
	<p>
		<label><?php esc_html_e( 'Ritratto sopra la fascia hero', 'ren' ); ?></label><br />
		<span class="description"><?php esc_html_e( 'Compare a destra del titolo, appoggiato al bordo inferiore della fascia. Ideale un PNG scontornato, verticale.', 'ren' ); ?></span>
	</p>
	<input type="hidden" id="ren_hero_portrait" name="ren_hero_portrait" value="<?php echo esc_attr( $portrait ? $portrait : '' ); ?>" />
	<div id="ren-hero-portrait-preview"><?php echo $src ? '<img src="' . esc_url( $src ) . '" style="max-width:100%;height:auto;display:block;margin-bottom:0.5rem;" />' : ''; ?></div>
	<p>
		<button type="button" class="button" id="ren-hero-portrait-upload"><?php esc_html_e( 'Scegli ritratto', 'ren' ); ?></button>
		<button type="button" class="button-link" id="ren-hero-portrait-remove"><?php esc_html_e( 'Rimuovi', 'ren' ); ?></button>
	</p>
	<script>
	( function () {
		var frame, field = document.getElementById( 'ren_hero_portrait' ), preview = document.getElementById( 'ren-hero-portrait-preview' );
		document.getElementById( 'ren-hero-portrait-upload' ).addEventListener( 'click', function ( e ) {
			e.preventDefault();
			if ( ! frame ) {
				frame = wp.media( { title: 'Scegli ritratto', button: { text: 'Usa questa immagine' }, multiple: false, library: { type: 'image' } } );
				frame.on( 'select', function () {
					var a = frame.state().get( 'selection' ).first().toJSON();
					field.value = a.id;
					preview.innerHTML = '<img src="' + ( a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url ) + '" style="max-width:100%;height:auto;display:block;margin-bottom:0.5rem;" />';
				} );
			}
			frame.open();
		} );
		document.getElementById( 'ren-hero-portrait-remove' ).addEventListener( 'click', function ( e ) {
			e.preventDefault();
			field.value = '';
			preview.innerHTML = '';
		} );
	} )();
	</script>
	<?php
}

/**
 * @param int $post_id Page ID.
 */
function ren_save_page_heading_fields( $post_id ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce already verified by ren_save_post_hero_meta().
	if ( isset( $_POST['ren_hero_kicker'] ) ) {
		update_post_meta( $post_id, 'ren_hero_kicker', sanitize_text_field( wp_unslash( $_POST['ren_hero_kicker'] ) ) );
	}
	if ( isset( $_POST['ren_hero_title'] ) ) {
		update_post_meta( $post_id, 'ren_hero_title', sanitize_textarea_field( wp_unslash( $_POST['ren_hero_title'] ) ) );
	}
	if ( isset( $_POST['ren_hero_lead'] ) ) {
		update_post_meta( $post_id, 'ren_hero_lead', wp_kses( wp_unslash( $_POST['ren_hero_lead'] ), ren_page_heading_lead_kses() ) );
	}
	if ( isset( $_POST['ren_hero_portrait'] ) ) {
		$portrait = absint( $_POST['ren_hero_portrait'] );
		if ( $portrait ) {
			update_post_meta( $post_id, 'ren_hero_portrait', $portrait );
		} else {
			delete_post_meta( $post_id, 'ren_hero_portrait' );
		}
	}
	// phpcs:enable
}

/**
 * [ren_page_heading] — kicker + H1 + lead. Single-line output: the Shortcode
 * block runs wpautop() on its result.
 */
function ren_shortcode_page_heading() {
	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return '';
	}

	$kicker = get_post_meta( $post_id, 'ren_hero_kicker', true );
	$title  = get_post_meta( $post_id, 'ren_hero_title', true );
	$lead   = get_post_meta( $post_id, 'ren_hero_lead', true );
	$title  = '' !== trim( (string) $title ) ? nl2br( esc_html( trim( $title ) ) ) : esc_html( get_the_title( $post_id ) );

	$html = '';
	if ( $kicker ) {
		$html .= '<p class="ren-hero-kicker">' . esc_html( $kicker ) . '</p>';
	}
	$html .= '<h1 class="wp-block-post-title has-xx-large-font-size ren-page-title">' . $title . '</h1>';
	if ( $lead ) {
		$lead  = nl2br( wp_kses( trim( $lead ), ren_page_heading_lead_kses() ) );
		$html .= '<div class="ren-hero-lead">' . $lead . '</div>';
	}

	$portrait = ren_page_hero_portrait_id( $post_id );
	if ( $portrait ) {
		$img  = wp_get_attachment_image( $portrait, 'large', false, array( 'class' => 'ren-hero-split__img', 'loading' => 'eager', 'fetchpriority' => 'high' ) );
		$html = '<div class="ren-hero-split"><div class="ren-hero-split__text">' . $html . '</div><div class="ren-hero-split__media">' . $img . '</div></div>';
	}

	return str_replace( array( "\r", "\n" ), '', $html );
}
add_shortcode( 'ren_page_heading', 'ren_shortcode_page_heading' );

/**
 * Portrait ID, only when the hero band is active on that page.
 *
 * @param int $post_id Page ID.
 */
function ren_page_hero_portrait_id( $post_id ) {
	if ( 'page' !== get_post_type( $post_id ) || ! get_post_meta( $post_id, 'ren_hero_enabled', true ) ) {
		return 0;
	}
	$id = (int) get_post_meta( $post_id, 'ren_hero_portrait', true );
	return ( $id && wp_attachment_is_image( $id ) ) ? $id : 0;
}

/**
 * Body class for the split hero layout.
 *
 * @param string[] $classes Body classes.
 */
function ren_page_hero_portrait_body_class( $classes ) {
	if ( is_page() && ren_page_hero_portrait_id( get_queried_object_id() ) ) {
		$classes[] = 'ren-hero-portrait';
	}
	return $classes;
}
add_filter( 'body_class', 'ren_page_hero_portrait_body_class' );
