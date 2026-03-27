<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EFEC_Cache {

    public static function init() {
        // Hook into template_redirect early
        add_action( 'template_redirect', [ __CLASS__, 'handle_cache' ], 1 );
    }

    /**
     * Main cache handler
     */
    public static function handle_cache() {
        // ==============================
        // 1️⃣ Skip conditions
        // ==============================
        if ( is_admin() || is_user_logged_in() ) return;
        if ( is_preview() ) return;
        if ( is_search() ) return;

        // Exclusion logic
        if ( ! self::should_cache() ) {
            if ( get_option('efc_debug_mode') ) {
                header("X-Easy-Cache: BYPASS");
            }
            return;
        }

        // ==============================
        // 2️⃣ Get settings
        // ==============================
        $cache_time   = (int) get_option( 'efc_cache_time', 600 );
        $reset_param  = get_option( 'efc_reset_param', 'reset' );
        $reset_all    = get_option( 'efc_reset_all_param', 'reset_all' );
        $allow_public = (int) get_option( 'efc_allow_public_reset', 0 );
        $cache_dir    = WP_CONTENT_DIR . '/efc-cache/';

        if ( ! is_dir( $cache_dir ) ) {
            wp_mkdir_p( $cache_dir );
        }

        $request_uri = $_SERVER['REQUEST_URI'];
        $cache_key   = md5( $request_uri );
        $cache_file  = $cache_dir . $cache_key . '.html';

        // ==============================
        // 3️⃣ Handle reset first
        // ==============================
        if ( isset( $_GET[$reset_all] ) && $_GET[$reset_all] == 1 ) {
            if ( $allow_public || current_user_can( 'manage_options' ) ) {
                foreach ( glob( $cache_dir . '*.html' ) as $file ) {
                    if ( is_file( $file ) ) {
                        unlink( $file );
                    }
                }
                wp_die( esc_html__( '✅ All cache cleared.', 'easy-front-end-cache' ) );
            }
        }

        if ( isset( $_GET[$reset_param] ) && $_GET[$reset_param] == 1 ) {
            if ( $allow_public || current_user_can( 'manage_options' ) ) {
                if ( file_exists( $cache_file ) ) {
                    unlink( $cache_file );
                }
            }
            return; // Do not cache reset requests
        }

        // ==============================
        // 4️⃣ Allow only safe query strings
        // ==============================
        $allowed_params = [ 'p', 'page_id' ];
        if ( ! empty( $_GET ) ) {
            foreach ( $_GET as $key => $value ) {
                if ( ! in_array( $key, $allowed_params ) ) {
                    return; // Skip caching for custom query strings
                }
            }
        }

        // ==============================
        // 5️⃣ Serve cache if valid
        // ==============================
        if ( file_exists( $cache_file ) && ( time() - filemtime( $cache_file ) ) < $cache_time ) {
            if ( get_option( 'efc_debug_mode' ) ) {
                header( "X-Easy-Cache: HIT" );
            }
            readfile( $cache_file );
            exit;
        }

        // ==============================
        // 6️⃣ Start output buffering
        // ==============================
        ob_start();

        add_action( 'wp_footer', function() use ( $cache_file ) {
            $output = ob_get_contents();
            if ( $output !== false && strlen( $output ) > 0 ) {
                // Minify if enabled
                if ( get_option( 'efc_minify_html' ) ) {
                    $output = preg_replace( '/\s+/', ' ', $output );
                }
                file_put_contents( $cache_file, $output, LOCK_EX );
            }
            ob_end_flush();

            if ( get_option( 'efc_debug_mode' ) ) {
                header( "X-Easy-Cache: MISS" );
            }
        }, 999 );
    }

    public static function should_cache() {
        if ( ! get_option('efc_enable_cache') ) {
            return false;
        }

        global $post, $wp;

        // Resolve current URL to post ID
        $post_id = 0;
        if ( isset($wp) ) {
            $request_url = home_url( $wp->request );
            $post_id     = url_to_postid( $request_url );
        }

        // Excluded Pages
        $excluded_pages = (array) get_option('efc_excluded_pages', []);
        if ( $post_id && in_array($post_id, $excluded_pages, true) ) {
            return false;
        }

        // Excluded Posts
        $excluded_posts = (array) get_option('efc_excluded_posts', []);
        if ( $post && in_array($post->ID, $excluded_posts, true) ) {
            return false;
        }

        // Excluded Categories (single post view)
        $excluded_cats = (array) get_option('efc_excluded_categories', []);
        if ( $post && ! empty($excluded_cats) ) {
            $post_cats = wp_get_post_categories($post->ID);
            if ( array_intersect($excluded_cats, $post_cats) ) {
                return false;
            }
        }

        // Category archive exclusion
        if ( is_category() ) {
            $queried_cat = get_queried_object_id();
            if ( in_array($queried_cat, $excluded_cats, true) ) {
                return false;
            }
        }

        // Excluded Custom Post Types (single post view)
        $excluded_cpts = (array) get_option('efc_excluded_cpts', []);
        if ( $post && in_array($post->post_type, $excluded_cpts, true) ) {
            return false;
        }

        // CPT archive exclusion
        if ( is_post_type_archive() ) {
            $queried_cpt = get_query_var('post_type');
            if ( in_array($queried_cpt, $excluded_cpts, true) ) {
                return false;
            }
        }

        // Excluded Slugs
        $excluded_slugs = preg_split('/\r\n|\r|\n/', get_option('efc_excluded_slugs', ''));
        $excluded_slugs = array_map('trim', $excluded_slugs);
        $excluded_slugs = array_filter($excluded_slugs);

        if ( $post && ! empty($excluded_slugs) ) {
            if ( in_array($post->post_name, $excluded_slugs, true) ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Purge all cache files (used by admin/AJAX)
     */
    public static function purge_all() {
        $dir = WP_CONTENT_DIR . '/efc-cache/';
        if ( is_dir( $dir ) ) {
            foreach ( glob( $dir . '*.html' ) as $file ) {
                if ( is_file( $file ) ) {
                    unlink( $file );
                }
            }
        }
    }
}