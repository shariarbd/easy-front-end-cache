<?php
/**
 * Plugin Name: Easy Front End Cache
 * Plugin URI:  https://github.com/shariarbd/easy-front-end-cache
 * Description: A lightweight front-end caching plugin with admin controls, purge options, AJAX-based cache clearing, and colorful admin bar status.
 * Version:     1.8
 * Author:      Shariar
 * Author URI:  https://kivabe.com
 * License:     GPLv2 or later
 * Text Domain: easy-front-end-cache
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define constants
define( 'EFEC_VERSION', '1.8.0' ); // keep version consistent
define( 'EFEC_PATH', plugin_dir_path( __FILE__ ) );
define( 'EFEC_URL', plugin_dir_url( __FILE__ ) );

// Load text domain for translations
function efec_load_textdomain() {
    load_plugin_textdomain( 'easy-front-end-cache', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'efec_load_textdomain' );

// Include required files
require_once EFEC_PATH . 'includes/class-helpers.php';
require_once EFEC_PATH . 'includes/class-cache.php';
require_once EFEC_PATH . 'includes/class-exclusions.php';
require_once EFEC_PATH . 'includes/class-purge.php';

// NEW: modular admin loader
if ( is_admin() ) {
    require_once EFEC_PATH . 'includes/admin/admin-loader.php';
    require_once EFEC_PATH . 'includes/class-admin-bar.php';
}

// Initialize plugin classes
add_action( 'init', function() {
    EFEC_Cache::init();
    EFEC_Exclusions::init();
    EFEC_Purge::init();

    if ( is_admin() ) {
        EFEC_Admin_Bar::init();
    }
});