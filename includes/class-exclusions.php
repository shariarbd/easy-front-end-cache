<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Exclusions logic for Easy Front End Cache
 * -----------------------------------------
 * This class determines when caching should be skipped.
 * Examples:
 * - Admin pages
 * - Feeds, previews, search results
 * - Logged-in users (to avoid caching personalized content)
 * - POST requests or non-GET requests
 */
class EFEC_Exclusions {

    /**
     * Check if current request should be excluded from caching.
     *
     * @return bool True if caching should be skipped
     */
    public static function should_exclude() {
        // Skip admin area
        if ( is_admin() ) {
            return true;
        }

        // Skip feeds (RSS, Atom)
        if ( is_feed() ) {
            return true;
        }

        // Skip search results
        if ( is_search() ) {
            return true;
        }

        // Skip previews
        if ( is_preview() ) {
            return true;
        }

        // Skip logged-in users (to avoid caching personalized content)
        if ( is_user_logged_in() ) {
            return true;
        }

        // Skip POST requests or non-GET requests
        if ( $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
            return true;
        }

        // Allow caching otherwise
        return false;
    }
}