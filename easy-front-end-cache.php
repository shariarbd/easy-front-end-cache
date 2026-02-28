<?php
/**
 * Plugin Name: Easy Front End Cache
 * Plugin URI:  https://github.com/yourname/easy-front-end-cache
 * Description: A lightweight, admin-friendly front-end caching plugin with instant feedback, overlay animations, cron control, and granular purge logic.
 * Version:     2.0.0
 * Author:      Shariar
 * Author URI:  https://github.com/yourname
 * License:     GPLv2 or later
 * Text Domain: easy-front-end-cache
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ==============================
// Define constants
// ==============================
define( 'EFEC_VERSION', '2.0.0' );
define( 'EFEC_PATH', plugin_dir_path( __FILE__ ) );
define( 'EFEC_URL', plugin_dir_url( __FILE__ ) );

// ==============================
// Autoload includes
// ==============================
require_once EFEC_PATH . 'includes/class-helpers.php';
require_once EFEC_PATH . 'includes/class-cache.php';
require_once EFEC_PATH . 'includes/class-admin.php';
require_once EFEC_PATH . 'includes/class-exclusions.php';
require_once EFEC_PATH . 'includes/class-purge.php';

// ==============================
// Initialize plugin
// ==============================
add_action( 'plugins_loaded', function() {
    // Initialize core classes
    EFEC_Cache::init();
    EFEC_Admin::init();
    EFEC_Purge::init();
});
