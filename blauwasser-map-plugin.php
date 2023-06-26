<?php
// x-release-please-start-version
/**
 * Plugin Name: Blauwasser Maps
 * Version: 1.4.1
 * Plugin URI: https://www.blauwasser.de/
 * Description: OpenSeaMap & Maptiler Karten für blauwasser.de.
 * Author: Jan Singer
 * Author URI: https://singer-tc.de/
 * Requires at least: 5.0
 * Tested up to: 5.0
 *
 * Text Domain: blauwasser-map-plugin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Jan Singer
 * @since 1.4.1
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.4.1 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('BLAUWASSER_MAP_PLUGIN_VERSION', '1.4.1');
// x-release-please-end
// Load plugin class files.
require_once 'includes/class-blauwasser-map-plugin.php';

// Load plugin libraries.
require_once 'includes/lib/class-blauwasser-map-plugin-settings.php';
require_once 'includes/lib/class-blauwasser-map-plugin-admin-api.php';
require_once 'includes/lib/class-blauwasser-map-plugin-wp-backery.php';
require_once 'includes/lib/class-blauwasser-map-plugin-shortcode.php';
require_once 'includes/lib/class-blauwasser-map-plugin-rest-api.php';

/**
 * Returns the main instance of Blauwasser_map_plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Blauwasser_map_plugin
 */
function Blauwasser_map_plugin()
{
	$instance = Blauwasser_map_plugin::instance(__FILE__, BLAUWASSER_MAP_PLUGIN_VERSION);

	if (is_null($instance->settings)) {
		$instance->settings = Blauwasser_map_plugin_Settings::instance($instance);
	}

	$instance->register_rest_api();
	if (!is_admin()) {
		$instance->register_shortcodes();
	}
	return $instance;
}

Blauwasser_map_plugin();
