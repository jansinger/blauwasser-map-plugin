<?php

/**
 * Post type Admin API file.
 *
 * @package Blauwasser-OSM
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Admin API class.
 */
class Blauwasser_OSM_Integration_WP_Backery
{

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent;

	/**
	 * Constructor function
	 */
	public function __construct($parent)
	{
		$this->parent = $parent;

		add_action("init", array($this, "add_map_editor"), 0);
	}

	public function show_map_editor()
	{
		return '<div class="bw-map-editor"></div>';
	}


	public function add_map_editor()
	{
		if (!function_exists('vc_map')) {
			return;
		}

		// Narrow data taxonomies
		add_filter('vc_autocomplete_bw-map_taxonomies_callback', 'vc_autocomplete_taxonomies_field_search', 10, 1);
		add_filter('vc_autocomplete_bw-map_taxonomies_render', 'vc_autocomplete_taxonomies_field_render', 10, 1);

		vc_add_shortcode_param('bw-map-preview', array($this, 'show_map_editor'), $this->parent->assets_url . 'js/editor.js');

		list($lon, $lat) = $this->parent->defaultSettings['center'];

		vc_map(array(
			"name" => __("Blauwasser Map", Blauwasser_OSM_Integration::$textdomain),
			"base" => "bw-map",
			"class" => "bw-map-settings",
			"category" => __('Content'),
			"icon" => $this->parent->assets_url . 'pics/bw-map-block.png',
			"weight" => 0,
			"params" => array(
				array(
					"type" => "hidden",
					"holder" => "span",
					"edit_field_class" => "bw-map-zoom col-md-4",
					"heading" => __("Zoom", Blauwasser_OSM_Integration::$textdomain),
					"param_name" => "zoom",
					"value" => $this->parent->defaultSettings['zoom'],
					"description" => __("Map zoom level.", Blauwasser_OSM_Integration::$textdomain)
				),
				array(
					"type" => "hidden",
					"holder" => "span",
					"edit_field_class" => "bw-map-center",
					"heading" => __("Center", Blauwasser_OSM_Integration::$textdomain),
					"param_name" => "center",
					"value" => "{$lat}, {$lon}",
					"description" => __("Coordinates of the map center.", Blauwasser_OSM_Integration::$textdomain)
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__('Filter posts', Blauwasser_OSM_Integration::$textdomain),
					'param_name' => 'taxonomies',
					"edit_field_class" => "bw-map-taxonomies",
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						// In UI show results grouped by groups, default false
						'unique_values' => true,
						// In UI show results except selected. NB! You should manually check values in backend, default false
						'display_inline' => true,
						// In UI show results inline view, default false (each value in own line)
						'delay' => 500,
						// delay for search. default 500
						'auto_focus' => true,
						// auto focus input, default true
					),
					'param_holder_class' => 'vc_not-for-custom',
					'description' => esc_html__('Enter categories, tags or custom taxonomies.', Blauwasser_OSM_Integration::$textdomain),
				),
				array(
					"type" => "bw-map-preview",
					"holder" => "div",
					"class" => "",
					"heading" => __("Map preview", Blauwasser_OSM_Integration::$textdomain),
					"param_name" => "map",
					"value" => "",
					"description" => __("Map preview. Adjust size and zoom and save.", Blauwasser_OSM_Integration::$textdomain),
				),
			)
		));
	}
}
