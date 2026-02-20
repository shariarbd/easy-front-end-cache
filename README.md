# Easy Front End Cache

**Contributors:** yourname  
**Tags:** cache, performance, speed, optimization  
**Requires at least:** 5.0  
**Tested up to:** 6.5  
**Stable tag:** 1.1  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

A lightweight file-based caching plugin for WordPress front-end pages. Includes admin controls for cache lifetime, reset parameters, manual reset, cache folder size display, and optional site-wide redirect.

---

## Description

Easy Front End Cache speeds up your WordPress site by caching front-end pages into static HTML files.  
It is simple, dependency-free, and admin-friendly.

### Features
- Cache front-end pages into `/wp-content/efc-cache/`
- Configurable cache lifetime (seconds)
- Custom reset parameters (`?reset=1`, `?reset_all=1`, or your own)
- Manual "Clear All Cache" button in admin
- Cache folder size display
- Redirect field for maintenance/migration
- Inline documentation in admin panel
- Excludes logged-in users and admin pages

---

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate **Easy Front End Cache** from the WordPress admin.
3. Go to **Settings → Easy Front End Cache** to configure options.

---

## Usage

- **Cache Time:** Set how long cached pages remain valid.
- **Reset Single Param:** Add `?reset=1` (or your custom param) to a page URL to clear its cache.
- **Reset All Param:** Add `?reset_all=1` (or your custom param) to any URL to clear all cache.
- **Manual Reset:** Use the "Clear All Cache" button in admin.
- **Redirect Entire Site:** Enter a URL to redirect all front-end requests (maintenance mode).

---

## Frequently Asked Questions

**Q: Does this cache logged-in users?**  
A: No, logged-in users and admin pages are excluded.

**Q: Where are cache files stored?**  
A: In `/wp-content/efc-cache/`.

**Q: How do I know if a page is served from cache?**  
A: Check response headers for `X-Cache: HIT`.

---

## Screenshots

1. Settings page with cache time, reset parameters, and redirect field.  
2. Cache management section showing folder size and reset button.  
3. Documentation section explaining usage.

---

## Changelog

### 1.1
- Added cache folder size display
- Added manual reset button
- Added redirect field
- Added inline documentation

### 1.0
- Initial release with caching and reset parameters

---

## License

This plugin is licensed under the GPLv2 or later.