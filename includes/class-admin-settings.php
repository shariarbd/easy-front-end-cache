<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Admin_Settings {

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function add_menu() {
        add_options_page(
            __('Easy Front End Cache', 'easy-front-end-cache'),
            __('Easy Front End Cache', 'easy-front-end-cache'),
            'manage_options',
            'easy-front-end-cache',
            [ __CLASS__, 'render_settings_page' ]
        );
    }

    public static function register_settings() {
        // Register all options (same as before)
        register_setting('easy-front-end-cache', 'efc_enable_cache');
        register_setting('easy-front-end-cache', 'efc_minify_html');
        register_setting('easy-front-end-cache', 'efc_debug_mode');
        register_setting('easy-front-end-cache', 'efc_cache_time');
        register_setting('easy-front-end-cache', 'efc_reset_param');
        register_setting('easy-front-end-cache', 'efc_reset_all_param');
        register_setting('easy-front-end-cache', 'efc_allow_public_reset');
        register_setting('easy-front-end-cache', 'efec_purge_on_update');
        register_setting('easy-front-end-cache', 'efec_purge_on_delete');
        register_setting('easy-front-end-cache', 'efec_purge_on_theme_switch');
        register_setting('easy-front-end-cache', 'efec_scheduled_cleanup');
    }

    /** Render helpers **/
    public static function render_checkbox($args) {
        $option      = $args['option'];
        $value       = get_option($option);
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr($option); ?>" value="1" <?php checked(1, $value); ?> />
            <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        </label>
        <?php
    }

    public static function render_number($args) {
        $option      = $args['option'];
        $default     = isset($args['default']) ? $args['default'] : '';
        $value       = get_option($option, $default);
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <input type="number" class="small-text" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_attr($value); ?>" />
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_text($args) {
        $option      = $args['option'];
        $default     = isset($args['default']) ? $args['default'] : '';
        $value       = get_option($option, $default);
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <input type="text" class="regular-text" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_attr($value); ?>" />
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_select($args) {
        $option      = $args['option'];
        $choices     = $args['choices'];
        $value       = get_option($option, 'daily');
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <select name="<?php echo esc_attr($option); ?>">
            <?php foreach ($choices as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    /** Settings page **/
    public static function render_settings_page() {
        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size($dir);
        $count = EFEC_Helpers::dir_count($dir);
        ?>
        <style>
            .efc-settings-wrap .postbox {
                margin-top: 20px;
            }
            .efc-settings-wrap .hndle {
                font-size: 1.5em;
                font-weight: 600;
                padding-left: 15px;
            }
            .efc-label {
                color: #0073aa;
                font-weight: bold;
            }
            .efc-grid {
                display: flex;
                gap: 20px;
                margin-bottom: 20px;
            }
            .efc-card {
                flex: 1;
                min-width: 300px;
            }
            .efc-settings-wrap .postbox {
                margin-top: 20px;
            }
            .efc-settings-wrap .hndle {
                font-size: 1.1em;
                font-weight: 600;
            }
            .efc-label {
                color: #0073aa;
                font-weight: bold;
            }
        </style>
        <div class="wrap efc-settings-wrap">
            <h1><?php esc_html_e('Easy Front End Cache', 'easy-front-end-cache'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('easy-front-end-cache'); ?>

                <!-- Grid wrapper for first two cards -->
                <div class="efc-grid">
                    <!-- General Options Card -->
                    <div class="postbox efc-card">
                        <h2 class="hndle"><?php esc_html_e('General Cache Options', 'easy-front-end-cache'); ?></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tbody>
                                    <tr><th><?php esc_html_e('Enable Cache'); ?></th><td><?php self::render_checkbox(['option'=>'efc_enable_cache','description'=>__('Turn caching on or off.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Minify HTML Output'); ?></th><td><?php self::render_checkbox(['option'=>'efc_minify_html','description'=>__('Compress whitespace in cached HTML.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Enable Debug Mode'); ?></th><td><?php self::render_checkbox(['option'=>'efc_debug_mode','description'=>__('Adds X-Easy-Cache headers.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Cache Lifetime (seconds)'); ?></th><td><?php self::render_number(['option'=>'efc_cache_time','default'=>600,'description'=>__('How long cached files remain valid.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Reset Param (single page)'); ?></th><td><?php self::render_text(['option'=>'efc_reset_param','default'=>'reset','description'=>__('Query string to clear cache for current page.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Reset All Param'); ?></th><td><?php self::render_text(['option'=>'efc_reset_all_param','default'=>'reset_all','description'=>__('Query string to clear all cache files.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Allow Public Reset'); ?></th><td><?php self::render_checkbox(['option'=>'efc_allow_public_reset','description'=>__('Allow non-admin visitors to trigger reset.','easy-front-end-cache')]); ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Purge Options Card -->
                    <div class="postbox efc-card">
                        <h2 class="hndle"><?php esc_html_e('Cache Purge Options', 'easy-front-end-cache'); ?></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tbody>
                                    <tr><th><?php esc_html_e('Clear Cache on Post Update'); ?></th><td><?php self::render_checkbox(['option'=>'efec_purge_on_update','description'=>__('Automatically clear cache when posts are updated.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Clear Cache on Post Delete'); ?></th><td><?php self::render_checkbox(['option'=>'efec_purge_on_delete','description'=>__('Automatically clear cache when posts are deleted.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Clear Cache on Theme Switch'); ?></th><td><?php self::render_checkbox(['option'=>'efec_purge_on_theme_switch','description'=>__('Automatically clear cache when switching themes.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Scheduled Cleanup Frequency'); ?></th><td><?php self::render_select(['option'=>'efec_scheduled_cleanup','choices'=>[
                                        'daily'=>__('Daily','easy-front-end-cache'),
                                        'twicedaily'=>__('Twice Daily','easy-front-end-cache'),
                                        'weekly'=>__('Weekly','easy-front-end-cache')
                                    ],'description'=>__('How often WP-Cron should clear all cache files.','easy-front-end-cache')]); ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- end grid -->

                <?php submit_button(); ?>
            </form>

            <!-- Cache Status Card -->
            <div class="postbox">
                <h2 class="hndle"><?php esc_html_e('Cache Status', 'easy-front-end-cache'); ?></h2>
                <div class="inside">
                    <p>
                        <?php esc_html_e('Directory:', 'easy-front-end-cache'); ?>
                        <?php echo esc_html(WP_CONTENT_DIR . '/efc-cache/'); ?>
                    </p>
                    <p>
                        <span id="easy-front-end-cache_stattus">
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
        </div>

        <?php
    }
}
