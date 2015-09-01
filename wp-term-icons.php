<?php

/**
 * Plugin Name: WP Term Icons
 * Plugin URI:  https://wordpress.org/plugins/wp-term-icons/
 * Description: Pretty icons for categories, tags, and other taxonomy terms
 * Author:      John James Jacoby
 * Version:     0.1.2
 * Author URI:  https://profiles.wordpress.org/johnjamesjacoby/
 * License:     GPL v2 or later
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Term_Icons' ) ) :
/**
 * Main WP Term Icons class
 *
 * @link https://make.wordpress.org/core/2013/07/28/potential-roadmap-for-taxonomy-meta-and-post-relationships/ Taxonomy Roadmap
 *
 * @since 0.1.0
 */
final class WP_Term_Icons {

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
	 * @var string File for plugin
	 */
	public $file = '';

	/**
	 * @var string URL to plugin
	 */
	public $url = '';

	/**
	 * @var string Path to plugin
	 */
	public $path = '';

	/**
	 * @var string Basename for plugin
	 */
	public $basename = '';

	/**
	 * @var boo Whether to use fancy icons
	 */
	public $fancy = false;

	/**
	 * Hook into queries, admin screens, and more!
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Setup plugin
		$this->file     = __FILE__;
		$this->url      = plugin_dir_url( $this->file );
		$this->path     = plugin_dir_path( $this->file );
		$this->basename = plugin_basename( $this->file );
		$this->fancy    = apply_filters( 'wp_fancy_term_icons', true );

		// Queries
		add_action( 'create_term', array( $this, 'add_term_icon' ), 10, 2 );
		add_action( 'edit_term',   array( $this, 'add_term_icon' ), 10, 2 );

		// Get visible taxonomies
		$taxonomies = $this->get_taxonomies();

		// Always hook these in, for ajax actions
		foreach ( $taxonomies as $value ) {

			// Unfancy gets the column
			add_filter( "manage_edit-{$value}_columns",          array( $this, 'add_column_header' ) );
			add_filter( "manage_{$value}_custom_column",         array( $this, 'add_column_value'  ), 10, 3 );
			add_filter( "manage_edit-{$value}_sortable_columns", array( $this, 'sortable_columns'  ) );

			add_action( "{$value}_add_form_fields",  array( $this, 'term_icon_add_form_field'  ) );
			add_action( "{$value}_edit_form_fields", array( $this, 'term_icon_edit_form_field' ) );
		}

		// @todo ajax actions
		//add_action( 'wp_ajax_reiconsing_terms', array( $this, 'ajax_reiconsing_terms' ) );

		// Only blog admin screens
		if ( is_blog_admin() || doing_action( 'wp_ajax_inline_save_tax' ) ) {
			add_action( 'admin_init',         array( $this, 'admin_init' ) );
			add_action( 'load-edit-tags.php', array( $this, 'edit_tags'  ) );
		}
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
	 * Administration area hooks
	 *
	 * @since 0.1.0
	 */
	public function edit_tags() {

		// Enqueue javascript
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_head',            array( $this, 'admin_head'      ) );

		// Quick edit
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_term_icon' ), 10, 3 );
	}

	/** Assets ****************************************************************/

