<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EFEC_Admin_Settings {
    use EFEC_Admin_Renderers; // make render_* methods available here too

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
        // Register all options once here
        $options = [
            'efc_enable_cache','efc_minify_html','efc_debug_mode','efc_cache_time',
            'efc_reset_param','efc_reset_all_param','efc_allow_public_reset',
            'efec_purge_on_update','efec_purge_on_delete','efec_purge_on_theme_switch',
            'efec_scheduled_cleanup',
            'efc_excluded_pages','efc_excluded_posts','efc_excluded_categories','efc_excluded_cpts',
            'efc_excluded_slugs'
        ];
        foreach ($options as $opt) {
            register_setting('easy-front-end-cache', $opt);
        }
    }

    public static function render_settings_page() {
        ?>
        <div class="wrap efc-settings-wrap">
            <h1><?php esc_html_e('Easy Front End Cache', 'easy-front-end-cache'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('easy-front-end-cache'); ?>

                <!-- Grid wrapper for first two cards -->
                <div class="efc-grid">
                    <?php EFEC_Admin_General::render(); ?>
                    <?php EFEC_Admin_Purge::render(); ?>
                </div>

                <?php EFEC_Admin_Exclusions::render(); ?>

                <?php submit_button(); ?>
            </form>

            <?php EFEC_Admin_Status::render(); ?>
        </div>
        <?php
    }
}