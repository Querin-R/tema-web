<?php
/**
 * Registers the 'portfolio' custom post type, its taxonomies, and the
 * project-spec meta fields (client, role, year, tools, project URL).
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Portfolio custom post type.
 */
function ren_register_portfolio_cpt() {

	$labels = array(
		'name'                  => _x( 'Portfolio', 'Post type general name', 'ren' ),
		'singular_name'         => _x( 'Project', 'Post type singular name', 'ren' ),
		'menu_name'             => _x( 'Portfolio', 'Admin Menu text', 'ren' ),
		'add_new'               => __( 'Add New Project', 'ren' ),
		'add_new_item'          => __( 'Add New Project', 'ren' ),
		'edit_item'             => __( 'Edit Project', 'ren' ),
		'new_item'              => __( 'New Project', 'ren' ),
		'view_item'             => __( 'View Project', 'ren' ),
		'view_items'            => __( 'View Projects', 'ren' ),
		'search_items'          => __( 'Search Projects', 'ren' ),
		'not_found'             => __( 'No projects found.', 'ren' ),
		'not_found_in_trash'    => __( 'No projects found in Trash.', 'ren' ),
		'all_items'             => __( 'All Projects', 'ren' ),
		'archives'              => __( 'Project Archives', 'ren' ),
		'featured_image'        => __( 'Cover Image', 'ren' ),
		'set_featured_image'    => __( 'Set cover image', 'ren' ),
		'remove_featured_image' => __( 'Remove cover image', 'ren' ),
	);

	$args = array(
		'label'               => __( 'Portfolio', 'ren' ),
		'labels'              => $labels,
		'description'         => __( 'Portfolio / case study projects, kept separate from blog posts.', 'ren' ),
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true, // Required for the block editor + site editor query loops.
		'rest_base'           => 'portfolio',
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-gallery',
		'query_var'           => true,
		'rewrite'             => array( 'slug' => 'work', 'with_front' => false ),
		'capability_type'     => 'post',
		'has_archive'         => 'work',
		'hierarchical'        => false,
		'exclude_from_search' => false,
		'template_lock'       => false,
		'supports'            => array(
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'revisions',
			'custom-fields',
			'page-attributes', // enables manual drag-and-drop ordering (menu_order) for the grid.
		),
	);

	register_post_type( 'portfolio', $args );

	// Hierarchical taxonomy — e.g. Branding, Web Design, Photography.
	register_taxonomy(
		'portfolio_category',
		'portfolio',
		array(
			'labels'            => array(
				'name'          => __( 'Project Categories', 'ren' ),
				'singular_name' => __( 'Project Category', 'ren' ),
			),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'work-category' ),
		)
	);

	// Flat taxonomy for finer filtering — e.g. Editorial, Packaging, 35mm.
	register_taxonomy(
		'portfolio_tag',
		'portfolio',
		array(
			'labels'            => array(
				'name'          => __( 'Project Tags', 'ren' ),
				'singular_name' => __( 'Project Tag', 'ren' ),
			),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'work-tag' ),
		)
	);
}
add_action( 'init', 'ren_register_portfolio_cpt' );

/**
 * Per-post override of Post Title Alignment (Theme Options → Blog sets the
 * site-wide default; this lets an individual post override it to Left,
 * Center, or Right — or "Auto" to just follow the global setting).
 */
