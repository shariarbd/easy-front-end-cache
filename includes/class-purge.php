<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Purge logic for Easy Front End Cache
 * ------------------------------------
 * This class handles automatic cache clearing when:
 * - Posts are updated or deleted
 * - Themes are switched
 * - Scheduled cron jobs run
 */
class EFEC_Purge {

    /**
     * Initialize purge hooks
     */
    public static function init() {
        // Clear cache when posts are updated
        if ( get_option( 'efec_purge_on_update' ) ) {
            add_action( 'save_post', [ __CLASS__, 'purge_post' ] );
        }

        // Clear cache when posts are deleted
        if ( get_option( 'efec_purge_on_delete' ) ) {
            add_action( 'delete_post', [ __CLASS__, 'purge_post' ] );
        }

        // Clear cache when theme is switched
        if ( get_option( 'efec_purge_on_theme_switch' ) ) {
            add_action( 'switch_theme', [ __CLASS__, 'purge_all' ] );
        }

        // Scheduled cleanup (WP-Cron)
        if ( get_option( 'efec_enable_cron_cleanup' ) ) {
            $frequency = get_option( 'efec_scheduled_cleanup', 'daily' );

            // Clear existing schedule if frequency changed
            wp_clear_scheduled_hook( 'efec_scheduled_cleanup_event' );

            if ( ! wp_next_scheduled( 'efec_scheduled_cleanup_event' ) ) {
                wp_schedule_event( time(), $frequency, 'efec_scheduled_cleanup_event' );
            }

            add_action( 'efec_scheduled_cleanup_event', [ __CLASS__, 'purge_all' ] );
        }
    }

    /**
     * Purge cache for a specific post and homepage
     *
     * @param int $post_id Post ID
     */
    public static function purge_post( $post_id ) {
        $dir = WP_CONTENT_DIR . '/efc-cache/';
        if ( ! is_dir( $dir ) ) {
            return;
        }

        // Build cache key for post URL
        $url       = get_permalink( $post_id );
        $cache_key = md5( parse_url( $url, PHP_URL_PATH ) );
        $cache_file = $dir . $cache_key . '.html';

        // Delete post cache file
        EFEC_Helpers::safe_unlink( $cache_file );

        // Also clear homepage cache (since it lists posts)
        $home_key  = md5( '/' );
        $home_file = $dir . $home_key . '.html';
        EFEC_Helpers::safe_unlink( $home_file );
    }

    /**
     * Purge all cache files
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
