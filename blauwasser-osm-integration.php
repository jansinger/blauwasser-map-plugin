<?php

/**
 * Plugin Name: Blauwasser Maps
 * Version: ___VERSION___
 * Plugin URI: https://www.blauwasser.de/
 * Description: OpenSeaMap integration for blauwasser.de.
 * Author: Jan Singer
 * Author URI: https://www.singer-tc.de/
 * Requires at least: 5.0
 * Tested up to: 5.0
 *
 * Text Domain: blauwasser-osm-integration
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Jan Singer
 * @since 1.0.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
require_once 'version.php';
if (!defined('BLAUWASSER_OSM_INTEGRATION_VERSION')) {
	die;
}
// Load plugin class files.
require_once 'includes/class-blauwasser-osm-integration.php';

// Load plugin libraries.
require_once 'includes/lib/class-blauwasser-osm-integration-settings.php';
require_once 'includes/lib/class-blauwasser-osm-integration-admin-api.php';
require_once 'includes/lib/class-blauwasser-osm-integration-wp-backery.php';
require_once 'includes/lib/class-blauwasser-osm-integration-shortcode.php';
require_once 'includes/lib/class-blauwasser-osm-integration-rest-api.php';

/**
 * Returns the main instance of Blauwasser_OSM_Integration to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Blauwasser_OSM_Integration
 */
function blauwasser_osm_integration()
{
	$instance = Blauwasser_OSM_Integration::instance(__FILE__, BLAUWASSER_OSM_INTEGRATION_VERSION);

	if (is_null($instance->settings)) {
		$instance->settings = Blauwasser_OSM_Integration_Settings::instance($instance);
	}

	$instance->register_rest_api();
	if (!is_admin()) {
		$instance->register_shortcodes();
	}
	return $instance;
}

blauwasser_osm_integration();
