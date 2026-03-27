<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class EFEC_Admin_Exclusions {
    use EFEC_Admin_Renderers;

    public static function render() {
        ?>
        <div class="postbox efc-card-full">
            <h2 class="hndle"><?php esc_html_e('Cache Exclusions', 'easy-front-end-cache'); ?></h2>
            <div class="inside">
                <table class="form-table"><tbody>
                    <tr>
                        <th><?php esc_html_e('Excluded Pages'); ?></th>
                        <td>
                            <?php self::render_page_dropdown([
                                'option'=>'efc_excluded_pages',
                                'description'=>__('Select pages to exclude from caching.','easy-front-end-cache')
                            ]); ?>
                            <button type="button" class="button efc-reset-select" data-target="efc_excluded_pages">
                                <?php esc_html_e('Reset', 'easy-front-end-cache'); ?>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Excluded Posts'); ?></th>
                        <td>
                            <?php self::render_post_dropdown([
                                'option'=>'efc_excluded_posts',
                                'description'=>__('Select posts to exclude from caching.','easy-front-end-cache')
                            ]); ?>
                            <button type="button" class="button efc-reset-select" data-target="efc_excluded_posts">
                                <?php esc_html_e('Reset', 'easy-front-end-cache'); ?>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('Excluded Categories'); ?></th>
                        <td>
                            <?php self::render_category_dropdown([
                                'option'=>'efc_excluded_categories',
                                'description'=>__('Select categories to exclude from caching.','easy-front-end-cache')
                            ]); ?>
                            <button type="button" class="button efc-reset-select" data-target="efc_excluded_categories">
                                <?php esc_html_e('Reset', 'easy-front-end-cache'); ?>
                            </button>
                        </td>
                    </tr>
                    <?php $cpts = get_post_types(['public'=>true,'_builtin'=>false],'objects'); if (!empty($cpts)) : ?>
                    <tr>
                        <th><?php esc_html_e('Excluded Custom Post Types'); ?></th>
                        <td>
                            <?php self::render_cpt_dropdown([
                                'option'=>'efc_excluded_cpts',
                                'description'=>__('Select custom post types to exclude from caching.','easy-front-end-cache')
                            ]); ?>
                            <button type="button" class="button efc-reset-select" data-target="efc_excluded_cpts">
                                <?php esc_html_e('Reset', 'easy-front-end-cache'); ?>
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php esc_html_e('Excluded Slugs'); ?></th>
                        <td>
                            <?php self::render_textarea([
                                'option'=>'efc_excluded_slugs',
                                'description'=>__('Enter one slug per line (e.g., testing-featured-image-and-layout).','easy-front-end-cache')
                            ]); ?>
                            <button type="button" class="button efc-reset-textarea" data-target="efc_excluded_slugs">
                                <?php esc_html_e('Reset', 'easy-front-end-cache'); ?>
                            </button>
                        </td>
                    </tr>
                </tbody></table>
            </div>
        </div>
        <?php
    }
}