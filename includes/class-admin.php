<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin logic for Easy Front End Cache
 * ------------------------------------
 * This class handles:
 * - Settings page (options, UI, status display)
 * - Admin bar menu for quick cache clearing
 * - AJAX handlers for clearing cache, rescheduling, and running cleanup
 * - Enqueuing admin scripts and styles
 */
class EFEC_Admin {

    /**
     * Initialize admin hooks
     */
    public static function init() {
        // Add settings page
        add_action( 'admin_menu', [ __CLASS__, 'add_settings_page' ] );

        // Register settings
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );

        // Add admin bar menu
        add_action( 'admin_bar_menu', [ __CLASS__, 'add_admin_bar_menu' ], 100 );

        // Enqueue admin scripts/styles
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );

        // AJAX handlers
        add_action( 'wp_ajax_efc_clear_cache_ajax', [ __CLASS__, 'handle_ajax_clear' ] );
        add_action( 'wp_ajax_efc_reschedule_ajax', [ __CLASS__, 'handle_ajax_reschedule' ] );
        add_action( 'wp_ajax_efc_run_cleanup_ajax', [ __CLASS__, 'handle_ajax_run_cleanup' ] );
    }

    /**
     * Add settings page under "Settings"
     */
    public static function add_settings_page() {
        add_options_page(
            __('Front End Cache', 'easy-front-end-cache'),
            __('Front End Cache', 'easy-front-end-cache'),
            'manage_options',
            'easy-front-end-cache',
            [ __CLASS__, 'render_settings_page' ]
        );
    }

    /**
     * Register plugin settings
     */
    public static function register_settings() {
        register_setting( 'efec_settings', 'efec_cache_time_posts' );
        register_setting( 'efec_settings', 'efec_cache_time_pages' );
        register_setting( 'efec_settings', 'efec_purge_on_update' );
        register_setting( 'efec_settings', 'efec_purge_on_delete' );
        register_setting( 'efec_settings', 'efec_purge_on_theme_switch' );
        register_setting( 'efec_settings', 'efec_enable_cron_cleanup' );
        register_setting( 'efec_settings', 'efec_scheduled_cleanup' );
    }

    /**
     * Render settings page UI
     */
    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Easy Front End Cache Settings', 'easy-front-end-cache'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'efec_settings' ); ?>
                <?php do_settings_sections( 'efec_settings' ); ?>

                <h2><?php esc_html_e('Cache Lifetime', 'easy-front-end-cache'); ?></h2>
                <p>
                    <label><?php esc_html_e('Posts (seconds):', 'easy-front-end-cache'); ?></label>
                    <input type="number" name="efec_cache_time_posts" value="<?php echo esc_attr(get_option('efec_cache_time_posts', 3600)); ?>">
                </p>
                <p>
                    <label><?php esc_html_e('Pages (seconds):', 'easy-front-end-cache'); ?></label>
                    <input type="number" name="efec_cache_time_pages" value="<?php echo esc_attr(get_option('efec_cache_time_pages', 3600)); ?>">
                </p>

                <h2><?php esc_html_e('Purge Options', 'easy-front-end-cache'); ?></h2>
                <p><label><input type="checkbox" name="efec_purge_on_update" value="1" <?php checked(get_option('efec_purge_on_update')); ?>> <?php esc_html_e('Purge on post update', 'easy-front-end-cache'); ?></label></p>
                <p><label><input type="checkbox" name="efec_purge_on_delete" value="1" <?php checked(get_option('efec_purge_on_delete')); ?>> <?php esc_html_e('Purge on post delete', 'easy-front-end-cache'); ?></label></p>
                <p><label><input type="checkbox" name="efec_purge_on_theme_switch" value="1" <?php checked(get_option('efec_purge_on_theme_switch')); ?>> <?php esc_html_e('Purge on theme switch', 'easy-front-end-cache'); ?></label></p>

                <h2><?php esc_html_e('Scheduled Cleanup', 'easy-front-end-cache'); ?></h2>
                <p><label><input type="checkbox" name="efec_enable_cron_cleanup" value="1" <?php checked(get_option('efec_enable_cron_cleanup')); ?>> <?php esc_html_e('Enable scheduled cleanup', 'easy-front-end-cache'); ?></label></p>
                <p>
                    <label><?php esc_html_e('Frequency:', 'easy-front-end-cache'); ?></label>
                    <select name="efec_scheduled_cleanup">
                        <option value="hourly" <?php selected(get_option('efec_scheduled_cleanup'), 'hourly'); ?>><?php esc_html_e('Hourly', 'easy-front-end-cache'); ?></option>
                        <option value="twicedaily" <?php selected(get_option('efec_scheduled_cleanup'), 'twicedaily'); ?>><?php esc_html_e('Twice Daily', 'easy-front-end-cache'); ?></option>
                        <option value="daily" <?php selected(get_option('efec_scheduled_cleanup'), 'daily'); ?>><?php esc_html_e('Daily', 'easy-front-end-cache'); ?></option>
                        <option value="weekly" <?php selected(get_option('efec_scheduled_cleanup'), 'weekly'); ?>><?php esc_html_e('Weekly', 'easy-front-end-cache'); ?></option>
                    </select>
                </p>

                <?php submit_button(); ?>
            </form>

            <h2><?php esc_html_e('Cache Status', 'easy-front-end-cache'); ?></h2>
            <div id="easy-front-end-cache_status">
                <?php
                $dir   = WP_CONTENT_DIR . '/efc-cache/';
                $size  = EFEC_Helpers::dir_size($dir);
                $count = EFEC_Helpers::dir_count($dir);
                echo '<strong style="color: rgb(0, 115, 170);">Cache Folder Size :</strong> ' . size_format($size) . '<br>';
                echo '<strong style="color: rgb(0, 115, 170);">Total Cached Files:</strong> ' . intval($count);
                ?>
            </div>

            <p>
                <?php esc_html_e('Last Cleared:', 'easy-front-end-cache'); ?>
                <?php 
                $last = get_option('efec_last_cleared');
                if ( $last ) {
                    echo esc_html( date_i18n( get_option('date_format') . ' ' . get_option('time_format'), $last ) );
                } else {
                    esc_html_e('Never cleared yet.', 'easy-front-end-cache');
                }
                ?>
            </p>

            <p>
                <?php esc_html_e('Next Scheduled Cleanup:', 'easy-front-end-cache'); ?>
                <?php echo esc_html(EFEC_Helpers::next_cron_time('efec_scheduled_cleanup_event')); ?>
                <button class="button efc-reschedule-btn"><?php esc_html_e('🔄 Reschedule Now', 'easy-front-end-cache'); ?></button>
                <button class="button efc-run-cleanup-btn"><?php esc_html_e('⚡ Run Cleanup Now', 'easy-front-end-cache'); ?></button>
                <span class="efc-reschedule-status"></span>
            </p>

            <p><button class="button button-primary efc-clear-cache-btn"><?php esc_html_e('Clear Cache Now', 'easy-front-end-cache'); ?></button></p>
        </div>
        <?php
    }

    /**
     * Add admin bar menu for quick cache clearing
     */
    public static function add_admin_bar_menu( $wp_admin_bar ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $wp_admin_bar->add_node( [
            'id'    => 'efc-clear-cache',
            'title' => __('Clear Cache', 'easy-front-end-cache'),
            'href'  => '#',
            'meta'  => [ 'class' => 'efc-clear-cache-link' ]
        ] );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public static function enqueue_assets() {
        wp_enqueue_script( 'efc-admin-js', EFEC_URL . 'assets/js/admin.js', ['jquery'], EFEC_VERSION, true );
        wp_enqueue_style( 'efc-admin-css', EFEC_URL . 'assets/css/admin.css', [], EFEC_VERSION );
    }


    /**
     * AJAX: Clear cache
     *
     * Triggered when admin clicks "Clear Cache Now" button
     * or uses the admin bar menu. Purges all cache files,
     * updates stats, and returns JSON response for live UI update.
     */
    public static function handle_ajax_clear() {
        if ( ! current_user_can('manage_options') ) {
            wp_send_json_error( [ 'message' => __('Permission denied.', 'easy-front-end-cache') ] );
        }

        // Purge all cache files
        EFEC_Purge::purge_all();

        // Calculate updated stats
        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size($dir);
        $count = EFEC_Helpers::dir_count($dir);

        // Save last cleared timestamp
        update_option( 'efec_last_cleared', current_time( 'timestamp' ) );

        // Return success response with stats + timestamp
        wp_send_json_success( [
            'message'     => __('✅ Cache cleared successfully.', 'easy-front-end-cache'),
            'size'        => size_format($size),
            'count'       => intval($count),
            'lastCleared' => date_i18n( get_option('date_format') . ' ' . get_option('time_format'), current_time('timestamp') ),
        ] );
    }

    /**
     * AJAX: Reschedule cron cleanup
     *
     * Triggered when admin clicks "Reschedule Now".
     * Clears existing cron job and schedules a new one
     * based on current settings. Returns next run time.
     */
    public static function handle_ajax_reschedule() {
        if ( ! current_user_can('manage_options') ) {
            wp_send_json_error( [ 'message' => __('Permission denied.', 'easy-front-end-cache') ] );
        }

        // Clear existing schedule
        wp_clear_scheduled_hook( 'efec_scheduled_cleanup_event' );

        // Reschedule if enabled
        if ( get_option( 'efec_enable_cron_cleanup' ) ) {
            $frequency = get_option( 'efec_scheduled_cleanup', 'daily' );
            wp_schedule_event( time(), $frequency, 'efec_scheduled_cleanup_event' );
        }

        $next = EFEC_Helpers::next_cron_time('efec_scheduled_cleanup_event');

        wp_send_json_success( [
            'message' => __('✅ Cleanup rescheduled successfully.', 'easy-front-end-cache'),
            'next'    => $next,
        ] );
    }

    /**
     * AJAX: Run cleanup immediately
     *
     * Triggered when admin clicks "Run Cleanup Now".
     * Purges all cache files instantly, updates stats,
     * and returns JSON response for live UI update.
     */
    public static function handle_ajax_run_cleanup() {
        if ( ! current_user_can('manage_options') ) {
            wp_send_json_error( [ 'message' => __('Permission denied.', 'easy-front-end-cache') ] );
        }

        // Run cleanup immediately
        EFEC_Purge::purge_all();

        // Calculate updated stats
        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size($dir);
        $count = EFEC_Helpers::dir_count($dir);

        // Update last cleared timestamp
        update_option( 'efec_last_cleared', current_time( 'timestamp' ) );

        // Return success response with stats + timestamp
        wp_send_json_success( [
            'message'     => __('✅ Cleanup run successfully.', 'easy-front-end-cache'),
            'size'        => size_format($size),
            'count'       => intval($count),
            'lastCleared' => date_i18n( get_option('date_format') . ' ' . get_option('time_format'), current_time('timestamp') ),
        ] );
    }
}
