<?php
/**
 * REST API additions.
 *
 * @package Ren_Portfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add a computed `ren_reading_time` field to the posts REST response.
 */
function ren_register_rest_fields() {
	register_rest_field(
		'post',
		'ren_reading_time',
		array(
			'get_callback' => function ( $post ) {
				return ren_reading_time( $post['id'] );
			},
			'schema'       => array(
				'type'        => 'integer',
				'description' => __( 'Estimated reading time in minutes.', 'ren' ),
				'context'     => array( 'view' ),
			),
		)
	);
}
add_action( 'rest_api_init', 'ren_register_rest_fields' );
