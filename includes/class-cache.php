<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Cache logic for Easy Front End Cache
 * ------------------------------------
 * This class handles:
 * - Serving cached HTML files if available
 * - Writing new cache files for front-end requests
 * - Respecting exclusions (admin, feeds, logged-in users, etc.)
 * - Applying cache lifetimes for posts and pages
 */
class EFEC_Cache {

    /**
     * Initialize cache hooks
     */
    public static function init() {
        // Hook into template redirect (before rendering front-end)
        add_action( 'template_redirect', [ __CLASS__, 'maybe_serve_cache' ], 0 );
        add_action( 'shutdown', [ __CLASS__, 'maybe_store_cache' ], 999 );
    }

    /**
     * Try to serve cached file if available
     */
    public static function maybe_serve_cache() {
        // Skip if exclusions apply
        if ( EFEC_Exclusions::should_exclude() ) {
            return;
        }

        $cache_file = self::get_cache_file();

        // Serve cached file if it exists and is still valid
        if ( file_exists( $cache_file ) ) {
            $lifetime = self::get_cache_lifetime();
            if ( ( time() - filemtime( $cache_file ) ) < $lifetime ) {
                readfile( $cache_file );
                exit; // Stop normal WP rendering
            }
        }
    }

    /**
     * Store cache file after page is rendered
     */
    public static function maybe_store_cache() {
        // Skip if exclusions apply
        if ( EFEC_Exclusions::should_exclude() ) {
            return;
        }

        $cache_file = self::get_cache_file();

        // Capture output buffer
        $output = ob_get_contents();
        if ( $output ) {
            // Ensure cache directory exists
            $dir = dirname( $cache_file );
            if ( ! is_dir( $dir ) ) {
                wp_mkdir_p( $dir );
            }

            // Write cached HTML file
            file_put_contents( $cache_file, $output );
        }
    }

    /**
     * Build cache file path based on current URL
     *
     * @return string Cache file path
     */
    private static function get_cache_file() {
        $dir = WP_CONTENT_DIR . '/efc-cache/';
        $path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
        $key  = md5( $path );
        return $dir . $key . '.html';
    }

    /**
     * Determine cache lifetime based on context
     *
     * @return int Lifetime in seconds
     */
    private static function get_cache_lifetime() {
        if ( is_single() ) {
            return intval( get_option( 'efec_cache_time_posts', 3600 ) );
        }
        if ( is_page() ) {
            return intval( get_option( 'efec_cache_time_pages', 3600 ) );
        }
        // Fallback global lifetime
        return 3600;
    }
}
