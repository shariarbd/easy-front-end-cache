<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Purge {

    public static function init() {
        // Purge on post update
        if ( get_option( 'efec_purge_on_update' ) ) {
            add_action( 'save_post', [ __CLASS__, 'purge_post' ] );
        }

        // Purge on post delete
        if ( get_option( 'efec_purge_on_delete' ) ) {
            add_action( 'delete_post', [ __CLASS__, 'purge_post' ] );
        }

        // Purge on theme switch
        if ( get_option( 'efec_purge_on_theme_switch' ) ) {
            add_action( 'switch_theme', [ __CLASS__, 'purge_all' ] );
        }

        // Scheduled cleanup (only if enabled)
        if ( get_option( 'efec_enable_cron_cleanup' ) ) {
            $frequency = get_option( 'efec_scheduled_cleanup', 'daily' );
            if ( ! wp_next_scheduled( 'efec_scheduled_cleanup_event' ) ) {
                wp_schedule_event( time(), $frequency, 'efec_scheduled_cleanup_event' );
            }
            add_action( 'efec_scheduled_cleanup_event', [ __CLASS__, 'purge_all' ] );
        }
    }

    /**
     * Purge cache for a specific post and homepage
     */
    public static function purge_post( $post_id ) {
        // Purge single post cache
        $url = get_permalink( $post_id );
        if ( $url ) {
            $key = md5( wp_parse_url( $url, PHP_URL_PATH ) );
            $file = WP_CONTENT_DIR . '/efc-cache/' . $key . '.html';
            EFEC_Helpers::safe_unlink( $file );
        }

        // Purge homepage cache
        $home_url = home_url('/');
        $home_key = md5( wp_parse_url( $home_url, PHP_URL_PATH ) );
        $home_file = WP_CONTENT_DIR . '/efc-cache/' . $home_key . '.html';
        EFEC_Helpers::safe_unlink( $home_file );
    }

    /**
     * Purge all cache files
     */
    public static function purge_all() {
        EFEC_Cache::purge_all();
    }
}