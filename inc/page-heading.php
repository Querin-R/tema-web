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

	return str_replace( array( "\r", "\n" ), '', $html );
}
add_shortcode( 'ren_page_heading', 'ren_shortcode_page_heading' );
