<div class="wrap">
    <h1>Easy Front End Cache Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields('efc_settings_group'); ?>
        <?php do_settings_sections('efc_settings_group'); ?>

        <table class="form-table">
            <tr>
                <th scope="row">Cache Time (seconds)</th>
                <td>
                    <input type="number" name="efc_cache_time" value="<?php echo esc_attr(get_option('efc_cache_time', 600)); ?>" />
                    <p class="description">How long each cached page should remain valid before being regenerated.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Reset Single Param</th>
                <td>
                    <input type="text" name="efc_reset_param" value="<?php echo esc_attr(get_option('efc_reset_param', 'reset')); ?>" />
                    <p class="description">Query parameter to clear cache for the current page. Example: <code>?reset=1</code></p>
                </td>
            </tr>
            <tr>
                <th scope="row">Reset All Param</th>
                <td>
                    <input type="text" name="efc_reset_all_param" value="<?php echo esc_attr(get_option('efc_reset_all_param', 'reset_all')); ?>" />
                    <p class="description">Query parameter to clear all cached pages. Example: <code>?reset_all=1</code></p>
                </td>
            </tr>
            <tr>
                <th scope="row">Redirect Entire Site</th>
                <td>
                    <input type="url" name="efc_redirect_url" value="<?php echo esc_attr(get_option('efc_redirect_url', '')); ?>" placeholder="https://example.com" />
                    <p class="description">If set, all front-end requests will redirect to this URL. Leave empty to disable.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Allow Public Reset via URL</th>
                <td>
                    <input type="checkbox" name="efc_allow_public_reset" value="1" <?php checked(1, get_option('efc_allow_public_reset', 0)); ?> />
                    <p class="description">
                        If enabled, anyone can clear cache using query parameters (e.g., <code>?reset=1</code>).
                        If disabled, only logged-in admins can use reset parameters.
                        Manual reset button in admin always works.
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>

    <h2>Cache Management</h2>
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
    <p><strong>Cache Folder Size:</strong> <?php echo size_format($size); ?></p>
    <p><strong>Total Cached Files:</strong> <?php echo intval($count); ?></p>

    <form method="post">
        <?php wp_nonce_field('efc_clear_cache_action', 'efc_clear_cache_nonce'); ?>
        <input type="submit" name="efc_clear_cache" class="button button-secondary" value="Clear All Cache" />
    </form>

    <h2>📖 Documentation</h2>
    <div style="background:#fff;border:1px solid #ccd0d4;padding:15px;">
        <h3>How It Works</h3>
        <ul>
            <li>Front-end pages are cached into <code>/wp-content/efc-cache/</code> as static HTML files.</li>
            <li>On first visit, the page is generated normally and saved to cache.</li>
            <li>On subsequent visits, if the cache is still valid, the cached file is served instantly.</li>
            <li>Logged-in users and admin pages are never cached.</li>
        </ul>

        <h3>Cache Reset Options</h3>
        <ul>
            <li><strong>Single Page Reset:</strong> Add <code>?reset=1</code> (or your custom param) to the page URL to clear its cache.</li>
            <li><strong>Global Reset:</strong> Add <code>?reset_all=1</code> (or your custom param) to any URL to clear all cached pages.</li>
            <li><strong>Manual Reset:</strong> Use the “Clear All Cache” button above.</li>
            <li><strong>Security:</strong> Public reset can be disabled so only admins can trigger resets.</li>
        </ul>

        <h3>Redirect Option</h3>
        <ul>
            <li>If you set a redirect URL, all front-end requests will be redirected there.</li>
            <li>Useful for maintenance mode or site migration.</li>
            <li>Leave the field empty to disable redirection.</li>
        </ul>

        <h3>Tips</h3>
        <ul>
            <li>Ensure <code>/wp-content/efc-cache/</code> is writable by the server.</li>
            <li>Test caching while logged out (logged-in users bypass cache).</li>
            <li>Check response headers for <code>X-Cache: HIT</code> to confirm cache serving.</li>
            <li>Cache folder is protected with <code>.htaccess</code> and <code>index.php</code> to block direct access.</li>
        </ul>
    </div>
</div>
