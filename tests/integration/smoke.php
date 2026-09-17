<?php

/**
 * Exercise the public icon metadata and asset contract in WordPress.
 *
 * This file is loaded by the centrally maintained integration runner after the
 * production plugin build has been activated.
 *
 * @package WP_Term_IconsTests
 */

defined( 'ABSPATH' ) || exit;

$assert = static function ( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
};

$term_id = 0;
$suffix  = strtolower( wp_generate_password( 12, false, false ) );

try {
	$assert( ! is_multisite(), 'WP Term Icons must use the single-site integration profile.' );
	$assert( class_exists( 'WP_Term_Icons' ), 'The production plugin did not load.' );

	$term = wp_insert_term(
		'Portfolio icon smoke ' . $suffix,
		'category',
		array( 'slug' => 'portfolio-icon-smoke-' . $suffix )
	);
	$assert( ! is_wp_error( $term ), 'WordPress could not create a smoke-test category.' );
	$term_id = (int) $term['term_id'];

	update_term_meta( $term_id, 'icon', 'dashicons-admin-site' );
	$assert( 'dashicons-admin-site' === get_term_meta( $term_id, 'icon', true ), 'WordPress could not persist the icon metadata.' );

	$icons = new WP_Term_Icons( dirname( __DIR__, 2 ) . '/wp-term-icons.php' );
	$icons->enqueue_scripts();
	$assert( wp_script_is( 'wp-dashicon-picker', 'enqueued' ), 'The picker script was not enqueued.' );
	$assert( wp_script_is( 'dashicons-picker', 'enqueued' ), 'The icon catalog script was not enqueued.' );
	$assert( wp_script_is( 'term-icons', 'enqueued' ), 'The term integration script was not enqueued.' );
	$assert( wp_style_is( 'wp-dashicon-picker', 'enqueued' ), 'The picker stylesheet was not enqueued.' );
	$assert( wp_style_is( 'dashicons-picker', 'enqueued' ), 'The icon catalog stylesheet was not enqueued.' );
} finally {
	if ( $term_id ) {
		wp_delete_term( $term_id, 'category' );
	}
}
