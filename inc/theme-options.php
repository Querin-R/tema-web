<?php
/**
 * "Theme Options" admin panel — lets a non-technical user pick the accent
 * color, logo, typography (local font upload or curated Google Fonts),
 * custom CSS/JS, and social links without touching the Site Editor.
 * Values are stored as a single option and rendered as CSS custom-property
 * overrides (+ @font-face / <style>/<script> output) in wp_head, so they
 * layer on top of theme.json rather than replacing it.
 *
 * Typography, custom CSS/JS, social links, and the decorative shortcodes
 * were ported from René's existing "Ren" (GeneratePress child) theme
 * panel — see inc/fonts.php and inc/social-shortcodes.php.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REN_OPTIONS_KEY', 'ren_theme_options' );

/**
 * Curated accent color choices, matching the brief's three options.
 * A "Custom" entry unlocks the color picker for any other value.
 */
function ren_accent_choices() {
	return array(
		'#b026ff' => 'Electric Violet',
		'#baff29' => 'Acid Green',
		'#ff5470' => 'Hot Coral',
		'custom'  => 'Custom…',
	);
}

/**
 * Default option values.
 */
function ren_default_options() {
	return array(
		// Branding.
		'accent_preset'  => '#b026ff',
		'accent_custom'  => '#b026ff',
		'accent_hover'   => '',
		'logo_id'        => 0,
		'logo_width'     => '160',
		'color_scheme'   => 'light',

		// Header — logo spacing + nav font.
		'logo_margin_top'    => '16',
		'logo_margin_bottom' => '16',
		'header_height'      => '95',
		'header_reveal_speed' => '250',
		'header_nav_size'    => '11',
		'header_nav_weight'  => '600',
		'header_nav_spacing' => '12',
		'header_autohide'    => '0',

		// Blog — archive layout.
		'blog_layout' => 'grid',
		'post_title_align' => 'left',
		'post_nav_placeholder_id' => 0,
		'color_more_articles_bg' => '',

		// Advanced — self-hosted Font Awesome (no external CDN request; see the EU GDPR note in the field description).
		'enable_font_awesome' => '0',
		'enable_codemirror'   => '0',

		// Colors — element-level overrides (blank = automatic, follows Color Scheme).
		'color_heading'    => '',
		'color_body'       => '',
		'color_link'       => '',
		'color_link_hover' => '',
		'color_post_hero'  => '',
		'color_table_header_bg'   => '',
		'color_table_header_text' => '',
		'color_table_row_1'       => '',
		'color_table_row_2'       => '',

		// Typography — heading.
		'font_heading_mode'          => 'google',
		'font_heading_family_google' => 'Space Grotesk',
		'font_heading_family_local'  => '',
		'font_heading_local_weight'  => '700',
		'font_heading_regular_woff2' => '',
		'font_heading_regular_woff'  => '',
		'font_heading_regular_ttf'   => '',
		'font_heading_regular_otf'   => '',

		// Typography — body.
		'font_body_mode'          => 'google',
		'font_body_family_google' => 'Inter',
		'font_body_family_local'  => '',
		'font_body_regular_woff2' => '',
		'font_body_regular_woff'  => '',
		'font_body_regular_ttf'   => '',
		'font_body_regular_otf'   => '',
		'font_body_bold_woff2'    => '',
		'font_body_bold_woff'     => '',
		'font_body_bold_ttf'      => '',
		'font_body_bold_otf'      => '',

		// Typography — sizes (px). Blank = automatic (theme's fluid clamp() default).
		'font_size_small'    => '',
		'font_size_medium'   => '',
		'font_size_large'    => '',
		'font_size_xlarge'   => '',
		'font_size_xxlarge'  => '',
		'font_size_hero'     => '',

		// Typography — advanced per-tag size/weight/line-height. Blank = automatic.
		'typo_h1_size' => '', 'typo_h1_weight' => '', 'typo_h1_lh' => '',
		'typo_h2_size' => '', 'typo_h2_weight' => '', 'typo_h2_lh' => '',
		'typo_h3_size' => '', 'typo_h3_weight' => '', 'typo_h3_lh' => '',
		'typo_h4_size' => '', 'typo_h4_weight' => '', 'typo_h4_lh' => '',
		'typo_h5_size' => '', 'typo_h5_weight' => '', 'typo_h5_lh' => '',
		'typo_h6_size' => '', 'typo_h6_weight' => '', 'typo_h6_lh' => '',
		'typo_body_size' => '', 'typo_body_weight' => '', 'typo_body_lh' => '',
		'typo_lead_size' => '', 'typo_lead_weight' => '', 'typo_lead_lh' => '',
		'typo_small_size' => '', 'typo_small_weight' => '', 'typo_small_lh' => '',

		// Custom code.
		'custom_css'       => '',
		'custom_js_head'   => '',
		'custom_js_footer' => '',

		// Social links.
		'social_instagram' => '',
		'social_flickr'    => '',
		'social_500px'     => '',
		'social_twitter'   => '',
		'social_linkedin'  => '',
		'social_github'    => '',
	);
}

/**
 * Read current options, merged over defaults.
 */
function ren_get_options() {
	$saved = get_option( REN_OPTIONS_KEY, array() );
	return wp_parse_args( $saved, ren_default_options() );
}

/**
 * Register the admin page under Appearance.
 */
function ren_add_options_page() {
	add_theme_page(
		__( 'Ren Studio Theme Options', 'ren' ),
		__( 'Theme Options', 'ren' ),
		'edit_theme_options',
		'ren-theme-options',
		'ren_render_options_page'
	);
}
add_action( 'admin_menu', 'ren_add_options_page' );

/**
 * Register settings, sections, and fields via the Settings API.
 */
