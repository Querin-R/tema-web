<?php
/**
 * Ren Studio — theme bootstrap.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REN_VERSION', '2.56.0' ); // Keep in sync with the "Version:" header in style.css on every release — this drives cache-busting for every enqueued script/style.
define( 'REN_DIR', get_template_directory() );
define( 'REN_URI', get_template_directory_uri() );

/**
 * Theme setup: supports, menus, image sizes.
 */
function ren_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	// Custom logo (native support — feeds the Site Logo block and our options panel fallback).
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Navigation', 'ren' ),
			'footer'  => __( 'Footer Navigation', 'ren' ),
		)
	);

	add_image_size( 'ren-project-card', 900, 1100, true );
	add_image_size( 'ren-project-hero', 2000, 1200, true );
	add_image_size( 'ren-blog-card', 800, 600, true );
	add_image_size( 'ren-prevnext', 150, 100, true );

	// Magazine Hero background: a full-bleed CSS background-image, served
	// at a fixed 2400x1200 crop on desktop and a much lighter 900x900 crop
	// on mobile (see ren_post_hero_inline_style() in inc/post-hero.php,
	// which swaps between the two via a @media query) — before this, the
	// hero always loaded the raw original upload at full resolution on
	// every device, phones included.
	add_image_size( 'ren-hero-bg', 2400, 1200, true );
	add_image_size( 'ren-hero-bg-mobile', 900, 900, true );
}
add_action( 'after_setup_theme', 'ren_setup' );

/**
 * Register a "Masonry" style variant for the native core/gallery block —
 * selectable from the block's own Styles panel, right next to "Default"
 * and "Rounded". No third-party plugin, no separate gallery block to learn:
 * it works on any Gallery block that already exists today, and switching
 * back to "Default" removes it instantly. See the matching CSS in
 * style.css (search for "is-style-ren-masonry") for how the column count
 * reuses the block's own native "Columns" setting instead of adding a
 * second, separate control.
 */
function ren_register_gallery_masonry_style() {
	register_block_style(
		'core/gallery',
		array(
			'name'  => 'ren-masonry',
			'label' => __( 'Masonry', 'ren' ),
		)
	);
}
add_action( 'init', 'ren_register_gallery_masonry_style' );

/**
 * Enqueue front-end assets.
 */
function ren_assets() {
	wp_enqueue_style( 'ren-style', get_stylesheet_uri(), array(), REN_VERSION );

	wp_enqueue_script(
		'ren-filter',
		REN_URI . '/assets/js/portfolio-filter.js',
		array(),
		REN_VERSION,
		true
	);

	wp_enqueue_script(
		'ren-decorative-shortcodes',
		REN_URI . '/assets/js/decorative-shortcodes.js',
		array(),
		REN_VERSION,
		true
	);

	wp_enqueue_script(
		'ren-responsive-tables',
		REN_URI . '/assets/js/responsive-tables.js',
		array(),
		REN_VERSION,
		true
	);

	wp_enqueue_script(
		'ren-header-scroll',
		REN_URI . '/assets/js/header-scroll.js',
		array(),
		REN_VERSION,
		true
	);

	wp_enqueue_script(
		'ren-search-overlay',
		REN_URI . '/assets/js/search-overlay.js',
		array(),
		REN_VERSION,
		true
	);

	$ren_options = ren_get_options();
	if ( ! empty( $ren_options['enable_font_awesome'] ) ) {
		wp_enqueue_style(
			'ren-font-awesome',
			REN_URI . '/assets/vendor/fontawesome/css/font-awesome.min.css',
			array(),
			'7.3.1'
		);
	}

	if ( ! empty( $ren_options['enable_codemirror'] ) ) {
		wp_enqueue_style(
			'ren-codemirror',
			REN_URI . '/assets/vendor/codemirror/codemirror.css',
			array(),
			'5.65.21'
		);
		wp_enqueue_script(
			'ren-codemirror',
			REN_URI . '/assets/vendor/codemirror/codemirror.js',
			array(),
			'5.65.21',
			true
		);
		wp_enqueue_script(
			'ren-codemirror-xml',
			REN_URI . '/assets/vendor/codemirror/xml.js',
			array( 'ren-codemirror' ),
			'5.65.21',
			true
		);
		wp_enqueue_script(
			'ren-codemirror-init',
			REN_URI . '/assets/js/codemirror-init.js',
			array( 'ren-codemirror', 'ren-codemirror-xml' ),
			REN_VERSION,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ren_assets' );

/**
 * Register blog sidebar widget area (used by parts/sidebar-blog.html via the
 * Legacy Widget block, and by the "Recent Posts" / "Categories" blocks directly).
 */
function ren_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'ren' ),
			'id'            => 'ren-blog-sidebar',
			'description'   => __( 'Widgets shown next to the blog archive and single post.', 'ren' ),
			'before_widget' => '<div class="ren-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="ren-widget__title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'ren_widgets_init' );

/**
 * Register the pattern categories used in /patterns file headers.
 */
function ren_register_pattern_categories() {
	register_block_pattern_category(
		'ren-home',
		array( 'label' => __( 'Ren Studio: Homepage', 'ren' ) )
	);
	register_block_pattern_category(
		'ren',
		array( 'label' => __( 'Ren Studio: Portfolio', 'ren' ) )
	);
	register_block_pattern_category(
		'ren-pages',
		array( 'label' => __( 'Ren Studio: Pages', 'ren' ) )
	);
}
add_action( 'init', 'ren_register_pattern_categories' );

/**
 * Includes.
 */
require_once REN_DIR . '/inc/custom-post-types.php';
require_once REN_DIR . '/inc/post-hero.php';
require_once REN_DIR . '/inc/post-nav-thumbs.php';
require_once REN_DIR . '/inc/default-featured-image.php';
require_once REN_DIR . '/inc/more-articles.php';
require_once REN_DIR . '/inc/archive-title.php';
require_once REN_DIR . '/inc/theme-options.php';
require_once REN_DIR . '/inc/fonts.php';
require_once REN_DIR . '/inc/custom-css-js.php';
require_once REN_DIR . '/inc/social-shortcodes.php';
require_once REN_DIR . '/inc/search-overlay.php';
require_once REN_DIR . '/inc/template-tags.php';
require_once REN_DIR . '/inc/rest-fields.php';
