<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DCT_Admin {

    public function init() {
        add_action( 'admin_menu', array( $this, 'register_menus' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function register_menus() {
        add_menu_page(
            'Cash Tracker',
            'Cash Tracker',
            'manage_options',
            'dct-dashboard',
            array( $this, 'page_dashboard' ),
            'dashicons-chart-line',
            30
        );

        add_submenu_page( 'dct-dashboard', 'Projects',      'Projects',      'manage_options', 'dct-projects',      array( $this, 'page_projects' ) );
        add_submenu_page( 'dct-dashboard', 'Stakeholders',  'Stakeholders',  'manage_options', 'dct-stakeholders',  array( $this, 'page_stakeholders' ) );
        add_submenu_page( 'dct-dashboard', 'Assign',        'Assign',        'manage_options', 'dct-assign',        array( $this, 'page_assign' ) );
        add_submenu_page( 'dct-dashboard', 'Cash Entry',    'Cash Entry',    'manage_options', 'dct-transactions',  array( $this, 'page_transactions' ) );
        add_submenu_page( 'dct-dashboard', 'Summary',       'Summary',       'manage_options', 'dct-summary',       array( $this, 'page_summary' ) );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'dct-' ) === false ) return;

        wp_enqueue_style( 'dct-style', DCT_PLUGIN_URL . 'assets/css/admin.css', array(), DCT_VERSION );
        wp_enqueue_script( 'dct-script', DCT_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), DCT_VERSION, true );
        wp_localize_script( 'dct-script', 'DCT', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'dct_nonce' ),
        ) );
    }

    public function page_dashboard() {
        include DCT_PLUGIN_DIR . 'templates/dashboard.php';
    }

    public function page_projects() {
        include DCT_PLUGIN_DIR . 'templates/projects.php';
    }

    public function page_stakeholders() {
        include DCT_PLUGIN_DIR . 'templates/stakeholders.php';
    }

    public function page_assign() {
        include DCT_PLUGIN_DIR . 'templates/assign.php';
    }

    public function page_transactions() {
        include DCT_PLUGIN_DIR . 'templates/transactions.php';
    }

    public function page_summary() {
        include DCT_PLUGIN_DIR . 'templates/summary.php';
    }
}
