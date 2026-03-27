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
        // Register all options
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

    /** Render helpers **/
    public static function render_checkbox($args) {
        $option      = $args['option'];
        $value       = get_option($option);
        $description = $args['description'] ?? '';
        ?>
        <label class="efc-toggle">
            <input type="checkbox" name="<?php echo esc_attr($option); ?>" value="1" <?php checked(1, $value); ?> />
            <span class="efc-slider"></span>
        </label>
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_number($args) {
        $option      = $args['option'];
        $default     = $args['default'] ?? '';
        $value       = get_option($option, $default);
        $description = $args['description'] ?? '';
        ?>
        <input type="number" class="small-text"
               name="<?php echo esc_attr($option); ?>"
               value="<?php echo esc_attr($value); ?>"
               placeholder="<?php echo esc_attr($default); ?>" />
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_text($args) {
        $option      = $args['option'];
        $default     = $args['default'] ?? '';
        $value       = get_option($option, $default);
        $description = $args['description'] ?? '';
        ?>
        <input type="text" class="regular-text"
               name="<?php echo esc_attr($option); ?>"
               value="<?php echo esc_attr($value); ?>"
               placeholder="<?php echo esc_attr($default); ?>" />
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_textarea($args) {
        $option      = $args['option'];
        $value       = get_option($option, '');
        $description = $args['description'] ?? '';
        ?>
        <textarea name="<?php echo esc_attr($option); ?>" rows="5" cols="50" class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    public static function render_select($args) {
        $option      = $args['option'];
        $choices     = $args['choices'];
        $value       = get_option($option, 'daily');
        $description = $args['description'] ?? '';
        ?>
        <select name="<?php echo esc_attr($option); ?>" class="regular-select">
            <option value=""><?php esc_html_e('Choose frequency…','easy-front-end-cache'); ?></option>
            <?php foreach ($choices as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($description) echo '<p class="description">' . esc_html($description) . '</p>'; ?>
        <?php
    }

    /** Exclusion renderers **/
    public static function render_page_dropdown($args) {
        $option   = $args['option'];
        $selected = (array) get_option($option, []);
        $pages    = get_pages(['sort_column'=>'post_title','sort_order'=>'ASC']);
        echo '<select name="'.esc_attr($option).'[]" multiple class="efc-page-select">';
        foreach ($pages as $page) {
            $sel = in_array($page->ID,$selected) ? 'selected' : '';
            echo '<option value="'.esc_attr($page->ID).'" '.$sel.'>'.esc_html($page->post_title).'</option>';
        }
        echo '</select>';
        if (!empty($args['description'])) echo '<p class="description">'.esc_html($args['description']).'</p>';
    }

    public static function render_post_dropdown($args) {
        $option   = $args['option'];
        $selected = (array) get_option($option, []);
        $posts    = get_posts(['numberposts'=>-1,'post_type'=>'post']);
        echo '<select name="'.esc_attr($option).'[]" multiple class="efc-page-select">';
        foreach ($posts as $post) {
            $sel = in_array($post->ID,$selected) ? 'selected' : '';
            echo '<option value="'.esc_attr($post->ID).'" '.$sel.'>'.esc_html($post->post_title).'</option>';
        }
        echo '</select>';
        if (!empty($args['description'])) echo '<p class="description">'.esc_html($args['description']).'</p>';
    }

    public static function render_category_dropdown($args) {
        $option   = $args['option'];
        $selected = (array) get_option($option, []);
        $cats     = get_categories(['hide_empty'=>false]);
        echo '<select name="'.esc_attr($option).'[]" multiple class="efc-page-select">';
        foreach ($cats as $cat) {
            $sel = in_array($cat->term_id,$selected) ? 'selected' : '';
            echo '<option value="'.esc_attr($cat->term_id).'" '.$sel.'>'.esc_html($cat->name).'</option>';
        }
        echo '</select>';
        if (!empty($args['description'])) echo '<p class="description">'.esc_html($args['description']).'</p>';
    }

    public static function render_cpt_dropdown($args) {
        $option   = $args['option'];
        $selected = (array) get_option($option, []);
        $cpts     = get_post_types(['public'=>true,'_builtin'=>false],'objects');
        if (empty($cpts)) {
            echo '<p>'.esc_html__('No custom post types found.','easy-front-end-cache').'</p>';
            return;
        }
        echo '<select name="'.esc_attr($option).'[]" multiple class="efc-page-select">';
        foreach ($cpts as $cpt) {
            $sel = in_array($cpt->name,$selected) ? 'selected' : '';
            echo '<option value="'.esc_attr($cpt->name).'" '.$sel.'>'.esc_html($cpt->labels->singular_name).'</option>';
        }
        echo '</select>';
        if (!empty($args['description'])) echo '<p class="description">'.esc_html($args['description']).'</p>';
    }

    /** Settings page **/
    public static function render_settings_page() {
        $dir   = WP_CONTENT_DIR . '/efc-cache/';
        $size  = EFEC_Helpers::dir_size($dir);
        $count = EFEC_Helpers::dir_count($dir);
        ?>
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
                            <table class="form-table"><tbody>
                                <tr><th><?php esc_html_e('Enable Cache'); ?></th><td><?php self::render_checkbox(['option'=>'efc_enable_cache','description'=>__('Turn caching on or off.','easy-front-end-cache')]); ?></td></tr>
                                <tr>
                                    <th><?php esc_html_e('Minify HTML Output'); ?></th>
                                    <td><?php self::render_checkbox([
                                        'option'=>'efc_minify_html',
                                        'description'=>__('Compress whitespace in cached HTML output.','easy-front-end-cache')
                                        ]); ?></td>
                                    </tr>
                                    <tr><th><?php esc_html_e('Enable Debug Mode'); ?></th><td><?php self::render_checkbox(['option'=>'efc_debug_mode','description'=>__('Adds X-Easy-Cache headers.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Cache Lifetime (seconds)'); ?></th><td><?php self::render_number(['option'=>'efc_cache_time','default'=>600,'description'=>__('How long cached files remain valid.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Reset Param (single page)'); ?></th><td><?php self::render_text(['option'=>'efc_reset_param','default'=>'reset','description'=>__('Query string to clear cache for current page.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Reset All Param'); ?></th><td><?php self::render_text(['option'=>'efc_reset_all_param','default'=>'reset_all','description'=>__('Query string to clear all cache files.','easy-front-end-cache')]); ?></td></tr>
                                    <tr><th><?php esc_html_e('Allow Public Reset'); ?></th><td><?php self::render_checkbox(['option'=>'efc_allow_public_reset','description'=>__('Allow non-admin visitors to trigger reset.','easy-front-end-cache')]); ?></td></tr>
                                </tbody></table>
                            </div>
                    </div>

                    <!-- Purge Options Card -->
                    <div class="postbox efc-card">
                        <h2 class="hndle"><?php esc_html_e('Cache Purge Options', 'easy-front-end-cache'); ?></h2>
                        <div class="inside">
                            <table class="form-table"><tbody>
                                <tr><th><?php esc_html_e('Clear Cache on Post Update'); ?></th><td><?php self::render_checkbox(['option'=>'efec_purge_on_update','description'=>__('Automatically clear cache when posts are updated.','easy-front-end-cache')]); ?></td></tr>
                                <tr><th><?php esc_html_e('Clear Cache on Post Delete'); ?></th><td><?php self::render_checkbox(['option'=>'efec_purge_on_delete','description'=>__('Automatically clear cache when posts are deleted.','easy-front-end-cache')]); ?></td></tr>
                                <tr><th><?php esc_html_e('Clear Cache on Theme Switch'); ?></th><td><?php self::render_checkbox(['option'=>'efec_purge_on_theme_switch','description'=>__('Automatically clear cache when switching themes.','easy-front-end-cache')]); ?></td></tr>
                                <tr><th><?php esc_html_e('Scheduled Cleanup Frequency'); ?></th><td><?php self::render_select(['option'=>'efec_scheduled_cleanup','choices'=>[
                                    'daily'=>__('Daily','easy-front-end-cache'),
                                    'twicedaily'=>__('Twice Daily','easy-front-end-cache'),
                                    'weekly'=>__('Weekly','easy-front-end-cache')
                                ],'description'=>__('How often WP-Cron should clear all cache files.','easy-front-end-cache')]); ?></td></tr>
                            </tbody></table>
                        </div>
                    </div>
                </div><!-- end grid -->

                <!-- Exclusion Options Card -->
                <div class="postbox efc-card-full">
                    <h2 class="hndle"><?php esc_html_e('Cache Exclusions', 'easy-front-end-cache'); ?></h2>
                    <div class="inside">
                        <table class="form-table"><tbody>
                            <tr><th><?php esc_html_e('Excluded Pages'); ?></th><td><?php self::render_page_dropdown(['option'=>'efc_excluded_pages','description'=>__('Select pages to exclude from caching.','easy-front-end-cache')]); ?></td></tr>
                            <tr><th><?php esc_html_e('Excluded Posts'); ?></th><td><?php self::render_post_dropdown(['option'=>'efc_excluded_posts','description'=>__('Select posts to exclude from caching.','easy-front-end-cache')]); ?></td></tr>
                            <tr><th><?php esc_html_e('Excluded Categories'); ?></th><td><?php self::render_category_dropdown(['option'=>'efc_excluded_categories','description'=>__('Select categories to exclude from caching.','easy-front-end-cache')]); ?></td></tr>
                            <?php $cpts = get_post_types(['public'=>true,'_builtin'=>false],'objects'); if (!empty($cpts)) : ?>
                            <tr><th><?php esc_html_e('Excluded Custom Post Types'); ?></th><td><?php self::render_cpt_dropdown(['option'=>'efc_excluded_cpts','description'=>__('Select custom post types to exclude from caching.','easy-front-end-cache')]); ?></td></tr>
                            <?php endif; ?>
                            <tr><th><?php esc_html_e('Excluded Slugs'); ?></th><td><?php self::render_textarea(['option'=>'efc_excluded_slugs','description'=>__('Enter one slug per line (e.g., testing-featured-image-and-layout).','easy-front-end-cache')]); ?></td></tr>
                        </tbody></table>
                    </div>
                </div>

                <?php submit_button(); ?>
            </form>

            <!-- Cache Status Card -->
            <div class="postbox efc-card-full">
                <h2 class="hndle"><?php esc_html_e('Cache Status', 'easy-front-end-cache'); ?></h2>
                <div class="inside">
                    <p><?php esc_html_e('Directory:', 'easy-front-end-cache'); ?> <?php echo esc_html(WP_CONTENT_DIR . '/efc-cache/'); ?></p>
                    <p>
                        <span id="easy-front-end-cache_status">
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
                        <span class="efc-refresh-status"></span>
                        <span class="efc-clear-status"></span>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
}

