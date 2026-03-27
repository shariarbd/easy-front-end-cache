<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
 
class EFEC_Admin_Purge {
    use EFEC_Admin_Renderers;

    public static function render() {
        ?>
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
        <?php
    }
}