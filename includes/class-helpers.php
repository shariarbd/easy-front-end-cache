<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Utility helper functions for Easy Front End Cache
 * -------------------------------------------------
 * This class provides reusable methods for:
 * - Calculating directory size and file count
 * - Safely deleting files
 * - Displaying next scheduled cron run time in WP local time
 */
class EFEC_Helpers {

    /**
     * Calculate total size of a directory (recursive).
     *
     * @param string $dir Directory path
     * @return int Size in bytes
     */
    public static function dir_size( $dir ) {
        $size = 0;
        if ( is_dir( $dir ) ) {
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) ) as $file ) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    /**
     * Count number of files in a directory.
     *
     * @param string $dir Directory path
     * @return int Number of files
     */
    public static function dir_count( $dir ) {
        $count = 0;
        if ( is_dir( $dir ) ) {
            foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) ) as $file ) {
                if ( $file->isFile() ) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Safely delete a file (with error suppression).
     *
     * @param string $file File path
     * @return bool True if deleted, false otherwise
     */
    public static function safe_unlink( $file ) {
        if ( file_exists( $file ) && is_file( $file ) ) {
            return @unlink( $file );
        }
        return false;
    }

    /**
     * Get next scheduled cron run time for a hook.
     *
     * @param string $hook Cron hook name
     * @return string Human-readable local WP time or "Not scheduled"
     */
    public static function next_cron_time( $hook ) {
        $timestamp = wp_next_scheduled( $hook );
        if ( ! $timestamp ) {
            return __('Not scheduled', 'easy-front-end-cache');
        }
        // Use WordPress local time settings
        return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
    }
}
