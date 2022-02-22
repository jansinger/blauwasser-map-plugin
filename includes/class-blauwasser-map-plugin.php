<?php

/**
 * Main plugin class file.
 *
 * @package Blauwasser-OSM
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Main plugin class.
 */
class Blauwasser_map_plugin
{

	/**
	 * The single instance of Blauwasser_map_plugin.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * Local instance of Blauwasser_map_plugin_Admin_API
	 *
	 * @var Blauwasser_map_plugin_Admin_API|null
	 */
	public $admin = null;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version; //phpcs:ignore

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	public $wpBackery;

	public $osm_assets_url;

	public $defaultSettings;

	public static $textdomain = 'blauwasser-map-plugin';

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public static $token = 'blauwasser_map_plugin';


	/**
	 * Constructor funtion.
	 *
	 * @param string $file File constructor.
	 * @param string $version Plugin version.
	 */
	private function __construct($file = '', $version = '1.0.0')
	{
		$this->_version = $version;

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname($this->file);
		$this->assets_dir = trailingslashit($this->dir) . 'assets';
		$this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

		$this->osm_assets_url = esc_url(trailingslashit(plugins_url('/assets/ol/', $this->file)));

		register_activation_hook($this->file, array($this, 'install'));

		// must be called before scripts
		$this->default_settings();

		// Load frontend JS & CSS.
		add_action('wp_enqueue_scripts', array($this, 'register_styles'), 10);
		add_action('wp_enqueue_scripts', array($this, 'register_scripts'), 10);

		// Load admin JS & CSS.
		add_action('admin_enqueue_scripts', array($this, 'register_admin_styles'), 10, 1);
		add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'), 10, 1);

		// Load API for generic admin functions.
		if (is_admin()) {
			$this->admin = new Blauwasser_map_plugin_Admin_API($this);
			$this->wpBackery = new Blauwasser_map_plugin_WP_Backery($this);
		}

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action('init', array($this, 'load_localisation'), 0);
	} // End __construct ()


	public function default_settings()
	{
		$lat = get_option('bw_osm_default_lat', '54.5126');
		$lon = get_option('bw_osm_default_lon', '13.6456');
		$this->defaultSettings = array(
			'center' => array($lon, $lat),
			'zoom' => get_option('bw_osm_default_zoom', '11')
		);
	}

	public function register_shortcodes()
	{
		return new Blauwasser_map_plugin_Shortcode($this, 'bw-map');
	}

	public function register_rest_api()
	{
		return new Blauwasser_map_plugin_Rest_Api('geo');
	}

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @return void
	 * @since   1.0.0
	 */
	public function register_styles()
	{
		// <link rel="stylesheet" href="/assets/{{ manifest['main.js'].css }}" />
		wp_register_style(self::$token . '-map', esc_url($this->osm_assets_url) . 'style.css', array(), $this->_version);
	} // End enqueue_styles ()