function ren_register_settings() {
	register_setting(
		'ren_theme_options_group',
		REN_OPTIONS_KEY,
		array(
			'sanitize_callback' => 'ren_sanitize_options',
			'default'           => ren_default_options(),
		)
	);

	// ── Branding ──────────────────────────────────────────────
	add_settings_section(
		'ren_branding_section',
		'',
		function () {
			echo '<p>' . esc_html__( 'These controls write CSS variables that override theme.json — no code required. For deeper control, use Appearance → Editor → Styles.', 'ren' ) . '</p>';
		},
		'ren-tab-branding'
	);
	add_settings_field( 'ren_logo', __( 'Logo', 'ren' ), 'ren_field_logo', 'ren-tab-branding', 'ren_branding_section' );
	add_settings_field( 'ren_logo_width', __( 'Logo Width', 'ren' ), 'ren_field_logo_width', 'ren-tab-branding', 'ren_branding_section' );
	add_settings_field( 'ren_color_scheme', __( 'Color Scheme', 'ren' ), 'ren_field_color_scheme', 'ren-tab-branding', 'ren_branding_section' );
	add_settings_field( 'ren_accent', __( 'Accent Color', 'ren' ), 'ren_field_accent', 'ren-tab-branding', 'ren_branding_section' );
	add_settings_field( 'ren_accent_hover', __( 'Accent Hover', 'ren' ), 'ren_field_accent_hover', 'ren-tab-branding', 'ren_branding_section' );

	// ── Typography ────────────────────────────────────────────
	add_settings_section(
		'ren_typography_section',
		'',
		function () {
			echo '<p>' . esc_html__( 'Choose a Google Font from the curated list, or upload your own local font files.', 'ren' ) . '</p>';
		},
		'ren-tab-typography'
	);
	add_settings_field( 'ren_font_heading', __( 'Heading Font', 'ren' ), 'ren_field_font_role', 'ren-tab-typography', 'ren_typography_section', array( 'role' => 'heading' ) );
	add_settings_field( 'ren_font_body', __( 'Body Font', 'ren' ), 'ren_field_font_role', 'ren-tab-typography', 'ren_typography_section', array( 'role' => 'body' ) );
	add_settings_field( 'ren_font_sizes', __( 'Font Sizes', 'ren' ), 'ren_field_font_sizes', 'ren-tab-typography', 'ren_typography_section' );
	add_settings_field( 'ren_typo_advanced', __( 'Heading Sizes &amp; Weights', 'ren' ), 'ren_field_typo_advanced', 'ren-tab-typography', 'ren_typography_section' );

	// ── Header ────────────────────────────────────────────────
	add_settings_section(
		'ren_header_section',
		'',
		function () {
			echo '<p>' . esc_html__( 'Logo spacing and main navigation font, independent of everything else.', 'ren' ) . '</p>';
		},
		'ren-tab-header'
	);
	add_settings_field( 'ren_logo_spacing', __( 'Logo Spacing', 'ren' ), 'ren_field_logo_spacing', 'ren-tab-header', 'ren_header_section' );
	add_settings_field( 'ren_header_height', __( 'Header Height', 'ren' ), 'ren_field_header_height', 'ren-tab-header', 'ren_header_section' );
	add_settings_field( 'ren_header_reveal_speed', __( 'Reveal Speed', 'ren' ), 'ren_field_header_reveal_speed', 'ren-tab-header', 'ren_header_section' );
	add_settings_field( 'ren_nav_font', __( 'Menu Font', 'ren' ), 'ren_field_nav_font', 'ren-tab-header', 'ren_header_section' );
	add_settings_field( 'ren_header_autohide', __( 'Scroll Behavior', 'ren' ), 'ren_field_header_autohide', 'ren-tab-header', 'ren_header_section' );

	// ── Blog ──────────────────────────────────────────────────
	add_settings_section(
		'ren_blog_section',
		'',
		function () {
			echo '<p>' . esc_html__( 'How the blog archive (archive.html) arranges posts.', 'ren' ) . '</p>';
		},
		'ren-tab-blog'
	);
	add_settings_field( 'ren_blog_layout', __( 'Archive Layout', 'ren' ), 'ren_field_blog_layout', 'ren-tab-blog', 'ren_blog_section' );
	add_settings_field( 'ren_post_title_align', __( 'Post Title Alignment', 'ren' ), 'ren_field_post_title_align', 'ren-tab-blog', 'ren_blog_section' );
	add_settings_field( 'ren_post_nav_placeholder', __( 'Previous/Next Placeholder Image', 'ren' ), 'ren_field_post_nav_placeholder', 'ren-tab-blog', 'ren_blog_section' );
	add_settings_field( 'ren_color_more_articles_bg', __( '"Altri Articoli" Background', 'ren' ), 'ren_field_color_more_articles_bg', 'ren-tab-blog', 'ren_blog_section' );

	// ── Colors ────────────────────────────────────────────────
	add_settings_section(
		'ren_colors_section',
		'',
		function () {
			echo '<p>' . esc_html__( 'Fine-tune individual element colors. Leave a field blank to keep following the Branding → Color Scheme automatically.', 'ren' ) . '</p>';
		},
		'ren-tab-colors'
	);
	add_settings_field( 'ren_color_heading', __( 'Headings', 'ren' ), 'ren_field_color_heading', 'ren-tab-colors', 'ren_colors_section' );
	add_settings_field( 'ren_color_body', __( 'Body Text', 'ren' ), 'ren_field_color_body', 'ren-tab-colors', 'ren_colors_section' );
	add_settings_field( 'ren_color_link', __( 'Links', 'ren' ), 'ren_field_color_link', 'ren-tab-colors', 'ren_colors_section' );
	add_settings_field( 'ren_color_link_hover', __( 'Links (hover)', 'ren' ), 'ren_field_color_link_hover', 'ren-tab-colors', 'ren_colors_section' );
	add_settings_field( 'ren_color_post_hero', __( 'Post Hero Background', 'ren' ), 'ren_field_color_post_hero', 'ren-tab-colors', 'ren_colors_section' );
	add_settings_field( 'ren_color_table_header_bg', __( 'Table Header Background', 'ren' ), 'ren_field_color_table_header_bg', 'ren-tab-colors', 'ren_colors_section' );
	add_settings_field( 'ren_color_table_header_text', __( 'Table Header Text', 'ren' ), 'ren_field_color_table_header_text', 'ren-tab-colors', 'ren_colors_section' );
	add_settings_field( 'ren_color_table_row_1', __( 'Table Row Color 1', 'ren' ), 'ren_field_color_table_row_1', 'ren-tab-colors', 'ren_colors_section' );
	add_settings_field( 'ren_color_table_row_2', __( 'Table Row Color 2', 'ren' ), 'ren_field_color_table_row_2', 'ren-tab-colors', 'ren_colors_section' );

	// ── Custom Code ───────────────────────────────────────────
	add_settings_section(
		'ren_code_section',
		'',
		function () {
			echo '<p>' . esc_html__( 'Optional CSS/JS snippets, output on the front end.', 'ren' ) . '</p>';
		},
		'ren-tab-code'
	);
	add_settings_field( 'ren_font_awesome', __( 'Font Awesome Icons', 'ren' ), 'ren_field_font_awesome', 'ren-tab-code', 'ren_code_section' );
	add_settings_field( 'ren_codemirror', __( 'Code Editor (CodeMirror)', 'ren' ), 'ren_field_codemirror', 'ren-tab-code', 'ren_code_section' );
	add_settings_field( 'ren_custom_css', __( 'Custom CSS', 'ren' ), 'ren_field_custom_css', 'ren-tab-code', 'ren_code_section' );
	add_settings_field( 'ren_custom_js_head', __( 'Custom JS (head)', 'ren' ), 'ren_field_custom_js_head', 'ren-tab-code', 'ren_code_section' );
	add_settings_field( 'ren_custom_js_footer', __( 'Custom JS (footer)', 'ren' ), 'ren_field_custom_js_footer', 'ren-tab-code', 'ren_code_section' );

	// ── Social Links ──────────────────────────────────────────
	add_settings_section(
		'ren_social_section',
		'',
		function () {
			echo '<p>' . esc_html__( 'Leave blank to hide. Used by the footer icons and the [ren_social_links] shortcode.', 'ren' ) . '</p>';
		},
		'ren-tab-social'
	);
	add_settings_field( 'ren_social', __( 'Profiles', 'ren' ), 'ren_field_social_links', 'ren-tab-social', 'ren_social_section' );
}
add_action( 'admin_init', 'ren_register_settings' );

/**
 * Sanitize submitted options.
 *
 * @param array $input Raw input.
 * @return array Clean values.
 */
