<?php
/**
 * Plugin Name: Daily Cash Tracker
 * Description: Track daily cash flow across multiple projects and stakeholders.
 * Version: 1.0.0
 * Author: Daily Cash Tracker
 * Text Domain: daily-cash-tracker
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DCT_VERSION', '1.0.0' );
define( 'DCT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DCT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once DCT_PLUGIN_DIR . 'includes/class-dct-db.php';
require_once DCT_PLUGIN_DIR . 'includes/class-dct-admin.php';
require_once DCT_PLUGIN_DIR . 'includes/class-dct-ajax.php';

register_activation_hook( __FILE__, array( 'DCT_DB', 'install' ) );

add_action( 'plugins_loaded', 'dct_init' );
function dct_init() {
    $admin = new DCT_Admin();
    $admin->init();

    $ajax = new DCT_Ajax();
    $ajax->init();
}
