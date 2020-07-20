<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Werecruit
 * @subpackage Tmsm_Werecruit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tmsm_Werecruit
 * @subpackage Tmsm_Werecruit/admin
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Werecruit_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin options.
	 *
	 * @since 		1.0.0
	 * @access 		private
	 * @var 		string 			$options    The plugin options.
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$this->set_options();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-werecruit-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-werecruit-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Health Check Test
	 *
	 * @param $tests
	 *
	 * @return mixed
	 */
	function test_cron_schedule_exists( $tests ) {
		$tests['direct']['tmsm_werecruit'] = array(
			'label' => __('TMSM WeRecruit Cron Schedule Exists', 'tmsm-werecruit'),
			'test'  => 'tmsm_werecruit_test_schedule_exists',
		);
		return $tests;
	}

	/**
	 * Add item to the admin menu.
	 *
	 * @uses add_dashboard_page()
	 * @uses __()
	 *
	 * @return void
	 */
	public function action_admin_menu() {
		$critical_issues = 0;
		$issue_counts    = get_transient( 'health-check-site-status-result' );

		if ( false !== $issue_counts ) {
			$issue_counts = json_decode( $issue_counts );

			$critical_issues = absint( $issue_counts->critical );
		}

		$critical_count = sprintf(
			'<span class="update-plugins count-%d"><span class="update-count">%s</span></span>',
			esc_attr( $critical_issues ),
			sprintf(
				'%d<span class="screen-reader-text"> %s</span>',
				esc_html( $critical_issues ),
				esc_html_x( 'Critical issues', 'Issue counter label for the admin menu', 'health-check' )
			)
		);

		$menu_title = __( 'Site Health' ) . ' '.( ! $issue_counts || $critical_issues < 1 ? '' : $critical_count );

		remove_submenu_page( 'tools.php', 'site-health.php' );

		add_submenu_page(
			'tools.php',
			__( 'Site Health' ),
			$menu_title,
			'view_site_health_checks',
			'site-health.php'
		);
	}


	/**
	 * Register the Settings page.
	 *
	 * @since    1.0.0
	 */
	public function options_page_menu() {
		add_options_page( __('WeRecruit', 'tmsm-werecruit'), __('WeRecruit', 'tmsm-werecruit'), 'manage_options', $this->plugin_name.'-settings', array($this, 'options_page_display'));

	}

	/**
	 * Plugin Settings Link on plugin page
	 *
	 * @since 		1.0.0
	 * @return 		mixed 			The settings field
	 */
	function settings_link( $links ) {
		$setting_link = array(
			'<a href="' . admin_url( 'options-general.php?page='.$this->plugin_name.'-settings' ) . '">'.__('Settings', 'tmsm-werecruit').'</a>',
		);
		return array_merge( $setting_link, $links );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function options_page_display() {
		include_once( 'partials/' . $this->plugin_name . '-admin-options-page.php' );
	}

	/**
	 * Creates a settings section
	 *
	 * @since 		1.0.0
	 * @param 		array 		$params 		Array of parameters for the section
	 * @return 		mixed 						The settings section
	 */
	public function section_api( $params ) {
		include_once( plugin_dir_path( __FILE__ ) . 'partials/'. $this->plugin_name.'-admin-section-api.php' );
	}

	/**
	 * Creates a settings section
	 *
	 * @since 		1.0.0
	 * @param 		array 		$params 		Array of parameters for the section
	 * @return 		mixed 						The settings section
	 */
	public function section_filters( $params ) {
		include_once( plugin_dir_path( __FILE__ ) . 'partials/'. $this->plugin_name.'-admin-section-filters.php' );
	}

	/**
	 * Creates a settings section
	 *
	 * @since 		1.0.0
	 * @param 		array 		$params 		Array of parameters for the section
	 * @return 		mixed 						The settings section
	 */
	public function section_desc( $params ) {
		include_once( plugin_dir_path( __FILE__ ) . 'partials/'. $this->plugin_name.'-admin-section-desc.php' );
	}

	/**
	 * Registers settings fields with WordPress
	 */
	public function register_fields() {

		add_settings_field(
			'apikey',
			esc_html__( 'API Key', 'tmsm-werecruit' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '-api',
			array(
				//'description' 	=> 'This message displays on the page if no job postings are found.',
				'id' => 'apikey',
				//'value' 		=> 'Thank you for your interest! There are no job openings at this time.',
			)
		);

/*
		add_settings_field(
			'groupid',
			esc_html__( 'Group ID', 'tmsm-werecruit' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '-filters',
			array(
				'id' => 'groupid',
			)
		);

		add_settings_field(
			'hotelid',
			esc_html__( 'Hotel ID', 'tmsm-werecruit' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '-filters',
			array(
				'id' => 'hotelid',
			)
		);

		add_settings_field(
			'roomids',
			esc_html__( 'Room IDs', 'tmsm-werecruit' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '-filters',
			array(
				'id'          => 'roomids',
				'description' => esc_html__( 'Separated by comma.', 'tmsm-werecruit' ),
			)
		);

		add_settings_field(
			'accommodationrateids',
			esc_html__( 'Accommodation Rate IDs', 'tmsm-werecruit' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '-filters',
			array(
				'id'          => 'accommodationrateids',
				'description' => esc_html__( 'Separated by comma.', 'tmsm-werecruit' ),
			)
		);

		add_settings_field(
			'otarateids',
			esc_html__( 'OTA Rate IDs', 'tmsm-werecruit' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '-filters',
			array(
				'id'          => 'otarateids',
				'description' => esc_html__( 'Separated by comma.', 'tmsm-werecruit' ),
			)
		);

		add_settings_field(
			'currency',
			esc_html__( 'Currency (ISO 4217 code)', 'tmsm-werecruit' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '-filters',
			array(
				'id' => 'currency',
			)
		);

		add_settings_field(
			'engine',
			esc_html__( 'Engine (format: name/id/)', 'tmsm-werecruit' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '-filters',
			array(
				'id' => 'engine',
			)
		);

		add_settings_field(
			'intro',
			esc_html__( 'Intro', 'tmsm-werecruit' ),
			array( $this, 'field_textarea' ),
			$this->plugin_name,
			$this->plugin_name . '-desc',
			array(
				'id' => 'intro',
				'description' => esc_html__( 'Html accepted. ', 'tmsm-werecruit' ),
			)
		);

		add_settings_field(
			'outro',
			esc_html__( 'Outro', 'tmsm-werecruit' ),
			array( $this, 'field_textarea' ),
			$this->plugin_name,
			$this->plugin_name . '-desc',
			array(
				'id' => 'outro',
				'description' => esc_html__( 'Html accepted. ', 'tmsm-werecruit' ),
			)
		);

		add_settings_field(
			'bookbuttonlabel',
			esc_html__( 'Book Button Label', 'tmsm-werecruit' ),
			array( $this, 'field_textarea' ),
			$this->plugin_name,
			$this->plugin_name . '-desc',
			array(
				'id' => 'bookbuttonlabel',
				'description' => esc_html__( 'Html accepted. ', 'tmsm-werecruit' ),
			)
		);

		add_settings_field(
			'yearbestpricelabel',
			esc_html__( 'Year Best Price Label', 'tmsm-werecruit' ),
			array( $this, 'field_textarea' ),
			$this->plugin_name,
			$this->plugin_name . '-desc',
			array(
				'id' => 'yearbestpricelabel',
				'description' => esc_html__( 'Html accepted. Use % as placeholder for price.', 'tmsm-werecruit' ),
			)
		);

		add_settings_field(
			'selecteddatepricelabel',
			esc_html__( 'Accommodation Price Label', 'tmsm-werecruit' ),
			array( $this, 'field_textarea' ),
			$this->plugin_name,
			$this->plugin_name . '-desc',
			array(
				'id' => 'selecteddatepricelabel',
				'description' => esc_html__( 'Html accepted. Use % as placeholder for price.', 'tmsm-werecruit' ),
			)
		);

		add_settings_field(
			'otacomparelabel',
			esc_html__( 'OTA Compare Label', 'tmsm-werecruit' ),
			array( $this, 'field_textarea' ),
			$this->plugin_name,
			$this->plugin_name . '-desc',
			array(
				'id' => 'otacomparelabel',
				'description' => esc_html__( 'Html accepted. Use % as placeholder for price.', 'tmsm-werecruit' ),
			)
		);*/

	}

	/**
	 * Registers settings sections with WordPress
	 */
	public function register_sections() {
		// add_settings_section( $id, $title, $callback, $menu_slug );
		add_settings_section(
			$this->plugin_name . '-api',
			esc_html__( 'API', 'tmsm-werecruit' ),
			array( $this, 'section_api' ),
			$this->plugin_name
		);

		/*
		add_settings_section(
			$this->plugin_name . '-filters',
			esc_html__( 'Filters', 'tmsm-werecruit' ),
			array( $this, 'section_filters' ),
			$this->plugin_name
		);

		add_settings_section(
			$this->plugin_name . '-desc',
			esc_html__( 'Descriptions', 'tmsm-werecruit' ),
			array( $this, 'section_desc' ),
			$this->plugin_name
		);*/

	}

	/**
	 * Registers plugin settings
	 *
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function register_settings() {
		// register_setting( $option_group, $option_name, $sanitize_callback );
		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-options',
			array( $this, 'validate_options' )
		);
	}

	/**
	 * Sanitize fields
	 *
	 * @param $type
	 * @param $data
	 *
	 * @return string|void
	 */
	private function sanitizer( $type, $data ) {
		if ( empty( $type ) ) { return; }
		if ( empty( $data ) ) { return; }
		$return 	= '';
		$sanitizer 	= new Tmsm_Werecruit_Sanitize();
		$sanitizer->set_data( $data );
		$sanitizer->set_type( $type );
		$return = $sanitizer->clean();
		unset( $sanitizer );
		return $return;
	}

	/**
	 * Sets the class variable $options
	 */
	private function set_options() {
		$this->options = get_option( $this->plugin_name . '-options' );
	}

	/**
	 * Validates saved options
	 *
	 * @since 		1.0.0
	 * @param 		array 		$input 			array of submitted plugin options
	 * @return 		array 						array of validated plugin options
	 */
	public function validate_options( $input ) {
		//wp_die( print_r( $input ) );
		$valid 		= array();
		$options 	= $this->get_options_list();
		foreach ( $options as $option ) {
			$name = $option[0];
			$type = $option[1];

			$valid[$option[0]] = $this->sanitizer( $type, $input[$name] );

		}
		return $valid;
	}

	/**
	 * Creates a checkbox field
	 *
	 * @param 	array 		$args 			The arguments for the field
	 * @return 	string 						The HTML field
	 */
	public function field_checkbox( $args ) {
		$defaults['class'] 			= '';
		$defaults['description'] 	= '';
		$defaults['label'] 			= '';
		$defaults['name'] 			= $this->plugin_name . '-options[' . $args['id'] . ']';
		$defaults['value'] 			= 0;
		apply_filters( $this->plugin_name . '-field-checkbox-options-defaults', $defaults );
		$atts = wp_parse_args( $args, $defaults );
		if ( ! empty( $this->options[$atts['id']] ) ) {
			$atts['value'] = $this->options[$atts['id']];
		}
		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-field-checkbox.php' );
	}

	/**
	 * Creates an editor field
	 *
	 * NOTE: ID must only be lowercase letter, no spaces, dashes, or underscores.
	 *
	 * @param 	array 		$args 			The arguments for the field
	 * @return 	string 						The HTML field
	 */
	public function field_editor( $args ) {
		$defaults['description'] 	= '';
		$defaults['settings'] 		= array( 'textarea_name' => $this->plugin_name . '-options[' . $args['id'] . ']' );
		$defaults['value'] 			= '';
		apply_filters( $this->plugin_name . '-field-editor-options-defaults', $defaults );
		$atts = wp_parse_args( $args, $defaults );
		if ( ! empty( $this->options[$atts['id']] ) ) {
			$atts['value'] = $this->options[$atts['id']];
		}
		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-field-editor.php' );
	}

	/**
	 * Creates a set of radios field
	 *
	 * @param 	array 		$args 			The arguments for the field
	 * @return 	string 						The HTML field
	 */
	public function field_radios( $args ) {
		$defaults['class'] 			= '';
		$defaults['description'] 	= '';
		$defaults['label'] 			= '';
		$defaults['name'] 			= $this->plugin_name . '-options[' . $args['id'] . ']';
		$defaults['value'] 			= 0;
		apply_filters( $this->plugin_name . '-field-radios-options-defaults', $defaults );
		$atts = wp_parse_args( $args, $defaults );
		if ( ! empty( $this->options[$atts['id']] ) ) {
			$atts['value'] = $this->options[$atts['id']];
		}
		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-field-radios.php' );
	}

	public function field_repeater( $args ) {
		$defaults['class'] 			= 'repeater';
		$defaults['fields'] 		= array();
		$defaults['id'] 			= '';
		$defaults['label-add'] 		= 'Add Item';
		$defaults['label-edit'] 	= 'Edit Item';
		$defaults['label-header'] 	= 'Item Name';
		$defaults['label-remove'] 	= 'Remove Item';
		$defaults['title-field'] 	= '';
		/*
				$defaults['name'] 			= $this->plugin_name . '-options[' . $args['id'] . ']';
		*/
		apply_filters( $this->plugin_name . '-field-repeater-options-defaults', $defaults );
		$setatts 	= wp_parse_args( $args, $defaults );
		$count 		= 1;
		$repeater 	= array();
		if ( ! empty( $this->options[$setatts['id']] ) ) {
			$repeater = maybe_unserialize( $this->options[$setatts['id']][0] );
		}
		if ( ! empty( $repeater ) ) {
			$count = count( $repeater );
		}
		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-field-repeater.php' );
	}

	/**
	 * Creates a select field
	 *
	 * Note: label is blank since its created in the Settings API
	 *
	 * @param 	array 		$args 			The arguments for the field
	 * @return 	string 						The HTML field
	 */
	public function field_select( $args ) {
		$defaults['aria'] 			= '';
		$defaults['blank'] 			= '';
		$defaults['class'] 			= 'widefat';
		$defaults['context'] 		= '';
		$defaults['description'] 	= '';
		$defaults['label'] 			= '';
		$defaults['name'] 			= $this->plugin_name . '-options[' . $args['id'] . ']';
		$defaults['selections'] 	= array();
		$defaults['value'] 			= '';
		apply_filters( $this->plugin_name . '-field-select-options-defaults', $defaults );
		$atts = wp_parse_args( $args, $defaults );
		if ( ! empty( $this->options[$atts['id']] ) ) {
			$atts['value'] = $this->options[$atts['id']];
		}
		if ( empty( $atts['aria'] ) && ! empty( $atts['description'] ) ) {
			$atts['aria'] = $atts['description'];
		} elseif ( empty( $atts['aria'] ) && ! empty( $atts['label'] ) ) {
			$atts['aria'] = $atts['label'];
		}
		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-field-select.php' );
	}

	/**
	 * Creates a text field
	 *
	 * @param 	array 		$args 			The arguments for the field
	 * @return 	string 						The HTML field
	 */
	public function field_text( $args ) {
		$defaults['class'] 			= 'regular-text';
		$defaults['description'] 	= '';
		$defaults['label'] 			= '';
		$defaults['name'] 			= $this->plugin_name . '-options[' . $args['id'] . ']';
		$defaults['placeholder'] 	= '';
		$defaults['type'] 			= 'text';
		$defaults['value'] 			= '';
		apply_filters( $this->plugin_name . '-field-text-options-defaults', $defaults );
		$atts = wp_parse_args( $args, $defaults );
		if ( ! empty( $this->options[$atts['id']] ) ) {
			$atts['value'] = $this->options[$atts['id']];
		}
		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-field-text.php' );
	}

	/**
	 * Creates a textarea field
	 *
	 * @param 	array 		$args 			The arguments for the field
	 * @return 	string 						The HTML field
	 */
	public function field_textarea( $args ) {
		$defaults['class'] 			= 'large-text';
		$defaults['cols'] 			= 50;
		$defaults['context'] 		= '';
		$defaults['description'] 	= '';
		$defaults['label'] 			= '';
		$defaults['name'] 			= $this->plugin_name . '-options[' . $args['id'] . ']';
		$defaults['rows'] 			= 10;
		$defaults['value'] 			= '';
		apply_filters( $this->plugin_name . '-field-textarea-options-defaults', $defaults );
		$atts = wp_parse_args( $args, $defaults );
		if ( ! empty( $this->options[$atts['id']] ) ) {
			$atts['value'] = $this->options[$atts['id']];
		}
		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-admin-field-textarea.php' );
	}

	/**
	 * Returns an array of options names, fields types, and default values
	 *
	 * @return 		array 			An array of options
	 */
	public static function get_options_list() {
		$options   = array();
		$options[] = array( 'apikey', 'text', '' );
		$options[] = array( 'consumersecret', 'text', '' );
		$options[] = array( 'accesstoken', 'text', '' );
		$options[] = array( 'tokensecret', 'text', '' );
		$options[] = array( 'groupid', 'text', '' );
		$options[] = array( 'hotelid', 'text', '' );
		$options[] = array( 'roomids', 'text', '' );
		$options[] = array( 'accommodationrateids', 'text', '' );
		$options[] = array( 'otarateids', 'text', '' );
		$options[] = array( 'currency', 'text', '' );
		$options[] = array( 'engine', 'text', '' );
		$options[] = array( 'intro', 'textarea', '' );
		$options[] = array( 'outro', 'textarea', '' );
		$options[] = array( 'bookbuttonlabel', 'textarea', '' );
		$options[] = array( 'selecteddatepricelabel', 'textarea', _x( 'From %', 'money format', 'tmsm-werecruit' ) );
		$options[] = array( 'yearbestpricelabel', 'textarea', _x('From %', 'money format', 'tmsm-werecruit') );
		$options[] = array( 'otacomparelabel', 'textarea', __( 'Compare with the OTA: %', 'tmsm-werecruit' ) );

		return $options;
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 *
	 * @since  1.0.0
	 */
	public function customize_register( $wp_customize ) {

		/*
		$wp_customize->add_section('tmsm_werecruit', array(
			'title'    => esc_html__('WeRecruit', 'tmsm-werecruit'),
			'description' => '',
			'priority' => 180,
		));

		$wp_customize->add_setting( 'tmsm_werecruit_calendar_selectedcolor', array(
			'transport' 			=> 'postMessage',
			'default'           	=> '#333333',
			'sanitize_callback' 	=> 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tmsm_werecruit_calendar_selectedcolor', array(
			'label'	   				=> esc_html__( 'Calendar color for selected date', 'tmsm-werecruit' ),
			'section'  				=> 'tmsm_werecruit',
			'settings' 				=> 'tmsm_werecruit_calendar_selectedcolor',
			'priority' 				=> 10,
		) ) );

		$wp_customize->add_setting( 'tmsm_werecruit_calendar_rangecolor', array(
			'transport' 			=> 'postMessage',
			'default'           	=> '#808080',
			'sanitize_callback' 	=> 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tmsm_werecruit_calendar_rangecolor', array(
			'label'	   				=> esc_html__( 'Calendar color for range date', 'tmsm-werecruit' ),
			'section'  				=> 'tmsm_werecruit',
			'settings' 				=> 'tmsm_werecruit_calendar_rangecolor',
			'priority' 				=> 10,
		) ) );


		$wp_customize->add_setting( 'tmsm_werecruit_calendar_bestpricecolor', array(
			'transport' 			=> 'postMessage',
			'default'           	=> '#0f9d58',
			'sanitize_callback' 	=> 'sanitize_hex_color',
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tmsm_werecruit_calendar_bestpricecolor', array(
			'label'	   				=> esc_html__( 'Calendar best price color', 'tmsm-werecruit' ),
			'section'  				=> 'tmsm_werecruit',
			'settings' 				=> 'tmsm_werecruit_calendar_bestpricecolor',
			'priority' 				=> 10,
		) ) );
		*/
	}
}

/**
 * Health Check Test
 * @return array
 */
function tmsm_werecruit_test_schedule_exists() {
	$result = array(
		'label'       => __('TMSM WeRecruit Cron Schedule Exists', 'tmsm-werecruit'),
		'status'      => 'good',
		'badge'       => array(
			'label' => __( 'Performance' ),
			'color' => 'green',
		),
		'description' => sprintf(
			'<p>%s</p>',
			__( 'Cron Schedule is not fired', 'tmsm-werecruit' )
		),
		'actions'     => '',
		'test'        => 'tmsm_werecruit',
	);

	if ( ! wp_next_scheduled( 'tmsmwerecruit_cronaction' ) ) {
		$result['status'] = 'critical';
		$result['badge']['color'] = 'red';
		$result['label'] = __('TMSM WeRecruit Cron Schedule is not added', 'tmsm-werecruit');
		$result['description'] = sprintf(
			'<p>%s</p>',
			__('Cron schedule is not fired, prices will not update from Availpro.', 'tmsm-werecruit')
		);
		$result['actions'] .= sprintf(
			'<p><a href="%s">%s</a></p>',
			esc_url( admin_url( 'plugins.php' ) ),
			__( 'Disable and enable again the plugin', 'tmsm-werecruit' )
		);
	}

	return $result;
}