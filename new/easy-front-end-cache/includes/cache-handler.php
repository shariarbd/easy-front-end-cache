<?php
if (!defined('ABSPATH')) exit;

function efc_handle_cache() {
    // Skip caching for admin area or logged-in users
    if (is_admin() || is_user_logged_in()) return;

    // Skip caching for WordPress preview pages
    if (is_preview()) return;

    // Skip caching for WordPress search results
    if (is_search()) return;

    // Get plugin options
    $cache_time   = (int) get_option('efc_cache_time', 600);
    $reset_param  = get_option('efc_reset_param', 'reset');
    $reset_all    = get_option('efc_reset_all_param', 'reset_all');
    $allow_public = get_option('efc_allow_public_reset', 0);
    $cache_dir    = WP_CONTENT_DIR . '/efc-cache/';

    if (!is_dir($cache_dir)) {
        wp_mkdir_p($cache_dir);
    }

    // Skip caching if reset parameters are present
    if (isset($_GET[$reset_param]) || isset($_GET[$reset_all])) {
        return;
    }

    // Skip caching if any query string exists (user variables)
    if (!empty($_SERVER['QUERY_STRING'])) {
        return;
    }

    $cache_key  = md5($_SERVER['REQUEST_URI']);
    $cache_file = $cache_dir . $cache_key . '.html';

    // Reset all cache
    if (isset($_GET[$reset_all]) && $_GET[$reset_all] == 1) {
        if ($allow_public || current_user_can('manage_options')) {
            foreach (glob($cache_dir . '*.html') as $file) {
                if (is_file($file)) unlink($file);
            }
            wp_die(esc_html__('✅ All cache cleared.', 'easy-front-end-cache'));
        }
    }

    // Reset single page cache
    if (isset($_GET[$reset_param]) && $_GET[$reset_param] == 1) {
        if ($allow_public || current_user_can('manage_options')) {
            if (file_exists($cache_file)) unlink($cache_file);
        }
    }

    // Serve cached file if valid
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        header("X-Cache: HIT");
        echo file_get_contents($cache_file);
        exit;
    }

    // Start output buffering
    ob_start();

    // Save cache at footer
    add_action('wp_footer', function() use ($cache_file) {
        $output = ob_get_contents();
        if ($output !== false && strlen($output) > 0) {
            file_put_contents($cache_file, $output, LOCK_EX);
        }
        ob_end_flush();
    }, 999);
}
add_action('template_redirect', 'efc_handle_cache', 0);