function ren_register_post_title_align_meta() {
	foreach ( array( 'post', 'page' ) as $post_type ) {
		register_post_meta(
			$post_type,
			'ren_title_align',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => function ( $value ) {
					return in_array( $value, array( 'left', 'center', 'right' ), true ) ? $value : '';
				},
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'ren_register_post_title_align_meta' );

/**
 * Meta box: lets a single post or page override the site-wide title alignment.
 */
function ren_add_post_title_align_meta_box() {
	foreach ( array( 'post', 'page' ) as $post_type ) {
		add_meta_box(
			'ren_post_title_align',
			__( 'Title Alignment', 'ren' ),
			'ren_render_post_title_align_meta_box',
			$post_type,
			'side',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'ren_add_post_title_align_meta_box' );

/**
 * @param WP_Post $post Current post object.
 */
function ren_render_post_title_align_meta_box( $post ) {
	wp_nonce_field( 'ren_save_post_title_align', 'ren_post_title_align_nonce' );

	$value   = get_post_meta( $post->ID, 'ren_title_align', true );
	$options = array(
		''       => __( 'Auto (follow Theme Options)', 'ren' ),
		'left'   => __( 'Left', 'ren' ),
		'center' => __( 'Center', 'ren' ),
		'right'  => __( 'Right', 'ren' ),
	);
	?>
	<select name="ren_title_align" style="width:100%;">
		<?php foreach ( $options as $val => $label ) : ?>
			<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $value, $val ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<p class="description"><?php esc_html_e( 'Overrides the site-wide alignment just for this post.', 'ren' ); ?></p>
	<?php
}

/**
 * @param int $post_id Post ID.
 */
function ren_save_post_title_align( $post_id ) {
	if ( ! isset( $_POST['ren_post_title_align_nonce'] ) ||
		! wp_verify_nonce( $_POST['ren_post_title_align_nonce'], 'ren_save_post_title_align' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['ren_title_align'] ) ) {
		$value = sanitize_text_field( wp_unslash( $_POST['ren_title_align'] ) );
		$value = in_array( $value, array( 'left', 'center', 'right' ), true ) ? $value : '';
		update_post_meta( $post_id, 'ren_title_align', $value );
	}
}
add_action( 'save_post_post', 'ren_save_post_title_align' );
add_action( 'save_post_page', 'ren_save_post_title_align' );

/**
 * Add a body class reflecting this post's alignment override, if any —
 * lets style.css target just this page without touching every other post.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
/**
 * Add a body class reflecting this post's alignment override, if any —
 * lets style.css target just this page without touching every other post.
 * Uses the same "which post/page's settings actually apply right now"
 * resolution as the Hero feature (inc/post-hero.php), so this also
 * correctly picks up the Blog page's own override while viewing the
 * post listing itself (is_home()), not just when visiting that page
 * directly by URL.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function ren_post_title_align_body_class( $classes ) {
	$post_id = function_exists( 'ren_hero_context_post_id' ) ? ren_hero_context_post_id() : ( is_singular( array( 'post', 'page' ) ) ? get_the_ID() : 0 );
	if ( $post_id ) {
		$value = get_post_meta( $post_id, 'ren_title_align', true );
		if ( in_array( $value, array( 'left', 'center', 'right' ), true ) ) {
			$classes[] = 'ren-post-align-' . $value;
		}
	}
	return $classes;
}
add_filter( 'body_class', 'ren_post_title_align_body_class' );
function ren_rewrite_flush() {
	ren_register_portfolio_cpt();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'ren_rewrite_flush' );

/**
 * Register the project-spec meta fields (client, role, year, tools, external URL).
 * show_in_rest makes them available to the block editor and the REST API.
 */
function ren_register_portfolio_meta() {
	$fields = array(
		'ren_client'      => __( 'Client', 'ren' ),
		'ren_role'        => __( 'Role', 'ren' ),
		'ren_year'        => __( 'Year', 'ren' ),
		'ren_tools'       => __( 'Tools / Software', 'ren' ),
		'ren_project_url' => __( 'Live Project URL', 'ren' ),
	);

	foreach ( $fields as $key => $label ) {
		register_post_meta(
			'portfolio',
			$key,
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
	}
}
add_action( 'init', 'ren_register_portfolio_meta' );

/**
 * Classic meta box for project specs, shown in the post editor sidebar.
 * (A lightweight alternative to a full block-bindings / ACF setup.)
 */
function ren_add_portfolio_meta_box() {
	add_meta_box(
		'ren_project_specs',
		__( 'Project Specs', 'ren' ),
		'ren_render_portfolio_meta_box',
		'portfolio',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'ren_add_portfolio_meta_box' );

/**
 * Render the meta box fields.
 *
 * @param WP_Post $post Current post object.
 */
function ren_render_portfolio_meta_box( $post ) {
	wp_nonce_field( 'ren_save_portfolio_meta', 'ren_portfolio_meta_nonce' );

	$fields = array(
		'ren_client'      => __( 'Client', 'ren' ),
		'ren_role'        => __( 'Role', 'ren' ),
		'ren_year'        => __( 'Year', 'ren' ),
		'ren_tools'       => __( 'Tools / Software', 'ren' ),
		'ren_project_url' => __( 'Live Project URL', 'ren' ),
	);

	echo '<div class="ren-meta-box">';
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		printf(
			'<p><label for="%1$s"><strong>%2$s</strong></label><br />
			<input type="text" id="%1$s" name="%1$s" value="%3$s" style="width:100%%;" /></p>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( $value )
		);
	}
	echo '</div>';
}

/**
 * Save the meta box fields.
 *
 * @param int $post_id Post ID.
 */
function ren_save_portfolio_meta( $post_id ) {
	if ( ! isset( $_POST['ren_portfolio_meta_nonce'] ) ||
		! wp_verify_nonce( $_POST['ren_portfolio_meta_nonce'], 'ren_save_portfolio_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array( 'ren_client', 'ren_role', 'ren_year', 'ren_tools', 'ren_project_url' );

	foreach ( $fields as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}
add_action( 'save_post_portfolio', 'ren_save_portfolio_meta' );
