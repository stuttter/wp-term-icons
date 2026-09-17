<?php

define( 'ABSPATH', dirname( __DIR__ ) . '/' );

$GLOBALS['wpti_test'] = array(
	'calls'   => array(),
	'returns' => array(),
);

function wpti_test_call( $name, $arguments = array() ) {
	$GLOBALS['wpti_test']['calls'][ $name ][] = $arguments;

	return isset( $GLOBALS['wpti_test']['returns'][ $name ] )
		? $GLOBALS['wpti_test']['returns'][ $name ]
		: null;
}

function add_action() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function add_filter() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function apply_filters( $name, $value ) {
	wpti_test_call( __FUNCTION__, func_get_args() );
	return $value;
}
function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
function plugin_dir_url() { return 'https://example.test/wp-content/plugins/wp-term-icons/'; }
function plugin_basename() { return 'wp-term-icons/wp-term-icons.php'; }
function esc_html__( $text ) { return $text; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function wp_enqueue_script() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function wp_enqueue_style() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function wp_localize_script() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function register_meta() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function delete_term_meta() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function update_term_meta() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function clean_term_cache() { return wpti_test_call( __FUNCTION__, func_get_args() ); }
function get_term_meta() { return wpti_test_call( __FUNCTION__, func_get_args() ); }

require_once dirname( __DIR__ ) . '/wp-term-icons.php';

$GLOBALS['wpti_initial_calls'] = $GLOBALS['wpti_test']['calls'];
