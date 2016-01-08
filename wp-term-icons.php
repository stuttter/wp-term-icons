<?php

/**
 * Plugin Name: WP Term Icons
 * Plugin URI:  https://wordpress.org/plugins/wp-term-icons/
 * Description: Pretty icons for categories, tags, and other taxonomy terms
 * Author:      John James Jacoby
 * Version:     0.2.0
 * Author URI:  https://profiles.wordpress.org/johnjamesjacoby/
 * License:     GPL v2 or later
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Include the required files & dependencies
 *
 * @since 0.1.0
 */
function _wp_term_icons() {

	// Setup the main file
	$plugin_path = plugin_dir_path( __FILE__ );

	// Include the main class
	require_once $plugin_path . '/includes/class-wp-term-meta-ui.php';
	require_once $plugin_path . '/includes/class-wp-term-icons.php';
}
add_action( 'plugins_loaded', '_wp_term_icons' );

/**
 * Initialize the main class
 *
 * @since 0.2.0
 */
function _wp_term_icons_init() {

	// Instantiate the main class
	new WP_Term_Icons( __FILE__ );
}
add_action( 'init', '_wp_term_icons_init', 79 );
