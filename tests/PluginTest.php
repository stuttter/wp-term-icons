<?php

use PHPUnit\Framework\TestCase;

final class PluginTest extends TestCase {
	protected function setUp(): void {
		$GLOBALS['wpti_test']['calls']   = array();
		$GLOBALS['wpti_test']['returns'] = array();
	}

	public function test_bootstrap_registers_plugin_hooks(): void {
		$this->assertContains( array( 'plugins_loaded', '_wp_term_icons' ), $GLOBALS['wpti_initial_calls']['add_action'] );
		$this->assertContains( array( 'init', '_wp_term_icons_init', 79 ), $GLOBALS['wpti_initial_calls']['add_action'] );
	}

	public function test_loader_makes_the_main_class_available(): void {
		_wp_term_icons();

		$this->assertTrue( class_exists( 'WP_Term_Icons' ) );
		$this->assertTrue( class_exists( 'JJJ\\WP\\Term\\Meta\\UI' ) );
	}

	public function test_icon_markup_escapes_the_stored_class(): void {
		$icons  = $this->icons();
		$method = new ReflectionMethod( $icons, 'format_output' );
		if ( PHP_VERSION_ID < 80100 ) {
			$method->setAccessible( true );
		}

		$this->assertSame(
			'<i data-icon="dashicons-admin-site&quot; onclick=&quot;bad" class="term-icon dashicons dashicons-admin-site&quot; onclick=&quot;bad"></i>',
			$method->invoke( $icons, 'dashicons-admin-site" onclick="bad' )
		);
	}

	public function test_icon_assets_preserve_public_handles_and_dependencies(): void {
		$icons = $this->icons();
		$icons->enqueue_scripts();

		$this->assertSame( 'wp-dashicon-picker', $GLOBALS['wpti_test']['calls']['wp_enqueue_script'][0][0] );
		$this->assertSame( array( 'jquery-touch-punch' ), $GLOBALS['wpti_test']['calls']['wp_enqueue_script'][0][2] );
		$this->assertSame( 'dashicons-picker', $GLOBALS['wpti_test']['calls']['wp_enqueue_script'][1][0] );
		$this->assertSame( array( 'wp-dashicon-picker' ), $GLOBALS['wpti_test']['calls']['wp_enqueue_script'][1][2] );
		$this->assertSame( 'term-icons', $GLOBALS['wpti_test']['calls']['wp_enqueue_script'][2][0] );
		$this->assertSame( array( 'dashicons-picker' ), $GLOBALS['wpti_test']['calls']['wp_enqueue_script'][2][2] );
		$this->assertSame( 'wpDashiconPickerL10n', $GLOBALS['wpti_test']['calls']['wp_localize_script'][0][1] );
	}

	public function test_register_meta_preserves_icon_callbacks(): void {
		$icons = $this->icons();
		$icons->register_meta();

		$registration = $GLOBALS['wpti_test']['calls']['register_meta'][0];
		$this->assertSame( 'term', $registration[0] );
		$this->assertSame( 'icon', $registration[1] );
		$this->assertSame( array( $icons, 'auth_callback' ), $registration[2]['auth_callback'] );
		$this->assertSame( array( $icons, 'sanitize_callback' ), $registration[2]['sanitize_callback'] );
	}

	public function test_column_contract_uses_the_icon_key(): void {
		$icons = $this->icons();

		$this->assertSame( 'Icon', $icons->add_column_header( array() )['icon'] );
		$this->assertSame( 'icon', $icons->sortable_columns( array() )['icon'] );
	}

	public function test_set_meta_updates_and_deletes_the_icon_value(): void {
		$icons = $this->icons();
		$icons->set_meta( 42, 'category', 'dashicons-admin-site', true );
		$icons->set_meta( 42, 'category', '', true );

		$this->assertSame( array( array( 42, 'icon', 'dashicons-admin-site' ) ), $GLOBALS['wpti_test']['calls']['update_term_meta'] );
		$this->assertSame( array( array( 42, 'icon' ) ), $GLOBALS['wpti_test']['calls']['delete_term_meta'] );
		$this->assertSame(
			array( array( 42, 'category' ), array( 42, 'category' ) ),
			$GLOBALS['wpti_test']['calls']['clean_term_cache']
		);
	}

	public function test_get_meta_reads_the_icon_value(): void {
		$icons = $this->icons();
		$GLOBALS['wpti_test']['returns']['get_term_meta'] = 'dashicons-admin-site';

		$this->assertSame( 'dashicons-admin-site', $icons->get_meta( 42 ) );
		$this->assertSame( array( array( 42, 'icon', true ) ), $GLOBALS['wpti_test']['calls']['get_term_meta'] );
	}

	public function test_unrelated_taxonomy_ordering_is_untouched(): void {
		$icons   = $this->icons();
		$clauses = array(
			'fields'  => 't.*',
			'join'    => '',
			'where'   => '',
			'orderby' => 'ORDER BY t.name',
		);

		$this->assertSame( $clauses, $icons->terms_clauses( $clauses, array( 'post_tag' ), array( 'orderby' => 'icon' ) ) );
	}

	private function icons(): WP_Term_Icons {
		$icons             = new WP_Term_Icons( dirname( __DIR__ ) . '/wp-term-icons.php' );
		$icons->taxonomies = array( 'category', 'product_cat' );

		return $icons;
	}
}
