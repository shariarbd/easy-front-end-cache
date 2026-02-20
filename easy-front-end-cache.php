<?php
/*
Plugin Name: Easy Front End Cache
Description: Lightweight file-based caching for WordPress front-end pages with admin controls.
Version: 1.1
Author: Shariar
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/cache-handler.php';

// Register settings
function efc_register_settings() {
    register_setting('efc_settings_group', 'efc_cache_time');
    register_setting('efc_settings_group', 'efc_reset_param');
    register_setting('efc_settings_group', 'efc_reset_all_param');
    register_setting('efc_settings_group', 'efc_redirect_url');
}
add_action('admin_init', 'efc_register_settings');

// Add admin menu
function efc_add_admin_menu() {
    add_options_page(
        'Easy Front End Cache',
        'Easy Front End Cache',
        'manage_options',
        'easy-front-end-cache',
        'efc_settings_page'
    );
}
add_action('admin_menu', 'efc_add_admin_menu');

// Load admin page
function efc_settings_page() {
    include plugin_dir_path(__FILE__) . 'admin/settings-page.php';
}

// Handle manual clear cache button
function efc_admin_clear_cache() {
    if (isset($_POST['efc_clear_cache']) && check_admin_referer('efc_clear_cache_action','efc_clear_cache_nonce')) {
        $cache_dir = WP_CONTENT_DIR . '/efc-cache/';
        foreach (glob($cache_dir . '*.html') as $file) {
            if (is_file($file)) unlink($file);
        }
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>✅ All cache cleared.</p></div>';
        });
    }
}
add_action('admin_init', 'efc_admin_clear_cache');

// Handle redirect option
function efc_redirect_entire_site() {
    if (is_admin() || is_user_logged_in()) return;
    $redirect_url = get_option('efc_redirect_url', '');
    if (!empty($redirect_url)) {
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('template_redirect', 'efc_redirect_entire_site', 0);