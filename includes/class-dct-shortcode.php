<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DCT_Shortcode {

	private static $enqueued = false;

	public function init() {
		add_shortcode( 'dct_summary', array( $this, 'render_summary' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets() {
		if ( ! self::$enqueued ) {
			return;
		}

		wp_enqueue_style( 'dct-style', DCT_PLUGIN_URL . 'assets/css/admin.css', array(), DCT_VERSION );
		wp_enqueue_script( 'dct-script', DCT_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), DCT_VERSION, true );
		wp_localize_script( 'dct-script', 'DCT', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'dct_nonce' ),
		) );
	}

	public function render_summary( $atts ) {
		self::$enqueued = true;
		$this->enqueue_assets();

		ob_start();
		include DCT_PLUGIN_DIR . 'templates/summary.php';
		return ob_get_clean();
	}
}