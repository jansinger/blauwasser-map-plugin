<?php

/**
 * Post type declaration file.
 *
 * @package Blauwasser-OSM
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Post type declaration class.
 */
class Blauwasser_OSM_Integration_Shortcode
{

	/**
	 * The name for the shortcode.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $shortcode;

	/**
	 * The main plugin object.
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent;

	/**
	 * Constructor
	 *
	 * @param string $shortcode Shortcode.
	 */
	public function __construct($parent, $shortcode)
	{

		if (!$shortcode || !$parent) {
			return;
		}

		// Shortcode name.
		$this->shortcode   = $shortcode;
		$this->parent = $parent;

		// Regsiter shortcode.
		add_action('init', array($this, 'register_shortcode'));
	}

	/**
	 * Register new shortcode
	 *
	 * @return void
	 */
	public function register_shortcode()
	{
		// Add Shortcode
		add_shortcode($this->shortcode, array($this, 'handle_shortcode'));
		// Add geotags & OSM compatible shortcode (only if OSM Plugin is disabled)
		$osm_active = is_plugin_active('osm/osm.php');
		if (!$osm_active) {
			add_action('wp_head', array($this, 'add_geotag_head'));
			add_shortcode('osm_map_v3', array($this, 'handle_shortcode'));
		}
	}

	public function add_geotag_head()
	{
		global $post;

		if (!isset($post)) {
			return;
		}

		$lat = '';
		$lon = '';

		$PostLatLon = get_post_meta($post->ID, 'OSM_geo_data', true);
		if (!empty($PostLatLon)) {
			list($lat, $lon) = explode(',', $PostLatLon);
		}
		if (is_single() && ($lat != '') && ($lon != '')) {
			$title = convert_chars(strip_tags(get_bloginfo("name"))) . " - " . convert_chars(strip_tags($post->post_title));

			echo "<meta name=\"ICBM\" content=\"{$lat}, {$lon}\" />\n";
			echo "<meta name=\"DC.title\" content=\"{$title}\" />\n";
			echo "<meta name=\"geo.placename\" content=\"{$title}\"/>\n";
			echo "<meta name=\"geo.position\"  content=\"{$lat};{$lon}\" />\n";
		}
	}

	/**
	 * [osm_map_v3 map_center="56.8869,21.1831" zoom="14" width="100%" height="450" post_markers="1" type="OpenSeaMap"]
	 * 
	 * [osm_map_v3 map_center="29.000,-15.6864" zoom="7" width="100%" height="450" tagged_type="post" 
	 * marker_name="mic_red_pinother_02.png" type="OpenSeaMap" tagged_filter="Liegeplatz" 
	 * map_border="thin solid red" tagged_color="red" mwz="true"]
	 */

	/**
	 * handle shortcode call
	 *
	 * @param  array $atts Passed attributes.
	 */
	public function handle_shortcode($atts = array(), $_, $shortcode_tag)
	{
		global $post;

		$latlon = get_post_meta($post->ID, "OSM_geo_data", true);

		// Attributes
		$atts = shortcode_atts(
			array(
				'width' => '100%',
				'height' => '450px',
				'zoom' => $this->parent->defaultSettings['zoom'],
				'center' => $latlon,
				'elementId' => 'bw-osm-' . uniqid(),
				'title' => get_the_title($post),
				'image' => get_the_post_thumbnail($post, 'medium'),
				'categories' => null,
				'tags' => null,
				'show_marker' => true,
				'taxonomies' => null,
				// OSM compatibility
				'map_center' => null,
				'tagged_filter' => null,
				'post_markers' => null
			),
			$atts,
			'bw-osm'
		);

		if (is_numeric($atts['height'])) {
			$atts['height'] = "{$atts['height']}px";
		}

		// OSM Plugin compability mode
		if (!empty($atts['map_center'])) {
			$atts['center'] = $atts['map_center'];
		}

		if (!empty($atts['center'])) {
			list($lat, $lon) = explode(',', $atts['center']);
			$atts['center'] = array($lon, $lat);
		} else {
			$atts['center'] = $this->parent->defaultSettings['center'];
		}

		// OSM Plugin compability mode
		if (!empty($atts['tagged_filter'])) {
			$atts['categories'] = $atts['tagged_filter'];
		}

		// OSM Plugin compability mode
		if ($shortcode_tag === 'osm_map_v3' && empty($atts['post_markers']) && empty($atts['tagged_filter'])) {
			$atts['show_marker'] = false;
		}

		if ($atts['categories'] != null || $atts['tags'] != null || $atts['taxonomies'] != null) {
			$data = array('categories' => $atts['categories'], 'tags' => $atts['tags'], 'taxonomies' => $atts['taxonomies']);
			$query = http_build_query(array_filter($data));
			$atts['src'] = esc_url(get_rest_url()) . 'blauwasser-map/v1/geo?' . $query;
		}

		wp_enqueue_style(Blauwasser_OSM_Integration::$token . '-map');
		wp_register_script($atts['elementId'], false, array(Blauwasser_OSM_Integration::$token . '-map', Blauwasser_OSM_Integration::$token . '-settings'), false, true);
		wp_enqueue_script($atts['elementId']);
		$script = 'bwOsmPlugin && bwOsmPlugin.data.push(' . json_encode($atts) . '); ';
		wp_add_inline_script($atts['elementId'], $script, 'before');

		$custom_css = "width: {$atts['width']}; height: {$atts['height']};";
		return '<div id="' . $atts['elementId']  . '" class="bw-osm-map" style="' . $custom_css . '"></div>';
	}
}