function ren_sanitize_options( $input ) {
	$defaults = ren_default_options();
	$clean    = array();

	// Branding.
	$clean['accent_preset'] = isset( $input['accent_preset'] ) ? sanitize_text_field( $input['accent_preset'] ) : $defaults['accent_preset'];
	$clean['accent_custom'] = isset( $input['accent_custom'] ) ? ( sanitize_hex_color( $input['accent_custom'] ) ?: $defaults['accent_custom'] ) : $defaults['accent_custom'];
	$clean['logo_id']       = isset( $input['logo_id'] ) ? absint( $input['logo_id'] ) : 0;
	$clean['logo_width']    = isset( $input['logo_width'] ) && $input['logo_width'] ? (string) min( 600, max( 20, absint( $input['logo_width'] ) ) ) : '160';
	$clean['color_scheme']  = isset( $input['color_scheme'] ) && 'dark' === $input['color_scheme'] ? 'dark' : 'light';
	$clean['accent_hover']  = isset( $input['accent_hover'] ) && '' !== $input['accent_hover'] ? ( sanitize_hex_color( $input['accent_hover'] ) ?: '' ) : '';

	// Header — logo spacing (px) + nav font.
	$clean['logo_margin_top']    = isset( $input['logo_margin_top'] ) ? (string) min( 100, max( 0, absint( $input['logo_margin_top'] ) ) ) : '16';
	$clean['logo_margin_bottom'] = isset( $input['logo_margin_bottom'] ) ? (string) min( 100, max( 0, absint( $input['logo_margin_bottom'] ) ) ) : '16';
	$clean['header_height']      = isset( $input['header_height'] ) ? (string) min( 400, max( 40, absint( $input['header_height'] ) ) ) : '95';
	$clean['header_reveal_speed'] = isset( $input['header_reveal_speed'] ) ? (string) min( 1000, max( 0, absint( $input['header_reveal_speed'] ) ) ) : '250';
	$clean['header_nav_size']    = isset( $input['header_nav_size'] ) ? (string) min( 24, max( 8, absint( $input['header_nav_size'] ) ) ) : '11';
	$clean['header_nav_weight']  = isset( $input['header_nav_weight'] )
		&& in_array( (string) $input['header_nav_weight'], array( '300', '400', '500', '600', '700', '800' ), true )
		? (string) $input['header_nav_weight']
		: '600';
	$clean['header_nav_spacing'] = isset( $input['header_nav_spacing'] ) ? (string) min( 50, max( 0, absint( $input['header_nav_spacing'] ) ) ) : '12';
	$clean['header_autohide']    = ! empty( $input['header_autohide'] ) ? '1' : '0';

	// Blog — archive layout.
	$clean['blog_layout'] = isset( $input['blog_layout'] ) && 'list' === $input['blog_layout'] ? 'list' : 'grid';
	$clean['post_title_align'] = isset( $input['post_title_align'] ) && in_array( $input['post_title_align'], array( 'left', 'center', 'right' ), true ) ? $input['post_title_align'] : 'left';
	$clean['post_nav_placeholder_id'] = isset( $input['post_nav_placeholder_id'] ) ? absint( $input['post_nav_placeholder_id'] ) : 0;

	// Advanced.
	$clean['enable_font_awesome'] = ! empty( $input['enable_font_awesome'] ) ? '1' : '0';
	$clean['enable_codemirror']   = ! empty( $input['enable_codemirror'] ) ? '1' : '0';

	// Element colors — blank stays blank (means "automatic").
	foreach ( array( 'color_heading', 'color_body', 'color_link', 'color_link_hover', 'color_post_hero', 'color_more_articles_bg', 'color_table_header_bg', 'color_table_header_text', 'color_table_row_1', 'color_table_row_2' ) as $key ) {
		$clean[ $key ] = isset( $input[ $key ] ) && '' !== $input[ $key ] ? ( sanitize_hex_color( $input[ $key ] ) ?: '' ) : '';
	}

	// Typography.
	foreach ( array( 'heading', 'body' ) as $role ) {
		$mode_key           = "font_{$role}_mode";
		$mode               = isset( $input[ $mode_key ] ) && 'local' === $input[ $mode_key ] ? 'local' : 'google';
		$clean[ $mode_key ] = $mode;

		$google_key            = "font_{$role}_family_google";
		$clean[ $google_key ]  = isset( $input[ $google_key ] ) ? sanitize_text_field( $input[ $google_key ] ) : $defaults[ $google_key ];

		$local_key            = "font_{$role}_family_local";
		$clean[ $local_key ]  = isset( $input[ $local_key ] ) ? sanitize_text_field( $input[ $local_key ] ) : '';

		foreach ( array( 'woff2', 'woff', 'ttf', 'otf' ) as $format ) {
			$file_key           = "font_{$role}_regular_{$format}";
			$clean[ $file_key ] = isset( $input[ $file_key ] ) ? esc_url_raw( $input[ $file_key ] ) : '';
		}
	}

	$clean['font_heading_local_weight'] = isset( $input['font_heading_local_weight'] )
		&& in_array( (string) $input['font_heading_local_weight'], array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ), true )
		? (string) $input['font_heading_local_weight']
		: '700';

	foreach ( array( 'woff2', 'woff', 'ttf', 'otf' ) as $format ) {
		$file_key           = "font_body_bold_{$format}";
		$clean[ $file_key ] = isset( $input[ $file_key ] ) ? esc_url_raw( $input[ $file_key ] ) : '';
	}

	// Font sizes (px) — blank stays blank (means "automatic / fluid default").
	foreach ( array( 'font_size_small', 'font_size_medium', 'font_size_large', 'font_size_xlarge', 'font_size_xxlarge', 'font_size_hero' ) as $key ) {
		if ( empty( $input[ $key ] ) ) {
			$clean[ $key ] = '';
		} else {
			$clean[ $key ] = (string) min( 300, max( 8, absint( $input[ $key ] ) ) );
		}
	}

	// Advanced per-tag typography (H1-H6, Body, Lead, Small) — each blank stays blank.
	$valid_weights = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );
	foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body', 'lead', 'small' ) as $tag ) {
		$size_key = "typo_{$tag}_size";
		$clean[ $size_key ] = empty( $input[ $size_key ] ) ? '' : (string) min( 200, max( 8, absint( $input[ $size_key ] ) ) );

		$weight_key = "typo_{$tag}_weight";
		$clean[ $weight_key ] = ( isset( $input[ $weight_key ] ) && in_array( (string) $input[ $weight_key ], $valid_weights, true ) )
			? (string) $input[ $weight_key ]
			: '';

		$lh_key = "typo_{$tag}_lh";
		$clean[ $lh_key ] = empty( $input[ $lh_key ] ) ? '' : (string) min( 3, max( 0.8, (float) $input[ $lh_key ] ) );
	}

	// Custom code.
	$clean['custom_css']       = isset( $input['custom_css'] ) ? ren_sanitize_custom_css( $input['custom_css'] ) : '';
	$clean['custom_js_head']   = isset( $input['custom_js_head'] ) ? ren_sanitize_custom_js( $input['custom_js_head'] ) : '';
	$clean['custom_js_footer'] = isset( $input['custom_js_footer'] ) ? ren_sanitize_custom_js( $input['custom_js_footer'] ) : '';

	// Social links.
	foreach ( array( 'instagram', 'flickr', '500px', 'twitter', 'linkedin', 'github' ) as $slug ) {
		$key            = 'social_' . $slug;
		$clean[ $key ]  = isset( $input[ $key ] ) ? esc_url_raw( $input[ $key ] ) : '';
	}

	return $clean;
}

/**
 * Strip a trailing </style> and any tags — custom CSS is plain text, never markup.
 */
function ren_sanitize_custom_css( $css ) {
	return trim( str_ireplace( '</style>', '', wp_strip_all_tags( $css ) ) );
}

/**
 * Strip a trailing </script> — custom JS keeps its own tags/quotes intact.
 */
function ren_sanitize_custom_js( $js ) {
	return trim( str_ireplace( '</script>', '', $js ) );
}

/** Field: heading color (blank = automatic). */
function ren_field_color_heading() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_heading]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>
		<p class="description">%4$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_heading'] ),
		esc_html__( 'Auto', 'ren' ),
		esc_html__( 'Click "Auto" to go back to following the Color Scheme.', 'ren' )
	);
}

/** Field: body text color (blank = automatic). */
function ren_field_color_body() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_body]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_body'] ),
		esc_html__( 'Auto', 'ren' )
	);
}

/** Field: link color (blank = automatic — follows the accent color). */
function ren_field_color_link() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_link]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_link'] ),
		esc_html__( 'Auto', 'ren' )
	);
}

/** Field: link hover color (blank = automatic). */
function ren_field_color_link_hover() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_link_hover]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_link_hover'] ),
		esc_html__( 'Auto', 'ren' )
	);
}

/** Field: table header background (blank = automatic, follows Accent). */
function ren_field_color_table_header_bg() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_table_header_bg]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>
		<p class="description">%4$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_table_header_bg'] ),
		esc_html__( 'Auto', 'ren' ),
		esc_html__( 'Used by the .ren-table class (regular tables and the WP Table block).', 'ren' )
	);
}

/** Field: table header text (blank = automatic, follows Background). */
function ren_field_color_table_header_text() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_table_header_text]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_table_header_text'] ),
		esc_html__( 'Auto', 'ren' )
	);
}

/** Field: table row color 1 (odd rows; blank = automatic, follows Background). */
function ren_field_color_table_row_1() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_table_row_1]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>
		<p class="description">%4$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_table_row_1'] ),
		esc_html__( 'Auto', 'ren' ),
		esc_html__( 'The two row colors alternate to create the zebra-stripe effect — no borders needed between rows.', 'ren' )
	);
}

/** Field: table row color 2 (even rows; blank = automatic, follows Surface). */
function ren_field_color_table_row_2() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_table_row_2]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_table_row_2'] ),
		esc_html__( 'Auto', 'ren' )
	);
}

