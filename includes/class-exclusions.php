<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Exclusions {

    public static function init() {
        // Nothing to hook directly — used by EFEC_Cache::maybe_serve_cache() and ::maybe_store_cache()
    }

    /**
     * Determine if current request should be excluded from caching
     */
    public static function should_exclude() {
        // Admin area
        if ( is_admin() ) {
            return true;
        }

        // Logged-in users
        if ( is_user_logged_in() ) {
            return true;
        }

        // Search results
        if ( is_search() ) {
            return true;
        }

        // Preview mode
        if ( is_preview() ) {
            return true;
        }

        // Feeds
        if ( is_feed() ) {
            return true;
        }

        // REST API requests
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return true;
        }

        // Query strings (avoid caching dynamic URLs)
        if ( ! empty( $_GET ) ) {
            return true;
        }

        // WooCommerce cart/checkout pages
        if ( function_exists( 'is_cart' ) && is_cart() ) {
            return true;
        }
        if ( function_exists( 'is_checkout' ) && is_checkout() ) {
            return true;
        }

        return false;
    }
}