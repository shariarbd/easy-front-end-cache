<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EFEC_Admin_General {
    use EFEC_Admin_Renderers; // <-- this line makes all render_* methods available

    public static function render() {
        ?>
        <div class="postbox efc-card">
            <h2 class="hndle"><?php esc_html_e('General Cache Options', 'easy-front-end-cache'); ?></h2>
            <div class="inside">
                <table class="form-table"><tbody>
                    <tr>
                        <th><?php esc_html_e('Enable Cache'); ?></th>
                        <td><?php self::render_checkbox([
                            'option'=>'efc_enable_cache',
                            'description'=>__('Turn caching on or off.','easy-front-end-cache')
                        ]); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Minify HTML Output'); ?></th>
                        <td><?php self::render_checkbox([
                            'option'=>'efc_minify_html',
                            'description'=>__('Compress whitespace in cached HTML output.','easy-front-end-cache')
                        ]); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Enable Debug Mode'); ?></th>
                        <td><?php self::render_checkbox([
                            'option'=>'efc_debug_mode',
                            'description'=>__('Adds X-Easy-Cache headers (HIT, MISS, BYPASS) for debugging.','easy-front-end-cache')
                        ]); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Cache Lifetime (seconds)'); ?></th>
                        <td><?php self::render_number([
                            'option'=>'efc_cache_time',
                            'default'=>600,
                            'description'=>__('How long cached files remain valid before regeneration.','easy-front-end-cache')
                        ]); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Reset Param (single page)'); ?></th>
                        <td><?php self::render_text([
                            'option'=>'efc_reset_param',
                            'default'=>'reset',
                            'description'=>__('Query string to clear cache for current page (e.g., ?reset=1).','easy-front-end-cache')
                        ]); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Reset All Param'); ?></th>
                        <td><?php self::render_text([
                            'option'=>'efc_reset_all_param',
                            'default'=>'reset_all',
                            'description'=>__('Query string to clear all cache files (e.g., ?reset_all=1).','easy-front-end-cache')
                        ]); ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Allow Public Reset'); ?></th>
                        <td><?php self::render_checkbox([
                            'option'=>'efc_allow_public_reset',
                            'description'=>__('Allow non-admin visitors to trigger cache reset.','easy-front-end-cache')
                        ]); ?></td>
                    </tr>
                </tbody></table>
            </div>
        </div>
        <?php
    }
}