/** Field: background color for the "Magazine Hero" post template's title band (blank = automatic, follows Accent). */
function ren_field_color_post_hero() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_post_hero]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>
		<p class="description">%4$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_post_hero'] ),
		esc_html__( 'Auto', 'ren' ),
		esc_html__( 'Used by the "Magazine Hero" post template (Impostazioni articolo → Modello). Defaults to the Accent Color.', 'ren' )
	);
}

/** Field: background color for the "Altri articoli" band at the end of a post (blank = light grey default). */
function ren_field_color_more_articles_bg() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[color_more_articles_bg]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>
		<p class="description">%4$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['color_more_articles_bg'] ),
		esc_html__( 'Auto', 'ren' ),
		esc_html__( 'Full-width background band behind "Altri articoli" at the end of a blog post. Defaults to a light grey (#f0f0f0).', 'ren' )
	);
}

/**
 * Field: font-size overrides (px) for the 6 named sizes used across the
 * theme's templates/patterns. Blank keeps theme.json's fluid clamp() default.
 */
function ren_field_font_sizes() {
	$options = ren_get_options();
	$sizes   = array(
		'font_size_small'   => __( 'Small', 'ren' ),
		'font_size_medium'  => __( 'Medium', 'ren' ),
		'font_size_large'   => __( 'Large', 'ren' ),
		'font_size_xlarge'  => __( 'XL', 'ren' ),
		'font_size_xxlarge' => __( 'XXL', 'ren' ),
		'font_size_hero'    => __( 'Hero', 'ren' ),
	);
	foreach ( $sizes as $key => $label ) {
		printf(
			'<p style="display:flex;align-items:center;gap:10px;max-width:260px;"><label style="flex:1 0 60px;">%1$s</label> <input type="number" min="8" max="300" name="%2$s[%3$s]" value="%4$s" placeholder="auto" style="width:90px;" /> <span>px</span></p>',
			esc_html( $label ),
			esc_attr( REN_OPTIONS_KEY ),
			esc_attr( $key ),
			esc_attr( $options[ $key ] )
		);
	}
	echo '<p class="description">' . esc_html__( 'Leave a size blank to keep the theme\'s responsive (fluid) default. Filling one in replaces it with this fixed pixel value on all screen sizes.', 'ren' ) . '</p>';
}

/**
 * Field: advanced per-tag typography — size (px), weight, and line-height
 * for H1-H6, Body, Lead (large intro paragraphs), and Small text. More
 * granular than "Font Sizes" above; leave any cell blank to keep automatic.
 */
function ren_field_typo_advanced() {
	$options = ren_get_options();
	$tags    = array(
		'h1'    => __( 'H1 — Main Title', 'ren' ),
		'h2'    => __( 'H2 — Section Title', 'ren' ),
		'h3'    => __( 'H3 — Subtitle', 'ren' ),
		'h4'    => __( 'H4', 'ren' ),
		'h5'    => __( 'H5', 'ren' ),
		'h6'    => __( 'H6', 'ren' ),
		'body'  => __( 'Body — Main Text', 'ren' ),
		'lead'  => __( 'Lead — Intro Paragraphs', 'ren' ),
		'small' => __( 'Small Text', 'ren' ),
	);
	$weights = array( '' => __( 'auto', 'ren' ), '300' => '300', '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800', '900' => '900' );
	?>
	<table class="widefat" style="max-width:640px;">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Tag', 'ren' ); ?></th>
				<th><?php esc_html_e( 'Size (px)', 'ren' ); ?></th>
				<th><?php esc_html_e( 'Weight', 'ren' ); ?></th>
				<th><?php esc_html_e( 'Line Height', 'ren' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $tags as $tag => $label ) : ?>
				<tr>
					<td><code><?php echo esc_html( $label ); ?></code></td>
					<td><input type="number" min="8" max="200" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[typo_<?php echo esc_attr( $tag ); ?>_size]" value="<?php echo esc_attr( $options[ "typo_{$tag}_size" ] ); ?>" placeholder="auto" style="width:80px;" /></td>
					<td>
						<select name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[typo_<?php echo esc_attr( $tag ); ?>_weight]">
							<?php foreach ( $weights as $val => $wlabel ) : ?>
								<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $options[ "typo_{$tag}_weight" ], $val ); ?>><?php echo esc_html( $wlabel ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td><input type="number" min="0.8" max="3" step="0.05" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[typo_<?php echo esc_attr( $tag ); ?>_lh]" value="<?php echo esc_attr( $options[ "typo_{$tag}_lh" ] ); ?>" placeholder="auto" style="width:80px;" /></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p class="description"><?php esc_html_e( '"Lead" applies to large intro paragraphs, "Small" to small-print text — both throughout the theme, not just on posts.', 'ren' ); ?></p>
	<?php
}

/** Field: logo top/bottom margin (px) — controls the visual height of the header. */
function ren_field_logo_spacing() {
	$options = ren_get_options();
	?>
	<p>
		<label style="display:inline-block;width:140px;"><?php esc_html_e( 'Top margin', 'ren' ); ?></label>
		<input type="number" min="0" max="100" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[logo_margin_top]" value="<?php echo esc_attr( $options['logo_margin_top'] ); ?>" style="width:80px;" /> px
	</p>
	<p>
		<label style="display:inline-block;width:140px;"><?php esc_html_e( 'Bottom margin', 'ren' ); ?></label>
		<input type="number" min="0" max="100" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[logo_margin_bottom]" value="<?php echo esc_attr( $options['logo_margin_bottom'] ); ?>" style="width:80px;" /> px
	</p>
	<?php
}

/**
 * Field: header height (px) — used directly as the reserved space below the
 * fixed header (body padding-top), instead of relying on JS to measure the
 * real rendered height at runtime. Use your browser's DevTools (inspect the
 * header, check the box-model height) to find the exact value, then adjust
 * here until the page's first section sits flush against the header with
 * no gap.
 */
function ren_field_header_height() {
	$options = ren_get_options();
	printf(
		'<input type="number" min="40" max="400" name="%1$s[header_height]" value="%2$s" style="width:80px;" /> px
		<p class="description">%3$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['header_height'] ),
		esc_html__( 'Reserved space below the fixed header. Inspect the header in your browser (box model height) and set this to match exactly if you see a gap or overlap.', 'ren' )
	);
}

/** Field: how fast the header slides fully into view when you scroll up (ms). 0 = instant, no animation. */
function ren_field_header_reveal_speed() {
	$options = ren_get_options();
	printf(
		'<input type="number" min="0" max="1000" step="50" name="%1$s[header_reveal_speed]" value="%2$s" style="width:80px;" /> ms
		<p class="description">%3$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['header_reveal_speed'] ),
		esc_html__( 'How long the header takes to slide fully into view when you scroll up. Set to 0 for an instant snap with no animation. Only affects reveal — hiding while scrolling down always follows your scroll 1:1.', 'ren' )
	);
}

