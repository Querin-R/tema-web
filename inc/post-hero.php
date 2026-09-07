<?php
/**
 * Per-post "Magazine Hero" band — lets a single post show a full-width,
 * colored/gradient or image title band instead of the plain title, without
 * needing a separate template file (single-featured.html was retired in
 * favour of this: same visual outcome, one single.html to maintain).
 *
 * Follows the same meta-box + body_class pattern already used for
 * ren_title_align in inc/custom-post-types.php.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the hero meta fields.
 */
function ren_register_post_hero_meta() {
	register_post_meta(
		'post',
		'ren_hero_enabled',
		array(
			'type'          => 'boolean',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'ren_hero_bg_type',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => function ( $value ) {
				return 'image' === $value ? 'image' : 'color';
			},
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'ren_hero_bg_color',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_hex_color',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'ren_hero_bg_gradient',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'post',
		'ren_hero_bg_image',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'ren_register_post_hero_meta' );

/**
 * Meta box.
 */
function ren_add_post_hero_meta_box() {
	add_meta_box(
		'ren_post_hero',
		__( 'Hero', 'ren' ),
		'ren_render_post_hero_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'ren_add_post_hero_meta_box' );

/**
 * @param WP_Post $post Current post object.
 */
function ren_render_post_hero_meta_box( $post ) {
	wp_nonce_field( 'ren_save_post_hero', 'ren_post_hero_nonce' );

	$enabled      = (bool) get_post_meta( $post->ID, 'ren_hero_enabled', true );
	$bg_type      = get_post_meta( $post->ID, 'ren_hero_bg_type', true );
	$bg_type      = 'image' === $bg_type ? 'image' : 'color';
	$bg_color     = get_post_meta( $post->ID, 'ren_hero_bg_color', true );
	$bg_gradient  = get_post_meta( $post->ID, 'ren_hero_bg_gradient', true );
	$bg_image_id  = (int) get_post_meta( $post->ID, 'ren_hero_bg_image', true );
	$bg_image_url = $bg_image_id ? wp_get_attachment_image_url( $bg_image_id, 'medium' ) : '';
	?>
	<p>
		<label>
			<input type="checkbox" name="ren_hero_enabled" value="1" <?php checked( $enabled ); ?> />
			<?php esc_html_e( 'Mostra fascia hero', 'ren' ); ?>
		</label>
		<br />
		<span class="description"><?php esc_html_e( 'Fascia a piena larghezza dietro al titolo, al posto del titolo semplice.', 'ren' ); ?></span>
	</p>

	<p>
		<label style="margin-right:1rem;">
			<input type="radio" name="ren_hero_bg_type" value="color" <?php checked( $bg_type, 'color' ); ?> class="ren-hero-bg-type" />
			<?php esc_html_e( 'Colore / sfumatura', 'ren' ); ?>
		</label>
		<label>
			<input type="radio" name="ren_hero_bg_type" value="image" <?php checked( $bg_type, 'image' ); ?> class="ren-hero-bg-type" />
			<?php esc_html_e( 'Immagine', 'ren' ); ?>
		</label>
	</p>

	<div class="ren-hero-panel" data-mode="color">
		<p>
			<label for="ren_hero_bg_color"><strong><?php esc_html_e( 'Colore', 'ren' ); ?></strong></label><br />
			<span class="ren-clearable-color">
				<input type="text" id="ren_hero_bg_color" class="ren-color-picker" data-allow-empty="true" name="ren_hero_bg_color" value="<?php echo esc_attr( $bg_color ); ?>" />
				<button type="button" class="button ren-color-clear"><?php esc_html_e( 'Pulisci', 'ren' ); ?></button>
			</span>
		</p>
		<p>
			<label for="ren_hero_bg_gradient"><strong><?php esc_html_e( 'Sfumatura personalizzata (CSS)', 'ren' ); ?></strong></label><br />
			<input type="text" id="ren_hero_bg_gradient" name="ren_hero_bg_gradient" value="<?php echo esc_attr( $bg_gradient ); ?>" style="width:100%;" placeholder="linear-gradient(135deg, #b026ff, #ff5470)" />
			<span class="description"><?php esc_html_e( 'Se compilata, ha la precedenza sul colore sopra.', 'ren' ); ?></span>
		</p>
	</div>

	<div class="ren-hero-panel" data-mode="image">
		<p>
			<input type="hidden" id="ren_hero_bg_image" name="ren_hero_bg_image" value="<?php echo esc_attr( $bg_image_id ); ?>" />
			<span id="ren-hero-image-preview"><?php if ( $bg_image_url ) : ?><img src="<?php echo esc_url( $bg_image_url ); ?>" style="max-width:100%;height:auto;display:block;margin-bottom:0.5rem;" /><?php endif; ?></span>
			<button type="button" class="button" id="ren-hero-image-upload"><?php esc_html_e( 'Scegli immagine', 'ren' ); ?></button>
			<button type="button" class="button" id="ren-hero-image-remove"><?php esc_html_e( 'Rimuovi', 'ren' ); ?></button>
		</p>
	</div>

	<p class="description"><?php esc_html_e( 'Se non imposti nulla qui, la fascia hero usa il colore impostato in Opzioni Tema → Colori → Post Hero Background.', 'ren' ); ?></p>
	<?php
}

/**
 * @param int $post_id Post ID.
 */
function ren_save_post_hero_meta( $post_id ) {
	if ( ! isset( $_POST['ren_post_hero_nonce'] ) ||
		! wp_verify_nonce( $_POST['ren_post_hero_nonce'], 'ren_save_post_hero' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, 'ren_hero_enabled', isset( $_POST['ren_hero_enabled'] ) ? 1 : 0 );

	if ( isset( $_POST['ren_hero_bg_type'] ) ) {
		$type = 'image' === $_POST['ren_hero_bg_type'] ? 'image' : 'color';
		update_post_meta( $post_id, 'ren_hero_bg_type', $type );
	}

	if ( isset( $_POST['ren_hero_bg_color'] ) ) {
		$color = sanitize_hex_color( wp_unslash( $_POST['ren_hero_bg_color'] ) );
		update_post_meta( $post_id, 'ren_hero_bg_color', $color ? $color : '' );
	}

	if ( isset( $_POST['ren_hero_bg_gradient'] ) ) {
		update_post_meta( $post_id, 'ren_hero_bg_gradient', sanitize_text_field( wp_unslash( $_POST['ren_hero_bg_gradient'] ) ) );
	}

	if ( isset( $_POST['ren_hero_bg_image'] ) ) {
		update_post_meta( $post_id, 'ren_hero_bg_image', absint( $_POST['ren_hero_bg_image'] ) );
	}
}
add_action( 'save_post_post', 'ren_save_post_hero_meta' );

/**
 * Body class so style.css can target just this page.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function ren_post_hero_body_class( $classes ) {
	if ( is_singular( 'post' ) && get_post_meta( get_the_ID(), 'ren_hero_enabled', true ) ) {
		$classes[] = 'ren-hero';

		$post_id = get_the_ID();
		$bg_type = get_post_meta( $post_id, 'ren_hero_bg_type', true );
		$has_img = (int) get_post_meta( $post_id, 'ren_hero_bg_image', true );

		if ( 'image' === $bg_type && $has_img ) {
			$classes[] = 'ren-hero-image';
		}
	}
	return $classes;
}
add_filter( 'body_class', 'ren_post_hero_body_class' );

/**
 * Output this post's hero background as inline CSS custom properties on
 * <body>, read by the .ren-hero rules in style.css. Kept as inline CSS
 * (not inline style="" on the block itself) so the same style.css rules
 * still fully control layout/typography — only the background varies
 * per post. Falls back to the site-wide Theme Options color when this
 * post hasn't set anything of its own.
 */
function ren_post_hero_inline_style() {
	if ( ! is_singular( 'post' ) || ! get_post_meta( get_the_ID(), 'ren_hero_enabled', true ) ) {
		return;
	}

	$post_id      = get_the_ID();
	$bg_type      = get_post_meta( $post_id, 'ren_hero_bg_type', true );
	$bg_type      = 'image' === $bg_type ? 'image' : 'color';
	$bg_value     = '';
	$bg_value_sm  = ''; // Mobile override; only set for the image case.

	if ( 'image' === $bg_type ) {
		$image_id     = (int) get_post_meta( $post_id, 'ren_hero_bg_image', true );
		$image_url    = $image_id ? wp_get_attachment_image_url( $image_id, 'ren-hero-bg' ) : '';
		$image_url_sm = $image_id ? wp_get_attachment_image_url( $image_id, 'ren-hero-bg-mobile' ) : '';
		if ( $image_url ) {
			$bg_value = 'url(' . esc_url( $image_url ) . ') center / cover no-repeat';
		}
		if ( $image_url_sm ) {
			$bg_value_sm = 'url(' . esc_url( $image_url_sm ) . ') center / cover no-repeat';
		}
	} else {
		$gradient = get_post_meta( $post_id, 'ren_hero_bg_gradient', true );
		$color    = get_post_meta( $post_id, 'ren_hero_bg_color', true );
		if ( $gradient ) {
			$bg_value = $gradient;
		} elseif ( $color ) {
			$bg_value = $color;
		}
	}

	if ( ! $bg_value ) {
		$bg_value = 'var(--wp--custom--color--post-hero)';
	}

	echo '<style>body.ren-hero{--ren-hero-bg:' . esc_html( $bg_value ) . ';}';
	// Mobile phones get the much lighter 900x900 crop instead of the
	// 2400x1200 desktop one — only relevant when the hero background is
	// an image (color/gradient hero values don't need a mobile variant).
	if ( $bg_value_sm ) {
		echo '@media (max-width: 781px){body.ren-hero{--ren-hero-bg:' . esc_html( $bg_value_sm ) . ';}}';
	}
	echo '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $bg_value/$bg_value_sm are a sanitized hex color, a CSS gradient() the author typed themselves in wp-admin, or an escaped URL; none of it is user-submitted on the front end.
}
add_action( 'wp_head', 'ren_post_hero_inline_style' );

/**
 * Admin assets for the meta box (color picker + media uploader), loaded
 * only on the post editor screen for the 'post' post type.
 *
 * @param string $hook Current admin page hook.
 */
function ren_post_hero_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	if ( 'post' !== get_current_screen()->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	wp_enqueue_script(
		'ren-admin-post-hero',
		REN_URI . '/assets/js/admin-post-hero.js',
		array( 'jquery', 'wp-color-picker' ),
		REN_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'ren_post_hero_admin_assets' );
