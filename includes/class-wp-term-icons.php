<?php

/**
 * Term Icons Class
 *
 * @since 0.1.2
 *
 * @package TermIcons/Includes/Class
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Term_Icons' ) ) :
/**
 * Main WP Term Icons class
 *
 * @since 0.1.0
 */
final class WP_Term_Icons extends WP_Term_Meta_UI {

	/**
	 * @var string Plugin version
	 */
	public $version = '0.1.2';

	/**
	 * @var string Database version
	 */
	public $db_version = 201509010001;

	/**
	 * @var string Database version
	 */
	public $db_version_key = 'wpdb_term_icon_version';

	/**
	 * @var string Metadata key
	 */
	public $meta_key = 'icon';

	/**
	 * Hook into queries, admin screens, and more!
	 *
	 * @since 0.1.0
	 */
	public function __construct( $file = '' ) {

		// Setup the labels
		$this->labels = array(
			'singular'    => esc_html__( 'Icon',  'wp-term-color' ),
			'plural'      => esc_html__( 'Icons', 'wp-term-color' ),
			'description' => esc_html__( 'Assign terms a custom icon to visually separate them from each-other.', 'wp-term-color' )
		);

		// Call the parent and pass the file
		parent::__construct( $file );
	}

	/**
	 * Administration area hooks
	 *
	 * @since 0.1.0
	 */
	public function admin_init() {

		// Check for DB update
		$this->maybe_upgrade_database();
	}

	/**
	 * Enqueue quick-edit JS
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		// Enqueue the icons picker
		wp_enqueue_script( 'wp-dashicon-picker', $this->url . 'assets/js/wp-dashicon-picker.js',   array( 'jquery-touch-punch' ), $this->db_version, true  );
		wp_enqueue_style( 'wp-dashicon-picker',  $this->url . 'assets/css/wp-dashicon-picker.css', array( 'dashicons'          ), $this->db_version, false );

		// Enqueue the icons picker
		wp_enqueue_script( 'dashicons-picker', $this->url . 'assets/js/dashicons-picker.js',   array( 'wp-dashicon-picker' ), $this->db_version, true  );
		wp_enqueue_style( 'dashicons-picker',  $this->url . 'assets/css/dashicons-picker.css', array( 'wp-dashicon-picker' ), $this->db_version, false );

		// Enqueue fancy icons; includes quick-edit
		wp_enqueue_script( 'term-icons', $this->url . 'assets/js/term-icons.js', array( 'dashicons-picker' ), $this->db_version, true );
		
		wp_localize_script( 'wp-dashicon-picker', 'wpDashiconPickerL10n', array(
			'clear'         => esc_html__( 'Clear',        'wp-term-icons' ),
			'defaultString' => esc_html__( 'Default',      'wp-term-icons' ),
			'pick'          => esc_html__( 'Select Icon',  'wp-term-icons' ),
			'current'       => esc_html__( 'Current Icon', 'wp-term-icons' )
		) );
	}

	/**
	 * Add help tabs for `color` column
	 *
	 * @since 0.1.2
	 */
	public function help_tabs() {
		get_current_screen()->add_help_tab(array(
			'id'      => 'wp_term_icons_help_tab',
			'title'   => __( 'Term Icon', 'wp-term-icons' ),
			'content' => '<p>' . __( 'Terms can have unique icons to help separate them from each other.', 'wp-term-icons' ) . '</p>',
		) );
	}

	/**
	 * Align custom `color` column
	 *
	 * @since 0.1.0
	 */
	public function admin_head() {
		?>

		<style type="text/css">
			.column-icon {
				width: 74px;
			}
			.term-icon {
				font: normal 25px/25px 'dashicons';
				vertical-align: top;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				width: 25px;
				height: 25px;
				margin-top: 2px;
			}
		</style>

		<?php
	}

	/**
	 * Return the formatted output for the colomn row
	 *
	 * @since 0.1.2
	 *
	 * @param string $meta
	 */
	protected function format_output( $meta = '' ) {
		return '<i data-icon="' . esc_attr( $meta ) . '" class="term-icon dashicons ' . esc_attr( $meta ) . '"></i>';
	}
	

	/**
	 * Output the form field
	 *
	 * @since 0.1.0
	 *
	 * @param  $term
	 */
	protected function form_field( $term = '' ) {

		// Get the meta value
		$value = isset( $term->term_id )
			?  $this->get_meta( $term->term_id )
			: ''; ?>

		<input type="text" name="term-<?php echo esc_attr( $this->meta_key ); ?>" id="term-<?php echo esc_attr( $this->meta_key ); ?>" value="<?php echo esc_attr( $value ); ?>" class="dashicons-picker">

		<?php
	}
}
endif;
