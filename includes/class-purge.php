<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Purge {

    public static function init() {
        // Purge on post update
        if ( get_option( 'efec_purge_on_update' ) ) {
            add_action( 'save_post', [ __CLASS__, 'purge_all' ] );
        }

        // Purge on post delete
        if ( get_option( 'efec_purge_on_delete' ) ) {
            add_action( 'delete_post', [ __CLASS__, 'purge_all' ] );
        }

        // Purge on theme switch
        if ( get_option( 'efec_purge_on_theme_switch' ) ) {
            add_action( 'switch_theme', [ __CLASS__, 'purge_all' ] );
        }

        // Scheduled cleanup
        $frequency = get_option( 'efec_scheduled_cleanup', 'daily' );
        if ( ! wp_next_scheduled( 'efec_scheduled_cleanup_event' ) ) {
            wp_schedule_event( time(), $frequency, 'efec_scheduled_cleanup_event' );
        }
        add_action( 'efec_scheduled_cleanup_event', [ __CLASS__, 'purge_all' ] );
    }

    /**
     * Purge all cache files
     */
    public static function purge_all() {
        EFEC_Cache::purge_all();
    }
}