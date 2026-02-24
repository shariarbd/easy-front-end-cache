=== Easy Front End Cache ===
Contributors: yourname
Donate link: https://yourwebsite.com
Tags: cache, performance, speed, optimization
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, admin‑friendly WordPress plugin that provides front‑end page caching with instant AJAX clearing, colorful admin bar status, and flexible purge options.

== Description ==
Easy Front End Cache improves site performance by caching front‑end pages into static HTML files. It includes admin bar integration, AJAX‑based cache clearing, purge triggers, and secure cache directory handling.

**Features:**
* Front‑end caching for faster page loads
* Manual cache clearing from admin bar and settings page (AJAX, no reload)
* Colorful admin bar status showing cache size and file count
* Automatic purge triggers (post update/delete, theme switch, scheduled cleanup)
* Secure cache directory with `.htaccess` and `index.php`
* Exclusions for admin, logged‑in users, search, previews, feeds, REST API, query strings, WooCommerce cart/checkout
* Options for minify, debug mode, and admin controls
* Localization ready (`easy-front-end-cache.pot` included)

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/easy-front-end-cache/` directory, or install via the WordPress Plugins screen.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure settings under **Settings → Front End Cache**.

== Screenshots ==
1. Settings Page — cache stats, manual clear button, options
2. Admin Bar Integration — colorful status with cache size, file count, and AJAX clear

== Frequently Asked Questions ==
= Does this plugin cache admin pages? =
No. Admin pages, logged‑in users, search results, previews, feeds, REST API, and WooCommerce cart/checkout are excluded.

= How do I clear the cache? =
You can clear cache instantly from the admin bar or the settings page using the AJAX‑based “Clean All Cache Now” button.

= Is the cache directory secure? =
Yes. The plugin auto‑creates `.htaccess` and `index.php` safety files in `wp-content/efc-cache/`.

== Changelog ==
= 1.2.0 =
* Added AJAX cache clearing (admin bar + settings page)
* Added cache status box with warnings
* Added purge triggers (post update/delete, theme switch, scheduled cleanup)
* Added minify + debug options
* Secure cache directory with safety files

== Upgrade Notice ==
= 1.2.0 =
This release adds AJAX cache clearing, purge triggers, and improved admin UX. Update recommended.