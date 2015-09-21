<?php

/**
 * Plugin Name: WP Term Icons
 * Plugin URI:  https://wordpress.org/plugins/wp-term-icons/
 * Description: Pretty icons for categories, tags, and other taxonomy terms
 * Author:      John James Jacoby
 * Version:     0.1.3
 * Author URI:  https://profiles.wordpress.org/johnjamesjacoby/
 * License:     GPL v2 or later
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Instantiate the main WordPress Term Color class
 *
 * @since 0.1.0
 */
function _wp_term_icons() {

	// Bail if no term meta
	if ( ! function_exists( 'add_term_meta' ) ) {
		return;
	}

	// Setup the main file
	$file = __FILE__;

	// Include the main class
	include dirname( $file ) . '/includes/class-wp-term-meta-ui.php';
	include dirname( $file ) . '/includes/class-wp-term-icons.php';

	// Instantiate the main class
	new WP_Term_Icons( $file );
}
add_action( 'init', '_wp_term_icons', 98 );
