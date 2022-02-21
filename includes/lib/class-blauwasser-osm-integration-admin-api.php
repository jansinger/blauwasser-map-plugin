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
class Blauwasser_OSM_Integration_Admin_API
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

		//add_filter('ships_custom_fields', array($this, 'add_ship_fields'));

		//add_action('save_post', array($this, 'save_meta_boxes'), 10, 1);
		add_action('add_meta_boxes', array($this, 'add_metabox'), 5);
		add_action('wp_ajax_bw_osm_save_geotag', array($this, 'save_geotag'));
		add_action('wp_ajax_bw_osm_delete_geotag', array($this, 'delete_geotag'));
	}


	public function add_ship_fields()
	{
		return array(
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_loa',
				'label'       => __('L.O.A', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Length over all in meters.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('L.O.A', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '1000',
				'step' 		  => '.01',
				'unit' 		  => 'm'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_lwl',
				'label'       => __('L.W.L', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Length at the waterline.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('L.W.L', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '1000',
				'step' 		  => '.01'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_beam',
				'label'       => __('Beam', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Beam of the ship in meters.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('Beam', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '100',
				'step' 		  => '.01',
				'unit' 		  => 'm'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_draft',
				'label'       => __('Draft', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Draft of the ship in meters.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('Draft', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '50',
				'step' 		  => '.01',
				'unit' 		  => 'm'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_mainsail_area',
				'label'       => __('Mainsail', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Normal mainsail area of the ship in square meters.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('Mainsail', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '5000',
				'step' 		  => '.01',
				'unit'		  => 'm2'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_jib_area',
				'label'       => __('Jib', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Normal jib area of the ship in square meters.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('Jib', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '5000',
				'step' 		  => '.01',
				'unit'		  => 'm2'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_fresh_water_tank',
				'label'       => __('Fresh water tank', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Size of the water tank in liters.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('Fresh water tank', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '10000',
				'step' 		  => '.01',
				'unit'		  => 'l'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_fuel_tank',
				'label'       => __('Fuel tank', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Size of the fuel tank in liters.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('Fuel tank', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '10000',
				'step' 		  => '.01',
				'unit'		  => 'l'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_displacement',
				'label'       => __('Displacement', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Displacement in kilogram.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('Displacement', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '100000',
				'step' 		  => '.01',
				'unit'		  => 'kg'
			),
			array(
				'metabox' 	  => 'bw_osm_ship_meta',
				'id'          => 'bw_ship_ballast',
				'label'       => __('Ballast', Blauwasser_OSM_Integration::$textdomain),
				'description' => __('Ballast in kilogram.', Blauwasser_OSM_Integration::$textdomain),
				'type'        => 'number',
				'default'     => '',
				'placeholder' => __('Ballast', Blauwasser_OSM_Integration::$textdomain),
				'min'		  => '0',
				'max' 		  => '100000',
				'step' 		  => '.01',
				'unit'		  => 'kg'
			),

		);
	}

	public function save_geotag()
	{
		check_ajax_referer('geotag_nonce');
		$geotag = sanitize_text_field($_POST['geotag']);
		$postId = sanitize_text_field($_POST['postId']);

		$result = update_post_meta($postId, "OSM_geo_data", $geotag);

		wp_send_json(array(
			'geotag' => $geotag,
			'result' => $result
		));
	}

	public function delete_geotag()
	{
		check_ajax_referer('geotag_nonce');
		$postId = sanitize_text_field($_POST['postId']);
		$result = delete_post_meta($postId, "OSM_geo_data");

		wp_send_json(array(
			'result' => $result
		));
	}

	public function add_metabox()
	{
		// OSM
		add_meta_box(
			'blauwasser-osm-metabox',
			__('Blauwasser Map'),
			array($this, 'show_osm_metabox'),
			'post',
			'normal',
			'default'
		);
	}

	public function show_osm_metabox()
	{
		global $post;

		$elementId = 'bw-osm-map-admin';

		$latlon = get_post_meta($post->ID, "OSM_geo_data", true);
		if (!empty($latlon)) {
			list($lat, $lon) = explode(',', $latlon);
			$center = array($lon, $lat);
		} else {
			$center = $this->parent->defaultSettings['center'];
		}


		// Svelte App
		echo '<div id="bw-osm-svelte"></div>';

		$data = array(
			'center' => $center,
			'nonce' => wp_create_nonce('geotag_nonce')
		);

		wp_enqueue_style(Blauwasser_OSM_Integration::$token . '-map-admin');
		wp_register_script($elementId, false, array(Blauwasser_OSM_Integration::$token . '-map-admin', Blauwasser_OSM_Integration::$token . '-settings'), false, true);
		wp_enqueue_script($elementId);
		$script = 'window.addEventListener("load", function () { bwOsm && bwOsm.showAdminMap(' . json_encode($data) . '); });';
		wp_add_inline_script($elementId, $script, 'after');
	}

	/**
	 * Generate HTML for displaying fields.
	 *
	 * @param  array   $data Data array.
	 * @param  object  $post Post object.
	 * @param  boolean $echo  Whether to echo the field HTML or return it.
	 * @return string
	 */
	public function display_field($data = array(), $post = null, $echo = true)
	{

		// Get field info.
		if (isset($data['field'])) {
			$field = $data['field'];
		} else {
			$field = $data;
		}

		// Check for prefix on option name.
		$option_name = '';
		if (isset($data['prefix'])) {
			$option_name = $data['prefix'];
		}

		// Get saved data.
		$data = '';
		if ($post) {
			// Get saved field data.
			$option_name .= $field['id'];
			$option       = get_post_meta($post->ID, $field['id'], true);

			// Get data to display in field.
			if (isset($option)) {
				$data = $option;
			}
		} else {

			// Get saved option.
			$option_name .= $field['id'];
			$option       = get_option($option_name);

			// Get data to display in field.
			if (isset($option)) {
				$data = $option;
			}
		}

		// Show default data if no option saved and default is supplied.
		if (false === $data && isset($field['default'])) {
			$data = $field['default'];
		} elseif (false === $data) {
			$data = '';
		}

		$html = '';

		switch ($field['type']) {

			case 'text':
			case 'url':
			case 'email':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="text" class="td-input-text-post-settings" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . esc_attr($data) . '" />' . "\n";
				break;

			case 'password':
			case 'number':
			case 'hidden':
				$min = '';
				if (isset($field['min'])) {
					$min = ' min="' . esc_attr($field['min']) . '"';
				}

				$max = '';
				if (isset($field['max'])) {
					$max = ' max="' . esc_attr($field['max']) . '"';
				}
				$step = '';
				if (isset($field['step'])) {
					$step = ' step="' . esc_attr($field['step']) . '"';
				}
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" class="td-input-text-post-settings" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . esc_attr($data) . '"' . $min . ' ' . $max . ' ' . $step . '/>' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="text" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="" />' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr($field['id']) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '">' . $data . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ($data && 'on' === $data) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . esc_attr($field['type']) . '" name="' . esc_attr($option_name) . '" ' . $checked . '/>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ($field['options'] as $k => $v) {
					$checked = false;
					if (in_array($k, (array) $data, true)) {
						$checked = true;
					}
					$html .= '<p><label for="' . esc_attr($field['id'] . '_' . $k) . '" class="checkbox_multi"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label></p> ';
				}
				break;

			case 'radio':
				foreach ($field['options'] as $k => $v) {
					$checked = false;
					if ($k === $data) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr($option_name) . '" id="' . esc_attr($field['id']) . '">';
				foreach ($field['options'] as $k => $v) {
					$selected = false;
					if ($k === $data) {
						$selected = true;
					}
					$html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr($option_name) . '[]" id="' . esc_attr($field['id']) . '" multiple="multiple">';
				foreach ($field['options'] as $k => $v) {
					$selected = false;
					if (in_array($k, (array) $data, true)) {
						$selected = true;
					}
					$html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'image':
				$image_thumb = '';
				if ($data) {
					$image_thumb = wp_get_attachment_thumb_url($data);
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __('Upload an image', 'blauwasser-osm-integration') . '" data-uploader_button_text="' . __('Use image', 'blauwasser-osm-integration') . '" class="image_upload_button button" value="' . __('Upload new image', 'blauwasser-osm-integration') . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="' . __('Remove image', 'blauwasser-osm-integration') . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
				break;

			case 'color':
				//phpcs:disable
?><div class="color-picker" style="position:relative;">
					<input type="text" name="<?php esc_attr_e($option_name); ?>" class="color" value="<?php esc_attr_e($data); ?>" />
					<div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
				</div>
<?php
				//phpcs:enable
				break;

			case 'editor':
				wp_editor(
					$data,
					$option_name,
					array(
						'textarea_name' => $option_name,
					)
				);
				break;
		}

		switch ($field['type']) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
				break;

			default:
				if (!$post) {
					$html .= '<label for="' . esc_attr($field['id']) . '">' . "\n";
				}

				$html .= '<span class="td-page-o-info">' . $field['description'] . '</span>' . "\n";

				if (!$post) {
					$html .= '</label>' . "\n";
				}
				break;
		}

		if (!$echo) {
			return $html;
		}

		echo $html; //phpcs:ignore

	}

	/**
	 * Validate form field
	 *
	 * @param  string $data Submitted value.
	 * @param  string $type Type of field to validate.
	 * @return string       Validated value
	 */
	public function validate_field($data = '', $type = 'text')
	{

		switch ($type) {
			case 'text':
				$data = esc_attr($data);
				break;
			case 'url':
				$data = esc_url($data);
				break;
			case 'email':
				$data = is_email($data);
				break;
		}

		return $data;
	}

	/**
	 * Add meta box to the dashboard.
	 *
	 * @param string $id            Unique ID for metabox.
	 * @param string $title         Display title of metabox.
	 * @param array  $post_types    Post types to which this metabox applies.
	 * @param string $context       Context in which to display this metabox ('advanced' or 'side').
	 * @param string $priority      Priority of this metabox ('default', 'low' or 'high').
	 * @param array  $callback_args Any axtra arguments that will be passed to the display function for this metabox.
	 * @return void
	 */
	public function add_meta_box($id = '', $title = '', $post_types = array(), $context = 'advanced', $priority = 'default', $callback_args = null)
	{

		// Get post type(s).
		if (!is_array($post_types)) {
			$post_types = array($post_types);
		}

		// Generate each metabox.
		foreach ($post_types as $post_type) {
			add_meta_box($id, $title, array($this, 'meta_box_content'), $post_type, $context, $priority, $callback_args);
		}
	}


	/**
	 * Display metabox content
	 *
	 * @param  object $post Post object.
	 * @param  array  $args Arguments unique to this metabox.
	 * @return void
	 */
	public function meta_box_content($post, $args)
	{

		$fields = apply_filters($post->post_type . '_custom_fields', array(), $post->post_type);

		if (!is_array($fields) || 0 === count($fields)) {
			return;
		}

		wp_enqueue_style(Blauwasser_OSM_Integration::$token . '-map');

		echo '<div class="td-meta-box-inside">' . "\n";
		echo '<div class="td-page-option-panel td-post-option-ship td-page-option-panel-active">' . "\n";

		foreach ($fields as $field) {

			if (!isset($field['metabox'])) {
				continue;
			}

			if (!is_array($field['metabox'])) {
				$field['metabox'] = array($field['metabox']);
			}

			if (in_array($args['id'], $field['metabox'], true)) {
				$this->display_meta_box_field($field, $post);
			}
		}

		echo '</div>' . "\n";
		echo '</div>' . "\n";
	}

	/**
	 * Dispay field in metabox
	 *
	 * @param  array  $field Field data.
	 * @param  object $post  Post object.
	 * @return void
	 */
	public function display_meta_box_field($field = array(), $post = null)
	{

		if (!is_array($field) || 0 === count($field)) {
			return;
		}

		$out = '<div class="td-meta-box-row">';
		$out .= '<label class="td-page-o-custom-label" for="' . $field['id'] . '">' . $field['label'] . '</label>' . $this->display_field($field, $post, false) . "\n";
		$out .= '</div>';

		echo $out; //phpcs:ignore
	}

	/**
	 * Save metabox fields.
	 *
	 * @param  integer $post_id Post ID.
	 * @return void
	 */
	public function save_meta_boxes($post_id = 0)
	{

		if (!$post_id) {
			return;
		}

		$post_type = get_post_type($post_id);

		$fields = apply_filters($post_type . '_custom_fields', array(), $post_type);

		if (!is_array($fields) || 0 === count($fields)) {
			return;
		}

		foreach ($fields as $field) {
			if (isset($_REQUEST[$field['id']])) { //phpcs:ignore
				update_post_meta($post_id, $field['id'], $this->validate_field($_REQUEST[$field['id']], $field['type'])); //phpcs:ignore
			} else {
				update_post_meta($post_id, $field['id'], '');
			}
		}
	}
}
