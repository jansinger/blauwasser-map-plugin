<?php

/**
 * Rest API declaration file.
 *
 * @package Blauwasser-OSM
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Post type declaration class.
 */
class Blauwasser_OSM_Integration_Rest_Api
{

	public static $rest_base = 'blauwasser-map/v1';

	/**
	 * The name for the endpoint.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $endpoint;

	/**
	 * Constructor
	 *
	 * @param string $endpoint Endpoint name.
	 */
	public function __construct($endpoint)
	{

		if (!$endpoint) {
			return;
		}

		// Endpoint name.
		$this->endpoint   = $endpoint;

		// Regsiter rest api.
		add_action('init', array($this, 'register_rest_api'));
	}

	/**
	 * Register new shortcode
	 *
	 * @return void
	 */
	public function register_rest_api()
	{
		// Add endpoint
		add_action('rest_api_init', array($this, 'rest_api_init'), 10);
	}

	public function rest_api_init()
	{
		register_rest_route(self::$rest_base, $this->endpoint, array(
			'methods' => 'GET',
			'callback' => array($this, 'get_posts_geojson'),
			'args' => $this->prefix_get_endpoint_args(),
			'permission_callback' => '__return_true'
		));
	}

	private function map_geojson($post)
	{

		$latlon = get_post_meta($post->ID, "OSM_geo_data", true);
		if (!empty($latlon)) {
			list($lat, $lon) = explode(',', $latlon);
		}

		return array(
			'type' => 'Feature',
			'geometry' => array(
				'type' => 'Point',
				'coordinates' => array($lon, $lat)
			),
			'properties' => array(
				'title' => $post->post_title,
				'link' => get_permalink($post),
				'image' => get_the_post_thumbnail($post, 'medium')
			)
		);
	}

	public function addTaxQuery(&$settings, $taxonomies)
	{
		$vc_taxonomies_types = get_taxonomies(array('public' => true));
		$terms = get_terms(array_keys($vc_taxonomies_types), array(
			'hide_empty' => false,
			'include' => $taxonomies,
		));
		$tax_queries = array(); // List of taxnonimes
		foreach ($terms as $term) {
			if (!isset($tax_queries[$term->taxonomy])) {
				$tax_queries[$term->taxonomy] = array(
					'taxonomy' => $term->taxonomy,
					'field' => 'id',
					'terms' => array($term->term_id),
					'relation' => 'IN',
				);
			} else {
				$tax_queries[$term->taxonomy]['terms'][] = $term->term_id;
			}
		}
		$settings['tax_query'] = array_values($tax_queries);
		$settings['tax_query']['relation'] = 'OR';
	}

	public function get_posts_geojson($request)
	{

		$query = array('meta_key' => 'OSM_geo_data', 'nopaging' => true);
		$tags = $request['tags'];
		if (!empty($tags)) {
			$query['tag'] = $tags;
		}
		$categories = $request['categories'];
		if (!empty($categories)) {
			$query['category_name'] = $categories;
		}
		$taxonomies = $request['taxonomies'];
		if (!empty($taxonomies)) {
			$this->addTaxQuery($query, $taxonomies);
		}

		$the_query = new WP_Query($query);

		// The Loop
		$data = array(
			'type' => 'FeatureCollection',
		);
		if ($the_query->have_posts()) {
			$posts = $the_query->posts;
			$data['features'] = array_map(array($this, 'map_geojson'), $posts);
			return new WP_REST_Response($data, 200);
		} else {
			return new WP_Error('no_geo_posts', 'No posts found.', array('status' => 404));
		}
	}
	/**
	 * Get the argument schema for this endpoint.
	 */
	private function prefix_get_endpoint_args()
	{
		$args = array();

		// Here we add our PHP representation of JSON Schema.
		$args['categories'] = array(
			'description'       => esc_html__('Categories to search for.', Blauwasser_OSM_Integration::$textdomain),
			'type'              => 'string',
			'validate_callback' => array($this, 'prefix_validate_categories'),
			'sanitize_callback' => array($this, 'prefix_sanitize_categories'),
			'required'          => false,
		);
		$args['tags'] = array(
			'description'       => esc_html__('Tags to search for.', Blauwasser_OSM_Integration::$textdomain),
			'type'              => 'string',
			'validate_callback' => array($this, 'prefix_validate_categories'),
			'sanitize_callback' => array($this, 'prefix_sanitize_categories'),
			'required'          => false,
		);
		$args['taxonomies'] = array(
			'description'       => esc_html__('Taxonomies to search for.', Blauwasser_OSM_Integration::$textdomain),
			'type'              => 'string',
			'validate_callback' => array($this, 'prefix_validate_categories'),
			'sanitize_callback' => array($this, 'prefix_sanitize_categories'),
			'required'          => false,
		);

		return $args;
	}

	/**
	 * Our validation callback for `string` parameter.
	 *
	 * @param mixed           $value   Value of the categories parameter.
	 * @param WP_REST_Request $request Current request object.
	 * @param string          $param   The name of the parameter in this case, 'categories'.
	 * @return true|WP_Error True if the data is valid, WP_Error otherwise.
	 */
	public function prefix_validate_categories($value, $request, $param)
	{
		$attributes = $request->get_attributes();

		if (isset($attributes['args'][$param])) {
			$argument = $attributes['args'][$param];
			// Check to make sure our argument is a string.
			if ('string' === $argument['type'] && !is_string($value)) {
				return new WP_Error('rest_invalid_param', sprintf(esc_html__('%1$s is not of type %2$s', Blauwasser_OSM_Integration::$textdomain), $param, 'string'), array('status' => 400));
			}
		} else {
			// This code won't execute because we have specified this argument as required.
			// If we reused this validation callback and did not have required args then this would fire.
			return new WP_Error('rest_invalid_param', sprintf(esc_html__('%s was not registered as a request argument.', Blauwasser_OSM_Integration::$textdomain), $param), array('status' => 400));
		}

		// If we got this far then the data is valid.
		return true;
	}

	/**
	 * Our sanitization callback for `string` parameter.
	 *
	 * @param mixed           $value   Value of the categoriesparameter.
	 * @param WP_REST_Request $request Current request object.
	 * @param string          $param   The name of the parameter in this case, 'categories'.
	 * @return mixed|WP_Error The sanitize value, or a WP_Error if the data could not be sanitized.
	 */
	public function prefix_sanitize_categories($value, $request, $param)
	{
		$attributes = $request->get_attributes();

		if (isset($attributes['args'][$param])) {
			$argument = $attributes['args'][$param];
			// Check to make sure our argument is a string.
			if ('string' === $argument['type']) {
				return sanitize_text_field($value);
			}
		} else {
			// This code won't execute because we have specified this argument as required.
			// If we reused this validation callback and did not have required args then this would fire.
			return new WP_Error('rest_invalid_param', sprintf(esc_html__('%s was not registered as a request argument.', Blauwasser_OSM_Integration::$textdomain), $param), array('status' => 400));
		}

		// If we got this far then something went wrong don't use user input.
		return new WP_Error('rest_api_sad', esc_html__('Something went terribly wrong.', Blauwasser_OSM_Integration::$textdomain), array('status' => 500));
	}
}