	public function register_admin_styles()
	{
		// <link rel="stylesheet" href="/assets/{{ manifest['main.js'].css }}" />
		wp_register_style(self::$token . '-map-admin', esc_url($this->osm_assets_url) . 'style.admin.css', array(), $this->_version);
	} // End enqueue_styles ()
	/**
	 * Load Javascript.
	 *
	 * @access  public
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function register_scripts()
	{
		add_filter('script_loader_tag', array($this, 'add_type_attribute'), 10, 3);
		if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
			// Local development mode
			$main_script = "http://localhost:3000/@vite/client";
			wp_register_script(self::$token . '-map-client', $main_script, array(), null, true);
			$map_script = "http://localhost:3000/ol/src/main.ts";
			wp_register_script(self::$token . '-map', $map_script, array(self::$token . '-map-client'), null, true);
		} else {
			// Production mode
			$map_script = esc_url($this->osm_assets_url) .  'bw-map.umd.js';
			wp_register_script(self::$token . '-map', $map_script, array(), $this->_version, true);
		}
		$this->add_settings_script();
	} // End admin_enqueue_scripts ()

	public function register_admin_scripts()
	{
		add_filter('script_loader_tag', array($this, 'add_type_attribute'), 10, 3);
		if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
			// Local development mode
			$main_script = "http://localhost:3000/@vite/client";
			wp_register_script(self::$token . '-map-client', $main_script, array(), null, true);
			$map_script = "http://localhost:3000/ol/src/admin.ts";
			wp_register_script(self::$token . '-map-admin', $map_script, array(self::$token . '-map-client'), null, true);
		} else {
			// Production mode
			$map_script = esc_url($this->osm_assets_url) .  'bw-map.admin.umd.js';
			wp_register_script(self::$token . '-map-admin', $map_script, array(), $this->_version, true);
		}
		$this->add_settings_script();
	} // End admin_enqueue_scripts ()

	public function add_settings_script()
	{
		$bwMapPlugin = array(
			'settings' => array(
				'key' => get_option('bw_osm_maptiler_key', '_invalid_key_'),
				'map' => get_option('bw_osm_maptiler_style', 'streets'),
				'assetsUrl' => $this->assets_url,
				'restBase' => esc_url(get_rest_url()),
				'restApi' => esc_url(get_rest_url()) . 'blauwasser-map/v1/geo?',
				'center' => $this->defaultSettings['center'],
				'zoom' => $this->defaultSettings['zoom'],
			),
			'lang' => array(
				'noNewPosition' => __("Now new coordinates set, nothing to save.", Blauwasser_map_plugin::$textdomain),
				'changePosition' => __("New coordinates set, please do not forget to save!", Blauwasser_map_plugin::$textdomain),
				'positionSaved' => __("New position saved successfully", Blauwasser_map_plugin::$textdomain),
				'positionDeleted' => __("Position deleted successfully", Blauwasser_map_plugin::$textdomain),
				'saveError' => __('Error saving new position:', Blauwasser_map_plugin::$textdomain),
				'deleteError' => __('Error deleting position:', Blauwasser_map_plugin::$textdomain),
				'geotag' => __('Geotag (lat/lon):', Blauwasser_map_plugin::$textdomain),
				'currentZoom' => __('Current zoom:', Blauwasser_map_plugin::$textdomain),
				'shortcodeHint' => __('To show a map with a marker at the geotag position in the center copy the shown shortcode into the textblock.', Blauwasser_map_plugin::$textdomain),
				'save' => __('Save', Blauwasser_map_plugin::$textdomain),
				'resetGeotag' => __('Reset geotag position', Blauwasser_map_plugin::$textdomain),
				'deleteGeotag' => __('Delete geotag from post', Blauwasser_map_plugin::$textdomain),
				'center' => __('Center', Blauwasser_map_plugin::$textdomain),
				'zoom' => __('Zoom', Blauwasser_map_plugin::$textdomain),
			),
			'data' => array(),
		);
		$script = 'const bwMapPlugin=' . json_encode($bwMapPlugin) . ';';

		wp_register_script(self::$token . '-settings', false, array(), false, true);
		wp_add_inline_script(self::$token . '-settings', $script, 'before');
	}

	public function add_type_attribute($tag, $handle, $src)
	{
		// if not your script, do nothing and return original $tag
		if (strpos($handle, self::$token . '-map') !== 0) {
			return $tag;
		}
		// change the script tag by adding type="module" and return it.
		return '<script type="module" src="' . esc_url($src) . '"></script>';
	}


	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_localisation()
	{
		load_plugin_textdomain(self::$textdomain, false, dirname(plugin_basename($this->file)) . '/lang/');
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain()
	{
		$locale = apply_filters('plugin_locale', get_locale(), self::$textdomain);

		load_textdomain(self::$textdomain, WP_LANG_DIR . '/' . self::$textdomain . '/' . self::$textdomain . '-' . $locale . '.mo');
		load_plugin_textdomain(self::$textdomain, false, dirname(plugin_basename($this->file)) . '/lang/');
	} // End load_plugin_textdomain ()

	/**
	 * Main Blauwasser_map_plugin Instance
	 *
	 * Ensures only one instance of Blauwasser_map_plugin is loaded or can be loaded.
	 *
	 * @param string $file File instance.
	 * @param string $version Version parameter.
	 *
	 * @return Object Blauwasser_map_plugin instance
	 * @see Blauwasser_map_plugin()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance($file = '', $version = '1.0.0')
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self($file, $version);
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone()
	{
		_doing_it_wrong(__FUNCTION__, esc_html(__('Cloning of Blauwasser_map_plugin is forbidden')), esc_attr($this->_version));
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup()
	{
		_doing_it_wrong(__FUNCTION__, esc_html(__('Unserializing instances of Blauwasser_map_plugin is forbidden')), esc_attr($this->_version));
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function install()
	{
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	private function _log_version_number()
	{ //phpcs:ignore
		update_option(self::$token . '_version', $this->_version);
	} // End _log_version_number ()

}