	/**
	 * Enqueue quick-edit JS
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts() {

		// Enqueue the icons picker
		wp_enqueue_script( 'wp-icons-picker', $this->url . 'js/dashicons-picker.js',   array( 'jquery'    ), $this->db_version, true  );
		wp_enqueue_style( 'wp-icons-picker',  $this->url . 'css/dashicons-picker.css', array( 'dashicons' ), $this->db_version, false );

		// Enqueue fancy icons; includes quick-edit
		wp_enqueue_script( 'term-icons', $this->url . 'js/term-icons.js', array( 'wp-icons-picker' ), $this->db_version, true );
	}

	/**
	 * Align custom `icons` column
	 *
	 * @since 0.1.0
	 */
	public function admin_head() {

		// Add the help tab
		get_current_screen()->add_help_tab(array(
			'id'      => 'wp_term_icons_help_tab',
			'title'   => __( 'Term Icon', 'wp-term-icons' ),
			'content' => '<p>' . __( 'Terms can have unique icons to help separate them from each other.', 'wp-term-icons' ) . '</p>',
		) ); ?>

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
	 * Return the taxonomies used by this plugin
	 *
	 * @since 0.1.0
	 *
	 * @param array $args
	 * @return array
	 */
	private static function get_taxonomies( $args = array() ) {

		// Parse arguments
		$r = wp_parse_args( $args, array(
			'show_ui' => true
		) );

		// Get & return the taxonomies
		return get_taxonomies( $r );
	}

	/** Columns ***************************************************************/

	/**
	 * Add the "Color" column to taxonomy terms list-tables
	 *
	 * @since 0.1.0
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_column_header( $columns = array() ) {
		$columns['icon'] = __( 'Icon', 'term-icon' );

		return $columns;
	}

	/**
	 * Output the value for the custom column, in our case: `icon`
	 *
	 * @since 0.1.0
	 *
	 * @param string $empty
	 * @param string $custom_column
	 * @param int    $term_id
	 *
	 * @return mixed
	 */
	public function add_column_value( $empty = '', $custom_column = '', $term_id = 0 ) {

		// Bail if no taxonomy passed or not on the `icon` column
		if ( empty( $_REQUEST['taxonomy'] ) || ( 'icon' !== $custom_column ) || ! empty( $empty ) ) {
			return;
		}

		// Get the icon
		$icon  = $this->get_term_icon( $term_id );
		$retval = '&#8212;';

		// Output HTML element if not empty
		if ( ! empty( $icon ) ) {
			$retval = '<i data-icon="' . $icon . '" class="term-icon dashicons ' . esc_attr( $icon ) . '"></i>';
		}

		echo $retval;
	}

	/**
	 * Allow sorting by `icon`
	 *
	 * @since 0.1.0
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function sortable_columns( $columns = array() ) {
		$columns['icon'] = 'icon';
		return $columns;
	}

	/**
	 * Add `icon` to term when updating
	 *
	 * @since 0.1.0
	 *
	 * @param  int     $term_id
	 * @param  string  $taxonomy
	 */
	public function add_term_icon( $term_id = 0, $taxonomy = '' ) {

		// Bail if not updating icon
		$icon = ! empty( $_POST['term-icon'] )
			? $_POST['term-icon']
			: '';

		self::set_term_icon( $term_id, $taxonomy, $icon );
	}

	/**
	 * Set icon of a specific term
	 *
	 * @since 0.1.0
	 *
	 * @param  int     $term_id
	 * @param  string  $taxonomy
	 * @param  string  $icon
	 * @param  bool    $clean_cache
	 */
	public static function set_term_icon( $term_id = 0, $taxonomy = '', $icon = '', $clean_cache = false ) {

		// No icon, so delete
		if ( empty( $icon ) ) {
			delete_term_meta( $term_id, 'icon' );

		// Update icon value
		} else {
			update_term_meta( $term_id, 'icon', $icon );
		}

		// Maybe clean the term cache
		if ( true === $clean_cache ) {
			clean_term_cache( $term_id, $taxonomy );
		}
	}

	/**
	 * Return the icon of a term
	 *
	 * @since 0.1.0
	 *
	 * @param int $term_id
	 */
	public function get_term_icon( $term_id = 0 ) {
		return get_term_meta( $term_id, 'icon', true );
	}

	/** Markup ****************************************************************/

	/**
	 * Output the "term-icon" form field when adding a new term
	 *
	 * @since 0.1.0
	 */
	public static function term_icon_add_form_field() {
		?>

		<div class="form-field term-icon-wrap">
			<label for="term-icon">
				<?php esc_html_e( 'Icon', 'wp-term-icons' ); ?>
			</label>
			<input type="text" name="term-icon" id="term-icon" value="" size="20">
			<input type="button" data-target="#term-icon" class="button dashicons-picker" value="<?php esc_html_e( 'Choose Icon', 'wp-term-icons' ); ?>" />
			<p class="description">
				<?php esc_html_e( 'Assign terms a custom icon to visually separate them from each-other.', 'wp-term-icons' ); ?>
			</p>
		</div>

		<?php
	}

	/**
	 * Output the "term-icon" form field when editing an existing term
	 *
	 * @since 0.1.0
	 *
	 * @param object $term
	 */
	public function term_icon_edit_form_field( $term = false ) {
		?>

		<tr class="form-field term-icon-wrap">
			<th scope="row" valign="top">
				<label for="term-icon">
					<?php esc_html_e( 'Icon', 'wp-term-icons' ); ?>
				</label>
			</th>
			<td>
				<input name="term-icon" id="term-icon" type="text" value="<?php echo $this->get_term_icon( $term->term_id ); ?>" size="20" />
				<input type="button" data-target="#term-icon" class="button dashicons-picker" value="<?php esc_html_e( 'Choose Icon', 'wp-term-icons' ); ?>" />
				<p class="description">
					<?php esc_html_e( 'Assign terms a custom icon to visually separate them from each-other.', 'wp-term-icons' ); ?>
				</p>
			</td>
		</tr>

		<?php
	}

	/**
	 * Output the "term-icon" quick-edit field
	 *
	 * @since 0.1.0
	 *
	 * @param  $term
	 */
	public function quick_edit_term_icon( $column_name = '', $screen = '', $name = '' ) {

		// Bail if not the `icon` column on the `edit-tags` screen for a visible taxonomy
		if ( ( 'icon' !== $column_name ) || ( 'edit-tags' !== $screen ) || ! in_array( $name, $this->get_taxonomies() ) ) {
			return false;
		} ?>

		<fieldset>
			<div class="inline-edit-col">
				<label>
					<span class="title"><?php esc_html_e( 'Icon', 'wp-term-icons' ); ?></span>
					<span class="input-text-wrap">
						<input type="text" class="ptitle" name="term-icon" value="" size="20">
					</span>
				</label>
			</div>
		</fieldset>

		<?php
	}

	/** Database Alters *******************************************************/

	/**
	 * Should a database update occur
	 *
	 * Runs on `init`
	 *
	 * @since 0.1.0
	 */
	private function maybe_upgrade_database() {

		// Check DB for version
		$db_version = get_option( $this->db_version_key );

		// Needs
		if ( $db_version < $this->db_version ) {
			$this->upgrade_database( $db_version );
		}
	}

	/**
	 * Modify the `term_taxonomy` table and add an `icon` column to it
	 *
	 * @since 0.1.0
	 *
	 * @param  int    $old_version
	 *
	 * @global object $wpdb
	 */
	private function upgrade_database( $old_version = 0 ) {
		global $wpdb;

		$old_version = (int) $old_version;

		// The main column alter
		if ( $old_version < 201508240001 ) {
			// Nothing to do here yet
		}

		// Update the DB version
		update_option( $this->db_version_key, $this->db_version );
	}
}
endif;

/**
 * Instantiate the main WordPress Term Color class
 *
 * @since 0.1.0
 */
function _wp_term_icon() {

	// Bail if no term meta
	if ( ! function_exists( 'add_term_meta' ) ) {
		return;
	}

	new WP_Term_Icons();
}
add_action( 'init', '_wp_term_icon', 98 );
