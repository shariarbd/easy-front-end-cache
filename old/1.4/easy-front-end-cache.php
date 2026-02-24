<?php
/*
Plugin Name: Easy Front End Cache
Description: Lightweight file-based caching for WordPress front-end pages with admin controls.
Version: 1.4
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





// Add colorful Easy Cache status to admin bar
function efc_admin_bar_status($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return; // Only show for admins
    }

    $cache_dir = WP_CONTENT_DIR . '/efc-cache/';
    $size = 0;
    $count = 0;

    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . '*.html');
        if ($files) {
            foreach ($files as $file) {
                $size += filesize($file);
            }
            $count = count($files);
        }
    }

    // Main colorful node
    $wp_admin_bar->add_node([
        'id'    => 'efc-status',
        'title' => '<span style="color:#2ecc71;font-weight:bold;">⚡ Easy Cache</span>',
        'href'  => admin_url('options-general.php?page=easy-front-end-cache'),
    ]);

    // Sub-node: Title
    $wp_admin_bar->add_node([
        'id'     => 'efc-title',
        'parent' => 'efc-status',
        'title'  => __('Easy Front End Cache', 'easy-front-end-cache'),
        'href'   => admin_url('options-general.php?page=easy-front-end-cache'),
    ]);

    // Sub-node: Size
    $wp_admin_bar->add_node([
        'id'     => 'efc-size',
        'parent' => 'efc-status',
        'title'  => __('Size: ', 'easy-front-end-cache') . size_format($size),
    ]);

    // Sub-node: Files
    $wp_admin_bar->add_node([
        'id'     => 'efc-files',
        'parent' => 'efc-status',
        'title'  => __('Files: ', 'easy-front-end-cache') . intval($count),
    ]);

    // Sub-node: Clean All (instant action)
    $wp_admin_bar->add_node([
        'id'     => 'efc-clear',
        'parent' => 'efc-status',
        'title'  => __('🧹 Clean All', 'easy-front-end-cache'),
        'href'   => wp_nonce_url(
            admin_url('admin-post.php?action=efc_clear_cache_bar'),
            'efc_clear_cache_action',
            'efc_clear_cache_nonce'
        ),
    ]);

    // Sub-node: Go to settings
    $wp_admin_bar->add_node([
        'id'     => 'efc-settings',
        'parent' => 'efc-status',
        'title'  => __('⚙️ Go Settings', 'easy-front-end-cache'),
        'href'   => admin_url('options-general.php?page=easy-front-end-cache'),
    ]);
}
add_action('admin_bar_menu', 'efc_admin_bar_status', 100);

// Handle "Clean All" from admin bar
function efc_handle_admin_bar_clear() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Permission denied.', 'easy-front-end-cache'));
    }

    check_admin_referer('efc_clear_cache_action', 'efc_clear_cache_nonce');

    $cache_dir = WP_CONTENT_DIR . '/efc-cache/';
    foreach (glob($cache_dir . '*.html') as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    // Success notice
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('✅ Cache cleared successfully.', 'easy-front-end-cache') . '</p></div>';
    });

    wp_safe_redirect(wp_get_referer() ?: admin_url());
    exit;
}
add_action('admin_post_efc_clear_cache_bar', 'efc_handle_admin_bar_clear');
