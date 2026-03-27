<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Helpers {

    /**
     * Safely delete a file
     */
    public static function safe_unlink( $file ) {
        if ( file_exists( $file ) && is_file( $file ) ) {
            @unlink( $file );
        }
    }

    /**
     * Get next scheduled cron time for a given event
     */
    public static function next_cron_time( $hook ) {
        $timestamp = wp_next_scheduled( $hook );
        if ( ! $timestamp ) {
            return __('Not scheduled', 'easy-front-end-cache');
        }
        return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timestamp );
    }

    /**
     * Calculate directory size
     */
    public static function dir_size( $dir ) {
        $size = 0;
        if ( is_dir( $dir ) ) {
            foreach ( glob( $dir . '/*' ) as $file ) {
                if ( is_file( $file ) ) {
                    $size += filesize( $file );
                }
            }
        }
        return $size;
    }

    /**
     * Count files in directory
     */
    public static function dir_count( $dir, $pattern = '*.html' ) {
        $count = 0;
        if ( is_dir( $dir ) ) {
            $files = glob( $dir . '/' . $pattern );
            if ( $files ) {
                $count = count( $files );
            }
        }
        return $count;
    }
}