/** Field: main navigation font size/weight/letter-spacing, with a live preview. */
function ren_field_nav_font() {
	$options  = ren_get_options();
	$weights  = array( '300' => 'Light (300)', '400' => 'Regular (400)', '500' => 'Medium (500)', '600' => 'SemiBold (600)', '700' => 'Bold (700)', '800' => 'ExtraBold (800)' );
	$size     = absint( $options['header_nav_size'] );
	$weight   = $options['header_nav_weight'];
	$spacing  = absint( $options['header_nav_spacing'] );
	?>
	<p>
		<label style="display:inline-block;width:140px;"><?php esc_html_e( 'Font size', 'ren' ); ?></label>
		<input type="number" min="8" max="24" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[header_nav_size]" value="<?php echo esc_attr( $size ); ?>" style="width:80px;" /> px
	</p>
	<p>
		<label style="display:inline-block;width:140px;"><?php esc_html_e( 'Font weight', 'ren' ); ?></label>
		<select name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[header_nav_weight]">
			<?php foreach ( $weights as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $weight, $val ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label style="display:inline-block;width:140px;"><?php esc_html_e( 'Letter spacing', 'ren' ); ?></label>
		0.<input type="number" min="0" max="50" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[header_nav_spacing]" value="<?php echo esc_attr( $spacing ); ?>" style="width:70px;" /> em
	</p>
	<div style="padding:0.75rem 1.25rem;background:#f9f9f9;border:1px solid #e0e0e0;border-radius:6px;display:flex;gap:2rem;align-items:center;margin-top:10px;">
		<?php foreach ( array( 'Home', 'Portfolio', 'Blog', 'Contact' ) as $item ) : ?>
			<span style="font-size:<?php echo esc_attr( $size ); ?>px;font-weight:<?php echo esc_attr( $weight ); ?>;letter-spacing:0.<?php echo esc_attr( str_pad( (string) $spacing, 2, '0', STR_PAD_LEFT ) ); ?>em;text-transform:uppercase;color:#1d2327;">
				<?php echo esc_html( $item ); ?>
			</span>
		<?php endforeach; ?>
	</div>
	<?php
}

/** Field: sticky header that hides on scroll down, reappears on scroll up. */
function ren_field_header_autohide() {
	$options = ren_get_options();
	?>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[header_autohide]" value="1" <?php checked( $options['header_autohide'], '1' ); ?> />
		<?php esc_html_e( 'Sticky header: hide when scrolling down, reappear when scrolling up.', 'ren' ); ?>
	</label>
	<p class="description"><?php esc_html_e( 'The header is always sticky at the top of the page. When this is off, it just stays put; when on, it also hides while scrolling down and reappears when you scroll up.', 'ren' ); ?></p>
	<?php
}

/** Field: blog archive layout — Grid or List. */
function ren_field_blog_layout() {
	$options = ren_get_options();
	$layouts = array(
		'grid' => __( 'Grid — 3-column uniform grid', 'ren' ),
		'list' => __( 'List — featured image beside the text', 'ren' ),
	);
	foreach ( $layouts as $value => $label ) :
		?>
		<label style="display:block;margin-bottom:8px;">
			<input type="radio" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[blog_layout]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $options['blog_layout'], $value ); ?> />
			<?php echo esc_html( $label ); ?>
		</label>
	<?php endforeach; ?>
	<?php
}

/** Field: single post title/meta alignment — Left, Center, or Right. */
function ren_field_post_title_align() {
	$options    = ren_get_options();
	$alignments = array(
		'left'   => __( 'Left', 'ren' ),
		'center' => __( 'Center', 'ren' ),
		'right'  => __( 'Right', 'ren' ),
	);
	foreach ( $alignments as $value => $label ) :
		?>
		<label style="display:inline-block;margin-right:20px;">
			<input type="radio" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[post_title_align]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $options['post_title_align'], $value ); ?> />
			<?php echo esc_html( $label ); ?>
		</label>
	<?php endforeach; ?>
	<p class="description"><?php esc_html_e( 'Aligns the title and the date/author/reading-time row on single blog posts.', 'ren' ); ?></p>
	<?php
}

/** Field: previous/next navigation placeholder image (Media Library uploader). */
function ren_field_post_nav_placeholder() {
	$options       = ren_get_options();
	$image_id      = $options['post_nav_placeholder_id'];
	$image_url     = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : '';
	?>
	<div id="ren-post-nav-placeholder-preview" style="margin-bottom:10px;">
		<?php if ( $image_url ) : ?>
			<img src="<?php echo esc_url( $image_url ); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:4px;display:block;" />
		<?php endif; ?>
	</div>
	<input type="hidden" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[post_nav_placeholder_id]" id="ren-post-nav-placeholder-id" value="<?php echo esc_attr( $image_id ); ?>" />
	<button type="button" class="button" id="ren-post-nav-placeholder-upload"><?php esc_html_e( 'Choose Image', 'ren' ); ?></button>
	<button type="button" class="button" id="ren-post-nav-placeholder-remove"><?php esc_html_e( 'Remove', 'ren' ); ?></button>
	<p class="description"><?php esc_html_e( 'Shown in the Previous/Next card at the end of a post whenever that adjacent post has no featured image set. Leave empty to just hide the thumbnail in that case, as before.', 'ren' ); ?></p>
	<?php
}

/** Field: logo (Media Library uploader). */
function ren_field_logo() {
	$options  = ren_get_options();
	$logo_id  = $options['logo_id'];
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
	?>
	<div id="ren-logo-preview" style="margin-bottom:10px;">
		<?php if ( $logo_url ) : ?>
			<img src="<?php echo esc_url( $logo_url ); ?>" style="max-width:200px;height:auto;display:block;" />
		<?php endif; ?>
	</div>
	<input type="hidden" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[logo_id]" id="ren-logo-id" value="<?php echo esc_attr( $logo_id ); ?>" />
	<button type="button" class="button" id="ren-logo-upload"><?php esc_html_e( 'Choose Logo', 'ren' ); ?></button>
	<button type="button" class="button" id="ren-logo-remove"><?php esc_html_e( 'Remove', 'ren' ); ?></button>
	<p class="description"><?php esc_html_e( 'This also sets the Site Logo used by the Site Logo block in the header.', 'ren' ); ?></p>
	<?php
}

/** Field: color scheme (dark/light). */
function ren_field_color_scheme() {
	$options = ren_get_options();
	$scheme  = $options['color_scheme'];
	?>
	<label style="display:inline-flex;align-items:center;gap:6px;margin-right:20px;">
		<input type="radio" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[color_scheme]" value="dark" <?php checked( $scheme, 'dark' ); ?> />
		<?php esc_html_e( 'Dark (black background)', 'ren' ); ?>
	</label>
	<label style="display:inline-flex;align-items:center;gap:6px;">
		<input type="radio" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[color_scheme]" value="light" <?php checked( $scheme, 'light' ); ?> />
		<?php esc_html_e( 'Light (white background)', 'ren' ); ?>
	</label>
	<p class="description"><?php esc_html_e( 'Heads up: Acid Green as an accent has weak contrast for small text on a light background — Electric Violet or Hot Coral read better in Light mode.', 'ren' ); ?></p>
	<?php
}

/** Field: logo width in px (overrides the Site Logo block's own width). */
function ren_field_logo_width() {
	$options = ren_get_options();
	printf(
		'<input type="number" min="20" max="600" name="%1$s[logo_width]" value="%2$s" style="width:90px;" /> px
		<p class="description">%3$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['logo_width'] ),
		esc_html__( 'Overrides whatever width is set on the Site Logo block in the header.', 'ren' )
	);
}

/** Field: accent color (preset radio + custom color picker). */
function ren_field_accent() {
	$options = ren_get_options();
	foreach ( ren_accent_choices() as $value => $label ) :
		?>
		<label style="display:inline-flex;align-items:center;gap:6px;margin-right:16px;">
			<input type="radio" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[accent_preset]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $options['accent_preset'], $value ); ?> />
			<?php if ( 'custom' !== $value ) : ?>
				<span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:<?php echo esc_attr( $value ); ?>;border:1px solid #444;"></span>
			<?php endif; ?>
			<?php echo esc_html( $label ); ?>
		</label>
	<?php endforeach; ?>
	<div style="margin-top:10px;">
		<input type="text" class="ren-color-picker" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[accent_custom]" value="<?php echo esc_attr( $options['accent_custom'] ); ?>" />
		<p class="description"><?php esc_html_e( 'Used only when "Custom…" is selected above.', 'ren' ); ?></p>
	</div>
	<?php
}

/** Field: accent hover color — used by buttons/links on hover instead of switching to the foreground color (blank = automatic, keeps current behavior). */
function ren_field_accent_hover() {
	$options = ren_get_options();
	printf(
		'<span class="ren-clearable-color"><input type="text" class="ren-color-picker" data-allow-empty="true" name="%1$s[accent_hover]" value="%2$s" /> <button type="button" class="button ren-color-clear">%3$s</button></span>
		<p class="description">%4$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_attr( $options['accent_hover'] ),
		esc_html__( 'Auto', 'ren' ),
		esc_html__( 'When set, buttons use this color on hover instead of the current text color. Leave blank to keep the default behavior.', 'ren' )
	);
}

