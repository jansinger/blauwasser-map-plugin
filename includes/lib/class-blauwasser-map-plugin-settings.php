<?php

/**
 * Settings class file.
 *
 * @package Blauwasser-OSM
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Settings class.
 */
class Blauwasser_map_plugin_Settings
{

	/**
	 * The single instance of Blauwasser_map_plugin_Settings.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	/**
	 * Constructor function.
	 *
	 * @param object $parent Parent object.
	 */
	private function __construct($parent)
	{
		$this->parent = $parent;

		$this->base = 'bw_osm_';

		// Initialise settings.
		add_action('init', array($this, 'init_settings'), 11);

		// Register plugin settings.
		add_action('admin_init', array($this, 'register_settings'));

		// Add settings page to menu.
		add_action('admin_menu', array($this, 'add_menu_item'));

		// Add settings link to plugins page.
		add_filter(
			'plugin_action_links_' . plugin_basename($this->parent->file),
			array(
				$this,
				'add_settings_link',
			)
		);

		// Configure placement of plugin settings page. See readme for implementation.
		add_filter($this->base . 'menu_settings', array($this, 'configure_settings'));
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function init_settings()
	{
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item()
	{

		$args = $this->menu_settings();

		// Do nothing if wrong location key is set.
		if (is_array($args) && isset($args['location']) && function_exists('add_' . $args['location'] . '_page')) {
			switch ($args['location']) {
				case 'options':
				case 'submenu':
					$page = add_submenu_page($args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function']);
					break;
				case 'menu':
					$page = add_menu_page($args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position']);
					break;
				default:
					return;
			}
			add_action('admin_print_styles-' . $page, array($this, 'settings_assets'));
		}
	}

	/**
	 * Prepare default settings page arguments
	 *
	 * @return mixed|void
	 */
	private function menu_settings()
	{
		return apply_filters(
			$this->base . 'menu_settings',
			array(
				'location'    => 'options', // Possible settings: options, menu, submenu.
				'parent_slug' => 'options-general.php',
				'page_title'  => __('BW OSM', 'blauwasser-map-plugin'),
				'menu_title'  => __('BW OSM', 'blauwasser-map-plugin'),
				'capability'  => 'manage_options',
				'menu_slug'   => Blauwasser_map_plugin::$token . '_settings',
				'function'    => array($this, 'settings_page'),
				'icon_url'    => '',
				'position'    => null,
			)
		);
	}

	/**
	 * Container for settings page arguments
	 *
	 * @param array $settings Settings array.
	 *
	 * @return array
	 */
	public function configure_settings($settings = array())
	{
		return $settings;
	}

	/**
	 * Load settings JS & CSS
	 *
	 * @return void
	 */
	public function settings_assets()
	{

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below.
		wp_enqueue_style('farbtastic');
		wp_enqueue_script('farbtastic');

		// We're including the WP media scripts here because they're needed for the image upload field.
		// If you're not including an image upload then you can leave this function call out.
		wp_enqueue_media();

		wp_register_script(Blauwasser_map_plugin::$token . '-settings-js', $this->parent->assets_url . 'js/settings.min.js', array('farbtastic', 'jquery'), '1.0.0', true);
		wp_enqueue_script(Blauwasser_map_plugin::$token . '-settings-js');
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links.
	 */
	public function add_settings_link($links)
	{
		$settings_link = '<a href="options-general.php?page=' . Blauwasser_map_plugin::$token . '_settings">' . __('Settings', 'blauwasser-map-plugin') . '</a>';
		array_push($links, $settings_link);
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields()
	{
		$settings['standard'] = array(
			'title'       => __('Standard', 'blauwasser-map-plugin'),
			'description' => __('Settings for OSM integration.', 'blauwasser-map-plugin'),
			'fields'      => array(
				array(
					'id'          => 'maptiler_key',
					'label'       => __('maptiler key', 'blauwasser-map-plugin'),
					'description' => __('API key for maptiler cloud. Visit <a href="https://cloud.maptiler.com">maptiler cloud</a> to create an API key.', 'blauwasser-map-plugin'),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => __('API key', 'blauwasser-map-plugin'),
				),
				array(
					'id'          => 'maptiler_style',
					'label'       => __('maptiler map style', 'blauwasser-map-plugin'),
					'description' => __('Choose a map style.', 'blauwasser-map-plugin'),
					'type'        => 'select',
					'options'     => array(
						'basic' 	=> 'basic',
						'streets'    => 'streets',
						'outdoor'   => 'outdoor',
						'pastel' 	=> 'pastel',
						'topo' 		=> 'topo',
						'topographique' => 'topographique',
						'voyager'   => 'voyager',
						'winter'    => 'winter',
						'hybrid' 	=> 'hybrid',
					),
					'default'     => 'streets',
				),
				array(
					'id'          => 'default_zoom',
					'label'       => __('Default zoom', 'blauwasser-map-plugin'),
					'description' => __('Default zoom level for maps.', 'blauwasser-map-plugin'),
					'type'        => 'number',
					'default'     => '13',
					'placeholder' => '13',
					'min' => '1',
					'max' => '28'
				),
				array(
					'id'          => 'default_lat',
					'label'       => __('Default latitude', 'blauwasser-map-plugin'),
					'description' => __('Default center latitude for maps (WGS84).', 'blauwasser-map-plugin'),
					'type'        => 'number',
					'default'     => '54.5126',
					'placeholder' => '54.5126',
					'min' => '-90',
					'max' => '90',
					'step' => '.0001'
				),
				array(
					'id'          => 'default_lon',
					'label'       => __('Default longitude', 'blauwasser-map-plugin'),
					'description' => __('Default center longitude for maps (WGS84).', 'blauwasser-map-plugin'),
					'type'        => 'number',
					'default'     => '13.6456',
					'placeholder' => '13.6456',
					'min' => '-180',
					'max' => '180',
					'step' => '.0001'
				),
			)
		);

		$settings = apply_filters(Blauwasser_map_plugin::$token . '_settings_fields', $settings);

		return $settings;
	}

	/**
	 * Register plugin settings
	 *
	 * @return void
	 */
	public function register_settings()
	{
		if (is_array($this->settings)) {

			// Check posted/selected tab.
			//phpcs:disable
			$current_section = '';
			if (isset($_POST['tab']) && $_POST['tab']) {
				$current_section = $_POST['tab'];
			} else {
				if (isset($_GET['tab']) && $_GET['tab']) {
					$current_section = $_GET['tab'];
				}
			}
			//phpcs:enable

			foreach ($this->settings as $section => $data) {

				if ($current_section && $current_section !== $section) {
					continue;
				}

				// Add section to page.
				add_settings_section($section, $data['title'], array($this, 'settings_section'), Blauwasser_map_plugin::$token . '_settings');

				foreach ($data['fields'] as $field) {

					// Validation callback for field.
					$validation = '';
					if (isset($field['callback'])) {
						$validation = $field['callback'];
					}

					// Register field.
					$option_name = $this->base . $field['id'];
					register_setting(Blauwasser_map_plugin::$token . '_settings', $option_name, $validation);

					// Add field to page.
					add_settings_field(
						$field['id'],
						$field['label'],
						array($this->parent->admin, 'display_field'),
						Blauwasser_map_plugin::$token . '_settings',
						$section,
						array(
							'field'  => $field,
							'prefix' => $this->base,
						)
					);
				}

				if (!$current_section) {
					break;
				}
			}
		}
	}

	/**
	 * Settings section.
	 *
	 * @param array $section Array of section ids.
	 * @return void
	 */
	public function settings_section($section)
	{
		$html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
		echo $html; //phpcs:ignore
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page()
	{

		// Build page HTML.
		$html      = '<div class="wrap" id="' . Blauwasser_map_plugin::$token . '_settings">' . "\n";
		$html .= '<h2>' . __('Plugin Settings', 'blauwasser-map-plugin') . '</h2>' . "\n";

		$tab = '';
		//phpcs:disable
		if (isset($_GET['tab']) && $_GET['tab']) {
			$tab .= $_GET['tab'];
		}
		//phpcs:enable

		// Show page tabs.
		if (is_array($this->settings) && 1 < count($this->settings)) {

			$html .= '<h2 class="nav-tab-wrapper">' . "\n";

			$c = 0;
			foreach ($this->settings as $section => $data) {

				// Set tab class.
				$class = 'nav-tab';
				if (!isset($_GET['tab'])) { //phpcs:ignore
					if (0 === $c) {
						$class .= ' nav-tab-active';
					}
				} else {
					if (isset($_GET['tab']) && $section == $_GET['tab']) { //phpcs:ignore
						$class .= ' nav-tab-active';
					}
				}

				// Set tab link.
				$tab_link = add_query_arg(array('tab' => $section));
				if (isset($_GET['settings-updated'])) { //phpcs:ignore
					$tab_link = remove_query_arg('settings-updated', $tab_link);
				}

				// Output tab.
				$html .= '<a href="' . $tab_link . '" class="' . esc_attr($class) . '">' . esc_html($data['title']) . '</a>' . "\n";

				++$c;
			}

			$html .= '</h2>' . "\n";
		}

		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

		// Get settings fields.
		ob_start();
		settings_fields(Blauwasser_map_plugin::$token . '_settings');
		do_settings_sections(Blauwasser_map_plugin::$token . '_settings');
		$html .= ob_get_clean();

		$html .= '<p class="submit">' . "\n";
		$html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
		$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'blauwasser-map-plugin')) . '" />' . "\n";
		$html .= '</p>' . "\n";
		$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html; //phpcs:ignore
	}

	/**
	 * Main Blauwasser_map_plugin_Settings Instance
	 *
	 * Ensures only one instance of Blauwasser_map_plugin_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Blauwasser_map_plugin()
	 * @param object $parent Object instance.
	 * @return object Blauwasser_map_plugin_Settings instance
	 */
	public static function instance($parent)
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self($parent);
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone()
	{
		_doing_it_wrong(__FUNCTION__, esc_html(__('Cloning of Blauwasser_map_plugin_API is forbidden.')), esc_attr($this->parent->_version));
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup()
	{
		_doing_it_wrong(__FUNCTION__, esc_html(__('Unserializing instances of Blauwasser_map_plugin_API is forbidden.')), esc_attr($this->parent->_version));
	} // End __wakeup()

}
