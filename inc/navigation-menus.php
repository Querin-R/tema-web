<?php
/**
 * Navigation menus used by parts/header.html and parts/footer.html.
 *
 * Menus imported from a classic menu inside the Site Editor stay in "draft"
 * until the template part that contains them is saved, and the Navigation
 * block renders nothing for a draft menu. Since header and footer are kept
 * as theme files (never saved in the Site Editor), this publishes the two
 * referenced menus once, from the admin.
 *
 * If you create a new menu for header or footer, update these IDs and the
 * "ref" in the matching template part.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REN_NAV_HEADER_ID', 4844 );
define( 'REN_NAV_FOOTER_ID', 4847 );

function ren_publish_theme_navigation_menus() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	foreach ( array( REN_NAV_HEADER_ID, REN_NAV_FOOTER_ID ) as $id ) {
		$nav = get_post( $id );
		if ( $nav && 'wp_navigation' === $nav->post_type && 'publish' !== $nav->post_status ) {
			wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
		}
	}
}
add_action( 'admin_init', 'ren_publish_theme_navigation_menus' );
