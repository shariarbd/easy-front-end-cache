<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Admin {

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
        add_action( 'admin_bar_menu', [ __CLASS__, 'admin_bar_status' ], 100 );
        add_action( 'wp_ajax_efc_clear_cache_ajax', [ __CLASS__, 'handle_ajax_clear' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    public static function add_menu() {
        add_options_page(
            __('Easy Front End Cache', 'easy-front-end-cache'),
            __('Front End Cache', 'easy-front-end-cache'),
            'manage_options',
            'easy-front-end-cache',
            [ __CLASS__, 'render_settings_page' ]
        );
    }

    public static function register_settings() {
        // General cache options
        add_settings_section(
            'efec_general_section',
            __('General Cache Options', 'easy-front-end-cache'),
            '__return_false',
            'easy-front-end-cache'
        );

        register_setting('easy-front-end-cache', 'efc_enable_cache');
        add_settings_field('efc_enable_cache', __('Enable Cache', 'easy-front-end-cache'),
            [ __CLASS__, 'render_checkbox' ], 'easy-front-end-cache', 'efec_general_section',
            ['option' => 'efc_enable_cache', 'description' => __('Turn caching on or off for the front end.', 'easy-front-end-cache')]
        );

        register_setting('easy-front-end-cache', 'efc_minify_html');
        add_settings_field('efc_minify_html', __('Minify HTML Output', 'easy-front-end-cache'),
            [ __CLASS__, 'render_checkbox' ], 'easy-front-end-cache', 'efec_general_section',
            ['option' => 'efc_minify_html', 'description' => __('Compress whitespace in cached HTML files.', 'easy-front-end-cache')]
        );

        register_setting('easy-front-end-cache', 'efc_debug_mode');
        add_settings_field('efc_debug_mode', __('Enable Debug Mode', 'easy-front-end-cache'),
            [ __CLASS__, 'render_checkbox' ], 'easy-front-end-cache', 'efec_general_section',
            ['option' => 'efc_debug_mode', 'description' => __('Adds X-Easy-Cache headers (HIT/MISS) for debugging.', 'easy-front-end-cache')]
        );

        // Cache lifetime and reset options
        register_setting('easy-front-end-cache', 'efc_cache_time');
        add_settings_field('efc_cache_time', __('Cache Lifetime (seconds)', 'easy-front-end-cache'),
            [ __CLASS__, 'render_number' ], 'easy-front-end-cache', 'efec_general_section',
            ['option' => 'efc_cache_time', 'default' => 600, 'description' => __('How long cached files remain valid before refresh.', 'easy-front-end-cache')]
        );

        register_setting('easy-front-end-cache', 'efc_reset_param');
        add_settings_field('efc_reset_param', __('Reset Param (single page)', 'easy-front-end-cache'),
            [ __CLASS__, 'render_text' ], 'easy-front-end-cache', 'efec_general_section',
            ['option' => 'efc_reset_param', 'default' => 'reset', 'description' => __('Query string to clear cache for the current page (e.g., ?reset=1).', 'easy-front-end-cache')]
        );

        register_setting('easy-front-end-cache', 'efc_reset_all_param');
        add_settings_field('efc_reset_all_param', __('Reset All Param', 'easy-front-end-cache'),
            [ __CLASS__, 'render_text' ], 'easy-front-end-cache', 'efec_general_section',
            ['option' => 'efc_reset_all_param', 'default' => 'reset_all', 'description' => __('Query string to clear all cache files (e.g., ?reset_all=1).', 'easy-front-end-cache')]
        );

        register_setting('easy-front-end-cache', 'efc_allow_public_reset');
        add_settings_field('efc_allow_public_reset', __('Allow Public Reset', 'easy-front-end-cache'),
            [ __CLASS__, 'render_checkbox' ], 'easy-front-end-cache', 'efec_general_section',
            ['option' => 'efc_allow_public_reset', 'description' => __('Allow non-admin visitors to trigger cache reset via query string.', 'easy-front-end-cache')]
        );

        // Purge options
        add_settings_section(
            'efec_purge_section',
            __('Cache Purge Options', 'easy-front-end-cache'),
            '__return_false',
            'easy-front-end-cache'
        );

        register_setting('easy-front-end-cache', 'efec_purge_on_update');
        add_settings_field('efec_purge_on_update', __('Clear Cache on Post Update', 'easy-front-end-cache'),
            [ __CLASS__, 'render_checkbox' ], 'easy-front-end-cache', 'efec_purge_section',
            ['option' => 'efec_purge_on_update', 'description' => __('Automatically clear cache when posts are updated.', 'easy-front-end-cache')]
        );

        register_setting('easy-front-end-cache', 'efec_purge_on_delete');
        add_settings_field('efec_purge_on_delete', __('Clear Cache on Post Delete', 'easy-front-end-cache'),
            [ __CLASS__, 'render_checkbox' ], 'easy-front-end-cache', 'efec_purge_section',
            ['option' => 'efec_purge_on_delete', 'description' => __('Automatically clear cache when posts are deleted.', 'easy-front-end-cache')]
        );

        register_setting('easy-front-end-cache', 'efec_purge_on_theme_switch');
        add_settings_field('efec_purge_on_theme_switch', __('Clear Cache on Theme Switch', 'easy-front-end-cache'),
            [ __CLASS__, 'render_checkbox' ], 'easy-front-end-cache', 'efec_purge_section',
            ['option' => 'efec_purge_on_theme_switch', 'description' => __('Automatically clear cache when switching themes.', 'easy-front-end-cache')]
        );

        register_setting('easy-front-end-cache', 'efec_scheduled_cleanup');
        add_settings_field('efec_scheduled_cleanup', __('Scheduled Cleanup Frequency', 'easy-front-end-cache'),
            [ __CLASS__, 'render_select' ], 'easy-front-end-cache', 'efec_purge_section',
            ['option' => 'efec_scheduled_cleanup', 'choices' => [
                'daily'      => __('Daily', 'easy-front-end-cache'),
                'twicedaily' => __('Twice Daily', 'easy-front-end-cache'),
                'weekly'     => __('Weekly', 'easy-front-end-cache')
            ], 'description' => __('How often WP-Cron should clear all cache files.', 'easy-front-end-cache')]
        );
    }

    /**
     * Render helpers with descriptions
     */
    public static function render_checkbox($args) {
        $option = $args['option'];
        $value = get_option($option);
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <label>
            <input type="checkbox" name="<?php echo esc_attr($option); ?>" value="1" <?php checked(1, $value); ?> />
            <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        </label>
        <?php
    }

    public static function render_number($args) {
        $option = $args['option'];
        $default = isset($args['default']) ? $args['default'] : '';
        $value = get_option($option, $default);
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <input type="number" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_attr($value); ?>" />
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_text($args) {
        $option = $args['option'];
        $default = isset($args['default']) ? $args['default'] : '';
        $value = get_option($option, $default);
        $description = isset($args['description']) ? $args['description'] : '';
        ?>
        <input type="text" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_attr($value); ?>" />
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

        public static function render_select($args) {
        $option = $args['option'];
        $choices = $args['choices'];
        $value = get_option($option, 'daily');
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

    /**
     * Admin bar status nodes
     */
    public static function admin_bar_status($wp_admin_bar) {
        if ( ! current_user_can('manage_options') ) {
            return;
        }

        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size($dir);
        $count = EFEC_Helpers::dir_count($dir);

        $wp_admin_bar->add_node([
            'id'    => 'efc-status',
            'title' => '⚡ Easy Cache',
        ]);

        $wp_admin_bar->add_node([
            'id'     => 'efc-size',
            'parent' => 'efc-status',
            'title'  => __('Size: ', 'easy-front-end-cache') . size_format($size),
        ]);

        $wp_admin_bar->add_node([
            'id'     => 'efc-files',
            'parent' => 'efc-status',
            'title'  => __('Files: ', 'easy-front-end-cache') . intval($count),
        ]);

        $wp_admin_bar->add_node([
            'id'     => 'efc-clear',
            'parent' => 'efc-status',
            'title'  => __('🧹 Clean All', 'easy-front-end-cache'),
            'href'   => '#',
            'meta'   => [
                'class' => 'efc-clear-cache-link'
            ]
        ]);
    }

    /**
     * Handle AJAX cache clear
     */
    public static function handle_ajax_clear() {
        if ( ! current_user_can('manage_options') ) {
            wp_send_json_error( [ 'message' => __('Permission denied.', 'easy-front-end-cache') ] );
        }

        EFEC_Cache::purge_all();

        wp_send_json_success( [ 'message' => __('✅ Cache cleared successfully.', 'easy-front-end-cache') ] );
    }

    /**
     * Enqueue admin JS for AJAX clearing
     */
    public static function enqueue_assets() {
        wp_enqueue_script(
            'efc-admin-js',
            EFEC_URL . 'assets/js/admin.js',
            [ 'jquery' ],
            EFEC_VERSION,
            true
        );
        wp_enqueue_style(
            'efc-admin-css',
            EFEC_URL . 'assets/css/admin.css',
            [],
            EFEC_VERSION
        );
    }

    /**
     * Render settings page
     */
    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Easy Front End Cache', 'easy-front-end-cache'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('easy-front-end-cache');
                do_settings_sections('easy-front-end-cache');
                submit_button();
                ?>
            </form>

            <h2><?php esc_html_e('Cache Status', 'easy-front-end-cache'); ?></h2>
            <p><?php esc_html_e('Directory:', 'easy-front-end-cache'); ?> <?php echo esc_html(WP_CONTENT_DIR . '/efc-cache/'); ?></p>
            <p><?php esc_html_e('Next Scheduled Cleanup:', 'easy-front-end-cache'); ?> <?php echo esc_html(EFEC_Helpers::next_cron_time('efec_scheduled_cleanup_event')); ?></p>
            <button class="button efc-clear-cache-btn"><?php esc_html_e('🧹 Clean All Cache Now', 'easy-front-end-cache'); ?></button>
            <span class="efc-clear-status"></span>
        </div>
        <?php
    }
}