/**
 * Field: one font role (heading or body) — mode toggle + Google select +
 * local upload. Both blocks are always in the DOM; admin-options.js shows
 * only the one matching the selected mode.
 *
 * @param array $args { 'role' => 'heading'|'body' }
 */
function ren_field_font_role( $args ) {
	$role    = $args['role'];
	$options = ren_get_options();
	$mode    = $options[ "font_{$role}_mode" ];
	?>
	<div class="ren-font-field" data-role="<?php echo esc_attr( $role ); ?>">
		<label style="margin-right:16px;">
			<input type="radio" class="ren-font-mode" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[font_<?php echo esc_attr( $role ); ?>_mode]" value="google" <?php checked( $mode, 'google' ); ?> />
			<?php esc_html_e( 'Google Font', 'ren' ); ?>
		</label>
		<label>
			<input type="radio" class="ren-font-mode" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[font_<?php echo esc_attr( $role ); ?>_mode]" value="local" <?php checked( $mode, 'local' ); ?> />
			<?php esc_html_e( 'Upload Local Font', 'ren' ); ?>
		</label>

		<div class="ren-font-mode-panel" data-mode="google" style="margin-top:10px;<?php echo 'google' !== $mode ? 'display:none;' : ''; ?>">
			<select name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[font_<?php echo esc_attr( $role ); ?>_family_google]">
				<?php foreach ( ren_get_google_fonts_list() as $group_label => $fonts ) : ?>
					<optgroup label="<?php echo esc_attr( $group_label ); ?>">
						<?php foreach ( $fonts as $font ) : ?>
							<option value="<?php echo esc_attr( $font ); ?>" <?php selected( $options[ "font_{$role}_family_google" ], $font ); ?>><?php echo esc_html( $font ); ?></option>
						<?php endforeach; ?>
					</optgroup>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="ren-font-mode-panel" data-mode="local" style="margin-top:10px;<?php echo 'local' !== $mode ? 'display:none;' : ''; ?>">
			<p>
				<label><?php esc_html_e( 'Font family name', 'ren' ); ?></label><br />
				<input type="text" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[font_<?php echo esc_attr( $role ); ?>_family_local]" value="<?php echo esc_attr( $options[ "font_{$role}_family_local" ] ); ?>" placeholder="<?php esc_attr_e( 'e.g. My Custom Grotesk', 'ren' ); ?>" />
			</p>

			<?php if ( 'heading' === $role ) : ?>
				<p>
					<label><?php esc_html_e( 'File weight (what the uploaded file actually is)', 'ren' ); ?></label><br />
					<select name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[font_heading_local_weight]">
						<?php foreach ( array( '400', '500', '600', '700', '800', '900' ) as $w ) : ?>
							<option value="<?php echo esc_attr( $w ); ?>" <?php selected( $options['font_heading_local_weight'], $w ); ?>><?php echo esc_html( $w ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<?php ren_field_font_file_upload( $role, 'regular', __( 'Font file', 'ren' ), $options ); ?>
			<?php else : ?>
				<?php ren_field_font_file_upload( $role, 'regular', __( 'Regular weight file', 'ren' ), $options ); ?>
				<?php ren_field_font_file_upload( $role, 'bold', __( 'Bold weight file', 'ren' ), $options ); ?>
			<?php endif; ?>
			<p class="description"><?php esc_html_e( 'Upload WOFF2 for best performance; WOFF/TTF/OTF are optional extra fallbacks for older browsers. Upload to the Media Library first if a file isn\'t showing.', 'ren' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Render one "choose file from Media Library" control for a font slot.
 *
 * @param string $role  heading|body.
 * @param string $slot  regular|bold.
 * @param string $label Field label.
 * @param string $value Current file URL.
 */
function ren_field_font_file_upload( $role, $slot, $label, $options ) {
	$formats = array(
		'woff2' => 'WOFF2',
		'woff'  => 'WOFF',
		'ttf'   => 'TTF',
		'otf'   => 'OTF',
	);
	?>
	<div class="ren-font-weight-group" data-role="<?php echo esc_attr( $role ); ?>" data-slot="<?php echo esc_attr( $slot ); ?>">
		<p style="font-weight:600;margin-bottom:6px;"><?php echo esc_html( $label ); ?></p>
		<div style="display:flex;gap:12px;flex-wrap:wrap;">
			<?php foreach ( $formats as $format => $format_label ) :
				$field_name = "font_{$role}_{$slot}_{$format}";
				$value      = $options[ $field_name ];
				$input_id   = "ren-font-{$role}-{$slot}-{$format}";
				$filename   = $value ? basename( wp_parse_url( $value, PHP_URL_PATH ) ) : '';
				?>
				<p class="ren-font-file-row" data-role="<?php echo esc_attr( $role ); ?>" data-slot="<?php echo esc_attr( $slot ); ?>" data-format="<?php echo esc_attr( $format ); ?>" style="min-width:150px;">
					<button type="button" class="button button-small ren-font-file-upload"><?php echo esc_html( $format_label ); ?></button>
					<button type="button" class="button-link ren-font-file-remove" style="margin-left:4px;color:#b32d2e;">×</button><br />
					<span class="ren-font-filename" style="font-size:0.75rem;color:<?php echo $filename ? '#2E7D32' : '#aaa'; ?>;word-break:break-all;"><?php echo $filename ? esc_html( $filename ) : esc_html__( 'No file', 'ren' ); ?></span>
					<input type="hidden" class="ren-font-file-input" id="<?php echo esc_attr( $input_id ); ?>" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[<?php echo esc_attr( $field_name ); ?>]" value="<?php echo esc_url( $value ); ?>" />
				</p>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/** Field: enable self-hosted Font Awesome (no external CDN request). */
function ren_field_font_awesome() {
	$options = ren_get_options();
	?>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[enable_font_awesome]" value="1" <?php checked( $options['enable_font_awesome'], '1' ); ?> />
		<?php esc_html_e( 'Load Font Awesome 7 brand icons (fa-brands fa-*), self-hosted from this theme — no external request.', 'ren' ); ?>
	</label>
	<p class="description"><?php esc_html_e( 'The font files ship inside the theme itself, not loaded from a CDN — avoids sending visitor IP addresses to a third-party server (relevant under EU GDPR). Leave off if you don\'t use icon classes anywhere.', 'ren' ); ?></p>
	<?php
}

/** Field: enable self-hosted CodeMirror (code editor with proper line numbers). */
function ren_field_codemirror() {
	$options = ren_get_options();
	?>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( REN_OPTIONS_KEY ); ?>[enable_codemirror]" value="1" <?php checked( $options['enable_codemirror'], '1' ); ?> />
		<?php esc_html_e( 'Load CodeMirror (self-hosted) for code-editor textareas — proper line numbers even when a line wraps onto multiple visual rows.', 'ren' ); ?>
	</label>
	<p class="description"><?php esc_html_e( 'Turns any <textarea class="ren-codemirror"> in a Custom HTML block into a real code editor. Off by default — only needed on pages that actually use one.', 'ren' ); ?></p>
	<?php
}

/** Field: custom CSS textarea. */
function ren_field_custom_css() {
	$options = ren_get_options();
	printf(
		'<textarea name="%1$s[custom_css]" rows="8" style="width:100%%;font-family:monospace;">%2$s</textarea>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_textarea( $options['custom_css'] )
	);
}

/** Field: custom JS (head) textarea. */
function ren_field_custom_js_head() {
	$options = ren_get_options();
	printf(
		'<textarea name="%1$s[custom_js_head]" rows="6" style="width:100%%;font-family:monospace;">%2$s</textarea>
		<p class="description">%3$s</p>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_textarea( $options['custom_js_head'] ),
		esc_html__( 'Wrapped in <script> and printed in <head>. No sanitization beyond stripping a stray closing tag — only paste code you trust.', 'ren' )
	);
}

/** Field: custom JS (footer) textarea. */
function ren_field_custom_js_footer() {
	$options = ren_get_options();
	printf(
		'<textarea name="%1$s[custom_js_footer]" rows="6" style="width:100%%;font-family:monospace;">%2$s</textarea>',
		esc_attr( REN_OPTIONS_KEY ),
		esc_textarea( $options['custom_js_footer'] )
	);
}

/** Field: social profile URLs. */
function ren_field_social_links() {
	$options = ren_get_options();
	$labels  = array(
		'instagram' => 'Instagram',
		'flickr'    => 'Flickr',
		'500px'     => '500px',
		'twitter'   => 'X / Twitter',
		'linkedin'  => 'LinkedIn',
		'github'    => 'GitHub',
	);
	foreach ( $labels as $slug => $label ) {
		printf(
			'<p><label style="display:inline-block;width:90px;">%1$s</label> <input type="url" name="%2$s[social_%3$s]" value="%4$s" placeholder="https://…" style="width:320px;" /></p>',
			esc_html( $label ),
			esc_attr( REN_OPTIONS_KEY ),
			esc_attr( $slug ),
			esc_url( $options[ 'social_' . $slug ] )
		);
	}
}

/**
 * Tabs shown on the options page: [ slug => [ label, icon (dashicon class) ] ].
 */
function ren_options_tabs() {
	return array(
		'branding'   => array( 'label' => __( 'Branding', 'ren' ), 'icon' => 'dashicons-admin-customizer' ),
		'colors'     => array( 'label' => __( 'Colors', 'ren' ), 'icon' => 'dashicons-art' ),
		'typography' => array( 'label' => __( 'Typography', 'ren' ), 'icon' => 'dashicons-editor-textcolor' ),
		'header'     => array( 'label' => __( 'Header', 'ren' ), 'icon' => 'dashicons-arrow-up-alt' ),
		'blog'       => array( 'label' => __( 'Blog', 'ren' ), 'icon' => 'dashicons-admin-post' ),
		'code'       => array( 'label' => __( 'CSS / JS', 'ren' ), 'icon' => 'dashicons-editor-code' ),
		'social'     => array( 'label' => __( 'Social', 'ren' ), 'icon' => 'dashicons-share' ),
	);
}

/**
 * Handle the "Reset to defaults" link — deletes the stored option so
 * everything falls back to ren_default_options(), then redirects back to
 * a clean options page URL.
 */
function ren_maybe_reset_options() {
	if ( ! isset( $_GET['page'] ) || 'ren-theme-options' !== $_GET['page'] || ! isset( $_GET['ren_reset'] ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	check_admin_referer( 'ren_reset_options' );

	delete_option( REN_OPTIONS_KEY );

	wp_safe_redirect( admin_url( 'themes.php?page=ren-theme-options&reset=1' ) );
	exit;
}
add_action( 'admin_init', 'ren_maybe_reset_options' );

/**
 * Render the options page markup: a sidebar of tabs (like the Ren theme's
 * panel) + a single form covering every tab's fields. Only one panel is
 * visible at a time via CSS/JS; all fields still submit together on Save,
 * so there's no risk of a hidden tab's values being dropped.
 */
function ren_render_options_page() {
	$tabs = ren_options_tabs();
	$theme = wp_get_theme();
	?>
	<div class="wrap ren-options-wrap">

		<h1 class="ren-options-title">
			<span class="ren-options-logo"><?php echo esc_html( substr( $theme->get( 'Name' ), 0, 3 ) ); ?></span>
			<?php esc_html_e( 'Theme Options', 'ren' ); ?>
			<span class="ren-options-version">v<?php echo esc_html( $theme->get( 'Version' ) ); ?></span>
		</h1>

		<?php if ( isset( $_GET['reset'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings reset to defaults.', 'ren' ); ?></p></div>
		<?php elseif ( isset( $_GET['settings-updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'ren' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="options.php" class="ren-options-shell">
			<?php settings_fields( 'ren_theme_options_group' ); ?>

			<nav class="ren-options-sidebar">
				<ul>
					<?php foreach ( $tabs as $slug => $tab ) : ?>
						<li>
							<a href="#ren-tab-<?php echo esc_attr( $slug ); ?>" class="ren-nav-item" data-tab="<?php echo esc_attr( $slug ); ?>">
								<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
								<?php echo esc_html( $tab['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'themes.php?page=ren-theme-options&ren_reset=1' ), 'ren_reset_options' ) ); ?>" class="ren-reset-link" id="ren-reset-options">
					<?php esc_html_e( 'Reset to defaults', 'ren' ); ?>
				</a>
			</nav>

			<div class="ren-options-content">
				<?php foreach ( $tabs as $slug => $tab ) : ?>
					<div class="ren-tab-panel" id="ren-tab-<?php echo esc_attr( $slug ); ?>">
						<h2><?php echo esc_html( $tab['label'] ); ?></h2>
						<?php do_settings_sections( 'ren-tab-' . $slug ); ?>

						<?php if ( 'branding' === $slug ) : ?>
							<div class="ren-theme-info-box">
								<p><strong><?php esc_html_e( 'Theme Info', 'ren' ); ?></strong></p>
								<p><?php esc_html_e( 'Version:', 'ren' ); ?> <?php echo esc_html( $theme->get( 'Version' ) ); ?></p>
								<p><?php esc_html_e( 'WordPress:', 'ren' ); ?> <?php echo esc_html( get_bloginfo( 'version' ) ); ?></p>
								<p>PHP: <?php echo esc_html( phpversion() ); ?></p>
								<p><?php esc_html_e( 'Template directory:', 'ren' ); ?> <?php echo esc_html( get_template_directory() ); ?></p>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>

				<?php submit_button( __( 'Save Changes', 'ren' ), 'primary ren-save-button' ); ?>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Load the media uploader + color picker only on our settings page.
 *
 * @param string $hook Current admin page hook.
 */
function ren_admin_assets( $hook ) {
	if ( 'appearance_page_ren-theme-options' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );

	wp_enqueue_style(
		'ren-admin-options',
		REN_URI . '/assets/css/admin-options.css',
		array(),
		REN_VERSION
	);

	wp_enqueue_script(
		'ren-admin-options',
		REN_URI . '/assets/js/admin-options.js',
		array( 'jquery', 'wp-color-picker' ),
		REN_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'ren_admin_assets' );

/**
 * Resolve the effective accent color (preset or custom).
 *
 * @return string Hex color.
 */
function ren_get_effective_accent() {
	$options = ren_get_options();
	if ( 'custom' === $options['accent_preset'] ) {
		return $options['accent_custom'] ? $options['accent_custom'] : '#b026ff';
	}
	return $options['accent_preset'];
}

/**
 * The 5 CSS variables that flip between the dark and light color schemes.
 *
 * @param string $scheme 'dark'|'light'.
 * @return array<string,string>
 */
function ren_get_scheme_colors( $scheme ) {
	if ( 'light' === $scheme ) {
		return array(
			'background' => '#ffffff',
			'surface'    => '#f5f5f5',
			'foreground' => '#0a0a0a',
			'muted'      => '#6b6b6b',
			'border'     => '#e2e2e2',
		);
	}

	return array(
		'background' => '#0a0a0a',
		'surface'    => '#161616',
		'foreground' => '#f5f5f2',
		'muted'      => '#8a8a8a',
		'border'     => '#2a2a2a',
	);
}

/**
 * Build the :root{...} CSS custom-property block (colors, fonts, sizes) as
 * a plain string — reused for both the front-end <style> tag and the block
 * editor iframe injection below.
 *
 * @return string
 */
function ren_get_dynamic_css() {
	$options = ren_get_options();
	$accent  = ren_get_effective_accent();
	$scheme  = ren_get_scheme_colors( $options['color_scheme'] );

	$heading_color    = $options['color_heading'] ? $options['color_heading'] : $scheme['foreground'];
	$body_color       = $options['color_body'] ? $options['color_body'] : $scheme['foreground'];
	$link_color       = $options['color_link'] ? $options['color_link'] : $accent;
	$link_hover_color = $options['color_link_hover'] ? $options['color_link_hover'] : $scheme['foreground'];
	$post_hero_color  = $options['color_post_hero'] ? $options['color_post_hero'] : $accent;
	$accent_hover      = $options['accent_hover'] ? $options['accent_hover'] : $scheme['foreground'];
	$table_header_bg   = $options['color_table_header_bg'] ? $options['color_table_header_bg'] : $accent;
	$table_header_text = $options['color_table_header_text'] ? $options['color_table_header_text'] : $scheme['background'];
	$table_row_1       = $options['color_table_row_1'] ? $options['color_table_row_1'] : $scheme['background'];
	$table_row_2       = $options['color_table_row_2'] ? $options['color_table_row_2'] : $scheme['surface'];

	$size_map = array(
		'font_size_small'   => 'small',
		'font_size_medium'  => 'medium',
		'font_size_large'   => 'large',
		'font_size_xlarge'  => 'x-large',
		'font_size_xxlarge' => 'xx-large',
		'font_size_hero'    => 'hero',
	);

	$lines   = array();
	$lines[] = '--wp--preset--color--accent: ' . $accent . ';';
	$lines[] = '--wp--preset--color--background: ' . $scheme['background'] . ';';
	$lines[] = '--wp--preset--color--surface: ' . $scheme['surface'] . ';';
	$lines[] = '--wp--preset--color--foreground: ' . $scheme['foreground'] . ';';
	$lines[] = '--wp--preset--color--muted: ' . $scheme['muted'] . ';';
	$lines[] = '--wp--preset--color--border: ' . $scheme['border'] . ';';
	$lines[] = '--wp--custom--color--heading: ' . $heading_color . ';';
	$lines[] = '--wp--custom--color--body: ' . $body_color . ';';
	$lines[] = '--wp--custom--color--link: ' . $link_color . ';';
	$lines[] = '--wp--custom--color--link-hover: ' . $link_hover_color . ';';
	$lines[] = '--wp--custom--color--post-hero: ' . $post_hero_color . ';';
	$lines[] = '--ren-more-articles-bg: ' . ( $options['color_more_articles_bg'] ? $options['color_more_articles_bg'] : '#f0f0f0' ) . ';';
	$lines[] = '--ren-header-height: ' . absint( $options['header_height'] ) . 'px;';
	$lines[] = '--ren-header-reveal-speed: ' . absint( $options['header_reveal_speed'] ) . 'ms;';

	$justify_map = array( 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' );
	$lines[]     = '--ren-post-title-align: ' . $options['post_title_align'] . ';';
	$lines[]     = '--ren-post-meta-justify: ' . $justify_map[ $options['post_title_align'] ] . ';';
	$lines[] = '--wp--custom--color--accent-hover: ' . $accent_hover . ';';
	$lines[] = '--wp--custom--color--table-header-bg: ' . $table_header_bg . ';';
	$lines[] = '--wp--custom--color--table-header-text: ' . $table_header_text . ';';
	$lines[] = '--wp--custom--color--table-row-1: ' . $table_row_1 . ';';
	$lines[] = '--wp--custom--color--table-row-2: ' . $table_row_2 . ';';
	$lines[] = '--wp--preset--font-family--display: ' . ren_font_css_stack( 'heading' ) . ';';
	$lines[] = '--wp--preset--font-family--body: ' . ren_font_css_stack( 'body' ) . ';';

	foreach ( $size_map as $option_key => $slug ) {
		if ( $options[ $option_key ] ) {
			$lines[] = '--wp--preset--font-size--' . $slug . ': ' . $options[ $option_key ] . 'px;';
		}
	}

	$logo_width = $options['logo_width'] ? absint( $options['logo_width'] ) : 160;

	$extra   = array();
	$extra[] = '.wp-block-site-logo img, .wp-block-site-logo a { width: ' . $logo_width . 'px !important; max-width: none !important; }';
	$extra[] = '.wp-block-site-logo img { height: auto !important; }';

	// Header: logo spacing.
	$extra[] = '.wp-block-site-logo { margin-top: ' . absint( $options['logo_margin_top'] ) . 'px !important; margin-bottom: ' . absint( $options['logo_margin_bottom'] ) . 'px !important; }';

	// Header: main navigation font.
	$nav_spacing = '0.' . str_pad( (string) absint( $options['header_nav_spacing'] ), 2, '0', STR_PAD_LEFT );
	$extra[]     = '.wp-block-navigation, .wp-block-navigation a.wp-block-navigation-item__content { font-size: ' . absint( $options['header_nav_size'] ) . 'px !important; font-weight: ' . absint( $options['header_nav_weight'] ) . ' !important; letter-spacing: ' . $nav_spacing . 'em !important; }';

	// Advanced per-tag typography (H1-H6 as real tags; Body/Lead/Small as the
	// utility classes those roles actually render with throughout the theme).
	$typo_selectors = array(
		'h1'    => 'h1',
		'h2'    => 'h2',
		'h3'    => 'h3',
		'h4'    => 'h4',
		'h5'    => 'h5',
		'h6'    => 'h6',
		'body'  => 'body, p, .has-medium-font-size, .ren-article-body',
		'lead'  => '.has-large-font-size',
		'small' => '.has-small-font-size',
	);
	foreach ( $typo_selectors as $tag => $selector ) {
		$rules = array();
		if ( $options[ "typo_{$tag}_size" ] ) {
			$rules[] = 'font-size: ' . absint( $options[ "typo_{$tag}_size" ] ) . 'px !important;';
		}
		if ( $options[ "typo_{$tag}_weight" ] ) {
			$rules[] = 'font-weight: ' . absint( $options[ "typo_{$tag}_weight" ] ) . ' !important;';
		}
		if ( $options[ "typo_{$tag}_lh" ] ) {
			$rules[] = 'line-height: ' . floatval( $options[ "typo_{$tag}_lh" ] ) . ' !important;';
		}
		if ( ! empty( $rules ) ) {
			$extra[] = $selector . ' { ' . implode( ' ', $rules ) . ' }';
		}
	}

	return ":root {\n" . implode( "\n", $lines ) . "\n}\n" . implode( "\n", $extra );
}

/**
 * Output the dynamic CSS on the front end.
 */
function ren_output_dynamic_styles() {
	echo '<style id="ren-dynamic-style">' . ren_get_dynamic_css() . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from sanitized option values in ren_get_dynamic_css().
}
add_action( 'wp_head', 'ren_output_dynamic_styles', 20 );

/**
 * Add a ren-scheme-dark / ren-scheme-light class to <body>, in case any
 * further CSS needs to key off the active scheme.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function ren_body_scheme_class( $classes ) {
	$options   = ren_get_options();
	$classes[] = 'light' === $options['color_scheme'] ? 'ren-scheme-light' : 'ren-scheme-dark';
	$classes[] = 'list' === $options['blog_layout'] ? 'ren-blog-layout-list' : 'ren-blog-layout-grid';
	if ( ! empty( $options['header_autohide'] ) ) {
		$classes[] = 'ren-header-autohide';
	}
	return $classes;
}
add_filter( 'body_class', 'ren_body_scheme_class' );

/**
 * Inject the same dynamic CSS (+ Google Fonts @import + local @font-face
 * rules) directly into the block editor / Site Editor's PREVIEW IFRAME.
 *
 * This is deliberately NOT done via admin_head: the editor canvas renders
 * inside its own <iframe>, which has a separate <head> from the wp-admin
 * page chrome — admin_head never reaches it. block_editor_settings_all is
 * the mechanism WordPress itself uses to load theme.json's global styles
 * into that iframe, so hooking the same filter is what actually works.
 *
 * @param array $editor_settings Existing block editor settings.
 * @return array
 */
function ren_inject_editor_styles( $editor_settings ) {
	$css = ren_get_dynamic_css();

	$google_fonts_url = ren_get_google_fonts_url();
	if ( $google_fonts_url ) {
		$css = "@import url('" . esc_url_raw( $google_fonts_url ) . "');\n" . $css;
	}

	$css .= "\n" . ren_get_font_face_css();

	if ( ! isset( $editor_settings['styles'] ) || ! is_array( $editor_settings['styles'] ) ) {
		$editor_settings['styles'] = array();
	}

	$editor_settings['styles'][] = array( 'css' => $css );

	return $editor_settings;
}
add_filter( 'block_editor_settings_all', 'ren_inject_editor_styles' );
