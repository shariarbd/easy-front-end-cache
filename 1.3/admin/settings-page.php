<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wrap">
    <h1><?php esc_html_e('Easy Front End Cache Settings', 'easy-front-end-cache'); ?></h1>
    <form method="post" action="options.php">
        <?php settings_fields('efc_settings_group'); ?>
        <?php do_settings_sections('efc_settings_group'); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Cache Time (seconds)', 'easy-front-end-cache'); ?></th>
                <td>
                    <input type="number" name="efc_cache_time" value="<?php echo esc_attr(get_option('efc_cache_time', 600)); ?>" />
                    <p class="description"><?php esc_html_e('How long each cached page should remain valid before being regenerated.', 'easy-front-end-cache'); ?></p>
                    <p class="description"><em><?php esc_html_e('Note: Admin pages, logged-in users, previews, search results, reset URLs, and query-string requests are never cached.', 'easy-front-end-cache'); ?></em></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Reset Single Param', 'easy-front-end-cache'); ?></th>
                <td>
                    <input type="text" name="efc_reset_param" value="<?php echo esc_attr(get_option('efc_reset_param', 'reset')); ?>" />
                    <p class="description"><?php esc_html_e('Query parameter to clear cache for the current page. Example:', 'easy-front-end-cache'); ?> <code>?reset=1</code></p>
                    <p class="description"><em><?php esc_html_e('Note: Requests with this parameter are excluded from caching automatically.', 'easy-front-end-cache'); ?></em></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Reset All Param', 'easy-front-end-cache'); ?></th>
                <td>
                    <input type="text" name="efc_reset_all_param" value="<?php echo esc_attr(get_option('efc_reset_all_param', 'reset_all')); ?>" />
                    <p class="description"><?php esc_html_e('Query parameter to clear all cached pages. Example:', 'easy-front-end-cache'); ?> <code>?reset_all=1</code></p>
                    <p class="description"><em><?php esc_html_e('Note: Requests with this parameter are excluded from caching automatically.', 'easy-front-end-cache'); ?></em></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Redirect Entire Site', 'easy-front-end-cache'); ?></th>
                <td>
                    <input type="url" name="efc_redirect_url" value="<?php echo esc_attr(get_option('efc_redirect_url', '')); ?>" placeholder="https://example.com" />
                    <p class="description"><?php esc_html_e('If set, all front-end requests will redirect to this URL. Leave empty to disable.', 'easy-front-end-cache'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Allow Public Reset via URL', 'easy-front-end-cache'); ?></th>
                <td>
                    <input type="checkbox" name="efc_allow_public_reset" value="1" <?php checked(1, get_option('efc_allow_public_reset', 0)); ?> />
                    <p class="description">
                        <?php esc_html_e('If enabled, anyone can clear cache using query parameters (e.g., ?reset=1).', 'easy-front-end-cache'); ?><br>
                        <?php esc_html_e('If disabled, only logged-in admins can use reset parameters.', 'easy-front-end-cache'); ?><br>
                        <?php esc_html_e('Manual reset button in admin always works.', 'easy-front-end-cache'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>

    <h2><?php esc_html_e('Cache Management', 'easy-front-end-cache'); ?></h2>
    <?php
    $cache_dir = WP_CONTENT_DIR . '/efc-cache/';
    $size = 0;
    $count = 0;
    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . '*.html');
        if ($files) {
            foreach ($files as $file) {
                $size += filesize($file);
            }
            $count = count($files);
        }
    }
    ?>
    <p><strong><?php esc_html_e('Cache Folder Size:', 'easy-front-end-cache'); ?></strong> <?php echo size_format($size); ?></p>
    <p><strong><?php esc_html_e('Total Cached Files:', 'easy-front-end-cache'); ?></strong> <?php echo intval($count); ?></p>

    <form method="post">
        <?php wp_nonce_field('efc_clear_cache_action', 'efc_clear_cache_nonce'); ?>
        <input type="submit" name="efc_clear_cache" class="button button-secondary" value="<?php esc_attr_e('Clear All Cache', 'easy-front-end-cache'); ?>" />
    </form>

    <h2>📖 <?php esc_html_e('Documentation', 'easy-front-end-cache'); ?></h2>
    <div style="background:#fff;border:1px solid #ccd0d4;padding:15px;">
        <h3><?php esc_html_e('How It Works', 'easy-front-end-cache'); ?></h3>
        <ul>
            <li><?php esc_html_e('Front-end pages are cached into /wp-content/efc-cache/ as static HTML files.', 'easy-front-end-cache'); ?></li>
            <li><?php esc_html_e('On first visit, the page is generated normally and saved to cache.', 'easy-front-end-cache'); ?></li>
            <li><?php esc_html_e('On subsequent visits, if the cache is still valid, the cached file is served instantly.', 'easy-front-end-cache'); ?></li>
            <li><?php esc_html_e('Logged-in users and admin pages are never cached.', 'easy-front-end-cache'); ?></li>
            <li><strong><?php esc_html_e('Default Exclusions:', 'easy-front-end-cache'); ?></strong> <?php esc_html_e('Preview pages, WordPress search results, reset URLs (e.g., ?reset=1), and any request with query parameters are never cached.', 'easy-front-end-cache'); ?></li>
        </ul>

        <h3><?php esc_html_e('Cache Reset Options', 'easy-front-end-cache'); ?></h3>
        <ul>
            <li><strong><?php esc_html_e('Single Page Reset:', 'easy-front-end-cache'); ?></strong> <?php esc_html_e('Add ?reset=1 (or your custom param) to the page URL to clear its cache.', 'easy-front-end-cache'); ?></li>
            <li><strong><?php esc_html_e('Global Reset:', 'easy-front-end-cache'); ?></strong> <?php esc_html_e('Add ?reset_all=1 (or your custom param) to any URL to clear all cached pages.', 'easy-front-end-cache'); ?></li>
            <li><strong><?php esc_html_e('Manual Reset:', 'easy-front-end-cache'); ?></strong> <?php esc_html_e('Use the “Clear All Cache” button above.', 'easy-front-end-cache'); ?></li>
            <li><strong><?php esc_html_e('Security:', 'easy-front-end-cache'); ?></strong> <?php esc_html_e('Public reset can be disabled so only admins can trigger resets.', 'easy-front-end-cache'); ?></li>
        </ul>

        <h3><?php esc_html_e('Redirect Option', 'easy-front-end-cache'); ?></h3>
        <ul>
            <li><?php esc_html_e('If you set a redirect URL, all front-end requests will be redirected there.', 'easy-front-end-cache'); ?></li>
            <li><?php esc_html_e('Useful for maintenance mode or site migration.', 'easy-front-end-cache'); ?></li>
            <li><?php esc_html_e('Leave the field empty to disable redirection.', 'easy-front-end-cache'); ?></li>
        </ul>

        <h3><?php esc_html_e('Tips', 'easy-front-end-cache'); ?></h3>
        <ul>
            <li><?php esc_html_e('Ensure /wp-content/efc-cache/ is writable by the server.', 'easy-front-end-cache'); ?></li>
            <li><?php esc_html_e('Test caching while logged out (logged-in users bypass cache).', 'easy-front-end-cache'); ?></li>
            <li><?php esc_html_e('Check response headers for X-Cache: HIT to confirm cache serving.', 'easy-front-end-cache'); ?></li>
            <li><?php esc_html_e('Cache folder is protected with .htaccess and index.php to block direct access.', 'easy-front-end-cache'); ?></li>
        </ul>
    </div>
</div>