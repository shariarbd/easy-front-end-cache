<?php
/*
Plugin Name: Easy Front End Cache
Description: Lightweight file-based caching for WordPress front-end pages with admin controls.
Version: 1.3
Author: Shariar
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit;

// Include caching logic
require_once plugin_dir_path(__FILE__) . 'includes/cache-handler.php';

// Register plugin settings
function efc_register_settings() {
    register_setting('efc_settings_group', 'efc_cache_time');
    register_setting('efc_settings_group', 'efc_reset_param');
    register_setting('efc_settings_group', 'efc_reset_all_param');
    register_setting('efc_settings_group', 'efc_redirect_url');
    register_setting('efc_settings_group', 'efc_allow_public_reset');
}
add_action('admin_init', 'efc_register_settings');

// Add admin menu
function efc_add_admin_menu() {
    add_options_page(
        __('Easy Front End Cache', 'easy-front-end-cache'),
        __('Easy Front End Cache', 'easy-front-end-cache'),
        'manage_options',
        'easy-front-end-cache',
        'efc_settings_page'
    );
}
add_action('admin_menu', 'efc_add_admin_menu');

// Load admin settings page
function efc_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
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
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('✅ All cache cleared.', 'easy-front-end-cache') . '</p></div>';
        });
    }
}
add_action('admin_init', 'efc_admin_clear_cache');

// Handle redirect option
function efc_redirect_entire_site() {
    if (is_admin() || is_user_logged_in()) return;
    $redirect_url = get_option('efc_redirect_url', '');
    if (!empty($redirect_url)) {
        wp_redirect(esc_url_raw($redirect_url));
        exit;
    }
}
add_action('template_redirect', 'efc_redirect_entire_site', 0);

// Protect cache directory
function efc_protect_cache_dir() {
    $cache_dir = WP_CONTENT_DIR . '/efc-cache/';
    if (!is_dir($cache_dir)) {
        wp_mkdir_p($cache_dir);
    }

    $htaccess_file = $cache_dir . '.htaccess';
    if (!file_exists($htaccess_file)) {
        $rules = "Options -Indexes\n<FilesMatch \"\\.html$\">\n    Require all denied\n</FilesMatch>\n";
        file_put_contents($htaccess_file, $rules);
    }

    $index_file = $cache_dir . 'index.php';
    if (!file_exists($index_file)) {
        file_put_contents($index_file, "<?php // Silence is golden");
    }
}
add_action('init', 'efc_protect_cache_dir');

function efc_admin_assets($hook) {
    if ($hook !== 'settings_page_easy-front-end-cache') {
        return;
    }
    wp_enqueue_style('efc-admin-css', plugin_dir_url(__FILE__) . 'assets/css/admin.css', [], '1.0');
    wp_enqueue_script('efc-admin-js', plugin_dir_url(__FILE__) . 'assets/js/admin.js', [], '1.0', true);
}
add_action('admin_enqueue_scripts', 'efc_admin_assets');