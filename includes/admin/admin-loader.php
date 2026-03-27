<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Require all modular admin classes
require_once __DIR__ . '/trait-admin-renderers.php';
require_once __DIR__ . '/class-admin-general.php';
require_once __DIR__ . '/class-admin-purge.php';
require_once __DIR__ . '/class-admin-exclusions.php';
require_once __DIR__ . '/class-admin-status.php';
require_once __DIR__ . '/class-admin-settings.php';

// Initialize the settings system
EFEC_Admin_Settings::init();