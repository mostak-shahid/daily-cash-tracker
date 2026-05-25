<?php
/**
 * Plugin Name: Daily Cash Tracker
 * Description: Track daily cash flow across multiple projects and stakeholders.
 * Version: 1.0.1
 * Author: Daily Cash Tracker
 * Text Domain: daily-cash-tracker
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DCT_VERSION', '1.0.1' );
define( 'DCT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DCT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'CATEGORIES', ["Cash In", "Cash Out", "Labor", "Salary", "Materials", "Equipment", "Transport", "Owners Costs", "Subcontractor", "Other"] );
define( 'PHASES', ["Site Preparation", "Pilling", "Basement", "Ground Floor", "First Floor", "Second Floor", "Third Floor", "Fourth Floor", "Fifth Floor", "Sixth Floor", "Seventh Floor"] );

/**
 * The core class that is used to define internationalization, 
 * caching, and others.
 */
if (file_exists(DCT_PLUGIN_DIR . '/vendor/autoload.php')) {
    require_once DCT_PLUGIN_DIR . '/vendor/autoload.php';
}

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://raw.githubusercontent.com/mostak-shahid/update/refs/heads/master/daily-cash-tracker.json',
	__FILE__, //Full path to the main plugin file or functions.php.
	'daily-cash-tracker'
);

require_once DCT_PLUGIN_DIR . 'includes/class-dct-db.php';
require_once DCT_PLUGIN_DIR . 'includes/class-dct-admin.php';
require_once DCT_PLUGIN_DIR . 'includes/class-dct-ajax.php';
require_once DCT_PLUGIN_DIR . 'includes/class-dct-shortcode.php';

register_activation_hook( __FILE__, array( 'DCT_DB', 'install' ) );

add_action( 'plugins_loaded', 'dct_init' );
function dct_init() {
    $admin = new DCT_Admin();
    $admin->init();

    $ajax = new DCT_Ajax();
    $ajax->init();

    $shortcode = new DCT_Shortcode();
    $shortcode->init();
}