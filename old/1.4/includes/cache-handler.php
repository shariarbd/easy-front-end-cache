<?php
if (!defined('ABSPATH')) exit;

function efc_handle_cache() {

    // ==============================
    // 1️⃣ Skip conditions
    // ==============================
    if (is_admin() || is_user_logged_in()) return;
    if (is_preview()) return;
    if (is_search()) return;

    // ==============================
    // 2️⃣ Get settings
    // ==============================
    $cache_time   = (int) get_option('efc_cache_time', 600);
    $reset_param  = get_option('efc_reset_param', 'reset');
    $reset_all    = get_option('efc_reset_all_param', 'reset_all');
    $allow_public = (int) get_option('efc_allow_public_reset', 0);
    $cache_dir    = WP_CONTENT_DIR . '/efc-cache/';

    if (!is_dir($cache_dir)) {
        wp_mkdir_p($cache_dir);
    }

    $request_uri = $_SERVER['REQUEST_URI'];
    $cache_key   = md5($request_uri);
    $cache_file  = $cache_dir . $cache_key . '.html';

    // ==============================
    // 3️⃣ HANDLE RESET FIRST
    // ==============================

    // Reset ALL cache
    if (isset($_GET[$reset_all]) && $_GET[$reset_all] == 1) {

        if ($allow_public || current_user_can('manage_options')) {

            foreach (glob($cache_dir . '*.html') as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            wp_die(esc_html__('✅ All cache cleared.', 'easy-front-end-cache'));
        }
    }

    // Reset SINGLE page cache
    if (isset($_GET[$reset_param]) && $_GET[$reset_param] == 1) {

        if ($allow_public || current_user_can('manage_options')) {

            if (file_exists($cache_file)) {
                unlink($cache_file);
            }
        }

        // Do NOT cache reset request
        return;
    }

    // ==============================
    // 4️⃣ Allow only safe query strings
    // ==============================

    // Allow default WP params like ?p=858 or ?page_id=10
    $allowed_params = ['p', 'page_id'];

    if (!empty($_GET)) {
        foreach ($_GET as $key => $value) {
            if (!in_array($key, $allowed_params)) {
                return; // Skip caching for custom query strings
            }
        }
    }

    // ==============================
    // 5️⃣ Serve cache if valid
    // ==============================

    if (
        file_exists($cache_file) &&
        (time() - filemtime($cache_file)) < $cache_time
    ) {
        header("X-Cache: HIT");
        readfile($cache_file);
        exit;
    }

    // ==============================
    // 6️⃣ Start output buffering
    // ==============================

    ob_start();

    add_action('wp_footer', function() use ($cache_file) {

        $output = ob_get_contents();

        if ($output !== false && strlen($output) > 0) {
            file_put_contents($cache_file, $output, LOCK_EX);
        }

        ob_end_flush();

    }, 999);
}

add_action('template_redirect', 'efc_handle_cache', 1);