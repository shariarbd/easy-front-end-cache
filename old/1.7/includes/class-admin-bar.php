<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Admin_Bar {

    public static function init() {
        add_action( 'admin_bar_menu', [ __CLASS__, 'admin_bar_status' ], 100 );
        add_action( 'wp_ajax_efc_clear_cache_ajax', [ __CLASS__, 'handle_ajax_clear' ] );
        add_action( 'wp_ajax_efc_get_cache_stats', [ __CLASS__, 'handle_ajax_stats' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    /**
     * Admin bar status nodes
     */
    public static function admin_bar_status( $wp_admin_bar ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size( $dir );
        $count = EFEC_Helpers::dir_count( $dir );

        $wp_admin_bar->add_node( [
            'id'    => 'efc-status',
            'title' => '<span class="efc-status-green">⚡ Easy Cache</span>',
            'href'  => admin_url( 'options-general.php?page=easy-front-end-cache' ),
        ] );

        $wp_admin_bar->add_node( [
            'id'     => 'efc-title',
            'parent' => 'efc-status',
            'title'  => __('<span style="font-size:.85em;">Easy Front End Cache</span>', 'easy-front-end-cache'),
        ] );

        $wp_admin_bar->add_node( [
            'id'     => 'efc-size',
            'parent' => 'efc-status',
            'title'  => __( 'Size: ', 'easy-front-end-cache' ) . size_format( $size ),
        ] );

        $wp_admin_bar->add_node( [
            'id'     => 'efc-files',
            'parent' => 'efc-status',
            'title'  => __( 'Files: ', 'easy-front-end-cache' ) . intval( $count ),
        ] );

        $wp_admin_bar->add_node( [
            'id'     => 'efc-clear',
            'parent' => 'efc-status',
            'title'  => __( '🧹 Clean All', 'easy-front-end-cache' ),
            'href'   => '#',
            'meta'   => [
                'class' => 'efc-clear-cache-link'
            ]
        ] );
    }

    /**
     * Handle AJAX cache clear
     */
    public static function handle_ajax_clear() {
        // Optional nonce check if you localize nonce in JS
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission denied.', 'easy-front-end-cache' ) ] );
        }

        // Clear all cache files
        EFEC_Cache::purge_all();

        // Recalculate stats AFTER purge
        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size( $dir );
        $count = EFEC_Helpers::dir_count( $dir );

        wp_send_json_success( [
            'message' => __( '✅ Cache cleared successfully.', 'easy-front-end-cache' ),
            'size'    => size_format( $size ),
            'count'   => intval( $count ),
        ] );
    }

    /**
     * Handle AJAX stats fetch
     */
    public static function handle_ajax_stats() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Permission denied.', 'easy-front-end-cache' ) ] );
        }

        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size( $dir );
        $count = EFEC_Helpers::dir_count( $dir );

        wp_send_json_success( [
            'size'  => size_format( $size ),
            'count' => intval( $count ),
        ] );
    }

    /**
     * Enqueue admin JS and CSS
     */
    public static function enqueue_assets() {
        wp_enqueue_script(
            'efc-admin-js',
            EFEC_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            EFEC_VERSION,
            true
        );

        // Optional: localize nonce for AJAX security
        wp_localize_script( 'efc-admin-js', 'efcAdmin', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'efc_admin_nonce' ),
        ] );

        wp_enqueue_style(
            'efc-admin-css',
            EFEC_URL . 'assets/css/admin.css',
            [],
            EFEC_VERSION
        );
    }
}