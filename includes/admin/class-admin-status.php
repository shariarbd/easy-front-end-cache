<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EFEC_Admin_Status {
    use EFEC_Admin_Renderers;
    public static function render() {
        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size($dir);
        $count = EFEC_Helpers::dir_count($dir);
        ?>
        <div class="postbox efc-card-full">
            <h2 class="hndle"><?php esc_html_e('Cache Status', 'easy-front-end-cache'); ?></h2>
            <div class="inside">
                <p><?php esc_html_e('Directory:', 'easy-front-end-cache'); ?> <?php echo esc_html($dir); ?></p>
                <p>
                    <span id="easy_front_end_cache_status">
                        <strong class="efc-label"><?php esc_html_e('Cache Folder Size:', 'easy-front-end-cache'); ?></strong>
                        <?php echo size_format($size); ?><br>
                        <strong class="efc-label"><?php esc_html_e('Total Cached Files:', 'easy-front-end-cache'); ?></strong>
                        <?php echo intval($count); ?>
                    </span>
                </p>
                <p>
                    <strong class="efc-label"><?php esc_html_e('Next Scheduled Cleanup:', 'easy-front-end-cache'); ?></strong>
                    <?php echo esc_html(EFEC_Helpers::next_cron_time('efec_scheduled_cleanup_event')); ?>
                </p>
                <p>
                    <button class="button button-primary efc-clear-cache-btn">
                        <?php esc_html_e('🧹 Clean All Cache Now', 'easy-front-end-cache'); ?>
                    </button>
                    <button class="button efc-refresh-stats-btn">
                        <?php esc_html_e('🔄 Refresh Stats', 'easy-front-end-cache'); ?>
                    </button>
                    <span class="efc-refresh-status"></span>
                    <span class="efc-clear-status"></span>
                </p>
            </div>
        </div>
        <?php
    }
}