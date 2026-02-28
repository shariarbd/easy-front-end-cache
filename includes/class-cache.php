<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Cache {

    public static function init() {
        // Hook into template_redirect early to start caching logic
        add_action( 'template_redirect', [ __CLASS__, 'handle_cache' ], 1 );
    }

    /**
     * Main cache handler
     */
    public static function handle_cache() {
        // ==============================
        // 1️⃣ Skip conditions
        // ==============================
        // Use centralized exclusions class for cleaner logic
        if ( EFEC_Exclusions::should_exclude() ) {
            return;
        }

        // ==============================
        // 2️⃣ Get settings
        // ==============================
        // Separate lifetimes for posts vs pages, fallback to global
        if ( is_singular('post') ) {
            $cache_time = (int) get_option( 'efc_cache_time_posts', 600 );
        } elseif ( is_page() ) {
            $cache_time = (int) get_option( 'efc_cache_time_pages', 1200 );
        } else {
            $cache_time = (int) get_option( 'efc_cache_time', 600 );
        }

        $reset_param  = get_option( 'efc_reset_param', 'reset' );
        $reset_all    = get_option( 'efc_reset_all_param', 'reset_all' );
        $allow_public = (int) get_option( 'efc_allow_public_reset', 0 );
        $cache_dir    = WP_CONTENT_DIR . '/efc-cache/';

        // Ensure cache directory exists
        if ( ! is_dir( $cache_dir ) ) {
            wp_mkdir_p( $cache_dir );
            // Drop an index.html file for security (prevent directory browsing)
            if ( ! file_exists( $cache_dir . 'index.html' ) ) {
                file_put_contents( $cache_dir . 'index.html', '' );
            }
        }

        $request_uri = $_SERVER['REQUEST_URI'];
        $cache_key   = md5( $request_uri );
        $cache_file  = $cache_dir . $cache_key . '.html';

        // ==============================
        // 3️⃣ Handle reset first
        // ==============================
        if ( isset( $_GET[$reset_all] ) && $_GET[$reset_all] == 1 ) {
            if ( $allow_public || current_user_can( 'manage_options' ) ) {
                foreach ( glob( $cache_dir . '*.html' ) as $file ) {
                    EFEC_Helpers::safe_unlink( $file );
                }
                wp_die( esc_html__( '✅ All cache cleared.', 'easy-front-end-cache' ) );
            }
        }

        if ( isset( $_GET[$reset_param] ) && $_GET[$reset_param] == 1 ) {
            if ( $allow_public || current_user_can( 'manage_options' ) ) {
                EFEC_Helpers::safe_unlink( $cache_file );
            }
            return; // Do not cache reset requests
        }

        // ==============================
        // 4️⃣ Allow only safe query strings
        // ==============================
        $allowed_params = [ 'p', 'page_id' ];
        if ( ! empty( $_GET ) ) {
            foreach ( $_GET as $key => $value ) {
                if ( ! in_array( $key, $allowed_params ) ) {
                    return; // Skip caching for custom query strings
                }
            }
        }

        // ==============================
        // 5️⃣ Serve cache if valid
        // ==============================
        if ( file_exists( $cache_file ) && ( time() - filemtime( $cache_file ) ) < $cache_time ) {
            if ( get_option( 'efc_debug_mode' ) ) {
                header( "X-Easy-Cache: HIT" );
            }
            readfile( $cache_file );
            exit;
        }

        // ==============================
        // 6️⃣ Start output buffering
        // ==============================
        ob_start();

        add_action( 'wp_footer', function() use ( $cache_file ) {
            $output = ob_get_contents();
            if ( $output !== false && strlen( $output ) > 0 ) {
                // Minify if enabled (basic whitespace compression)
                if ( get_option( 'efc_minify_html' ) ) {
                    $output = preg_replace( '/\s+/', ' ', $output );
                }
                file_put_contents( $cache_file, $output, LOCK_EX );
            }
            ob_end_flush();

            if ( get_option( 'efc_debug_mode' ) ) {
                header( "X-Easy-Cache: MISS" );
            }
        }, 999 );
    }

    /**
     * Purge all cache files (used by admin/AJAX)
     */
    public static function purge_all() {
        $dir = WP_CONTENT_DIR . '/efc-cache/';
        if ( is_dir( $dir ) ) {
            foreach ( glob( $dir . '*.html' ) as $file ) {
                EFEC_Helpers::safe_unlink( $file );
            }
        }
    }
}