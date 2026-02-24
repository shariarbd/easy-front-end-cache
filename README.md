# Easy Front End Cache

**Version:** 1.2.0 
**Author:** Shariar  
**License:** GPLv2 or later

## Description
Easy Front End Cache is a lightweight file-based caching plugin for WordPress. It caches front-end pages into static HTML files for faster load times, with simple admin controls for cache management.

## ✨ Features
- ⚡ Front‑end caching for faster page loads
- 🧹 Manual cache clearing from both the admin bar and settings page (AJAX, no reload)
- 🎨 Colorful admin bar status showing cache size and file count
- 🔄 Automatic purge triggers (post update/delete, theme switch, scheduled cleanup)
- 🛡️ Secure cache directory (`wp-content/efc-cache/`) with `.htaccess` and `index.php`
- 🧩 Exclusions for admin, logged‑in users, search, previews, feeds, REST API, query strings, WooCommerce cart/checkout
- 🛠️ Options for minify, debug mode, and admin controls
- 🌍 Localization ready (`easy-front-end-cache.pot` included)

---

## Default Exclusions
The following are **never cached**:
- Admin pages
- Logged-in users
- WordPress preview pages
- WordPress search results
- Reset URLs (`?reset=1`, `?reset_all=1`)
- Any request with query parameters

## Installation
1. Upload the plugin files to `/wp-content/plugins/easy-front-end-cache/`
2. Activate the plugin through the WordPress admin
3. Configure settings under **Settings → Easy Front End Cache**

## Usage
- Cached files are stored in `/wp-content/efc-cache/`
- Use `?reset=1` to clear a single page cache
- Use `?reset_all=1` to clear all cached pages
- Use the admin “Clear All Cache” button for manual reset
- Set a redirect URL to send all visitors to a specific page

## Screenshots
1. Settings page with options
2. Cache management section
3. Admin notice after clearing cache

## Changelog
**1.2.0**
- Added AJAX cache clearing (admin bar + settings page)
- Added cache status box with warnings
- Added purge triggers (post update/delete, theme switch, scheduled cleanup)
- Added minify + debug options
- Secure cache directory with safety files

### 1.3
- Added default exclusions: preview pages, search results, reset URLs, query-string requests
- Improved admin UI with inline notes
- Added i18n support for messages

### 1.2
- Added public reset option
- Added site-wide redirect option

### 1.0
- Initial release