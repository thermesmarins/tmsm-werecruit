<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Werecruit
 * @subpackage Tmsm_Werecruit/public
 */

use Elementor\Plugin;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tmsm_Werecruit
 * @subpackage Tmsm_Werecruit/public
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Werecruit_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Engine URL
	 *
	 * @since 		1.0.0
	 */
	const ENGINE_URL = 'https://www.secure-hotel-booking.com/';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Get locale
	 */
	private function get_locale() {
		return (function_exists('pll_current_language') ? pll_current_language() : substr(get_locale(),0, 2));
	}


	/**
	 * Get option
	 * @param string $option_name
	 *
	 * @return null
	 */
	private function get_option($option_name){

		$options = get_option($this->plugin_name . '-options');

		if(empty($options[$option_name])){
			return null;
		}
		return $options[$option_name];
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tmsm-werecruit-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-werecruit-public.js', array( 'jquery', 'wp-util' ), $this->version, true );

		// Params
		$params = [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'locale'   => $this->get_locale(),
			'security' => wp_create_nonce( 'security' ),
			'i18n'     => [
				'loading'          => __( 'Loading...', 'tmsm-werecruit' ),
				'loadingexplaination'          => __( 'Jobs offers are being refreshed...', 'tmsm-werecruit' ),
			],
			'options'  => [
				//'currency' => $this->get_option( 'currency' ),
			],
		];

		wp_localize_script( $this->plugin_name, 'tmsm_werecruit_params', $params);

	}

	/**
	 * Register the shortcodes
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'tmsm-werecruit-offers', array( $this, 'shortcode_offers') );
	}


	/**
	 * Send an email to admin if the scheduled cron is not defined
	 */
	public function check_cron_schedule_exists(){

		if ( ! wp_next_scheduled( 'tmsmwerecruit_cronaction' ) ) {

			$email = wp_mail(
				get_option( 'admin_email' ),
				wp_specialchars_decode( sprintf( __('TMSM WeRecruit cron is not scheduled on %s', 'tmsm-werecruit'), get_option( 'blogname' ) ) ),
				wp_specialchars_decode( sprintf( __('TMSM WeRecruit cron is not scheduled on %s', 'tmsm-werecruit'), get_option( 'blogname' ) ) )
			);
		}

	}

	/**
	 * Offers shortcode
	 *
	 * @since    1.0.0
	 */
	public function shortcode_offers($atts) {
		$atts = shortcode_atts( array(
			'option' => '',
		), $atts, 'tmsm-werecruit-offers' );

		$filters = get_option($this->plugin_name . '-filters');



		echo '<div id="tmsm-werecruit-container">';

		if(!empty($filters)){

			echo '<form action="'.admin_url('admin-ajax.php').'" method="post" id="tmsm-werecruit-filterform">';

			foreach ($filters as $filter_key => $filter_values){

				$filter_name = '';
				switch ($filter_key){
					case 'sector': $filter_name = __('Activity Sector', 'tmsm-werecruit');break;
					case 'jobType': $filter_name = __('Job Type', 'tmsm-werecruit');break;
					case 'type': $filter_name = __('Contract Type', 'tmsm-werecruit');break;
					case 'contract': $filter_name = __('Rythm', 'tmsm-werecruit');break;
					case 'addressCity': $filter_name = __('Location', 'tmsm-werecruit');break;
					case 'company': $filter_name = __('Company', 'tmsm-werecruit');break;
				}
				if(is_array($filter_values) && $this->get_option($filter_key) === 'yes'){
					echo '<select id="tmsm-werecruit-'.$filter_key.'" title="'.esc_attr($filter_name).'" name="'.esc_attr($filter_key).'">';
					echo '<option value="">'.$filter_name.'</option>';
					foreach ($filter_values as $filter_value){
						echo '<option value="'.$filter_value.'" '.selected($_REQUEST[$filter_key], $filter_value).'>'.$filter_value.'</option>';
					}
					echo '</select>';

				}
			}

			echo '
			<button class="button" id="tmsm-werecruit-submit">'.__('Filter', 'tmsm-werecruit').'</button>
			<input type="hidden" name="action" value="tmsm-werecruit-jobsfilter">
		</form>';

		}


		echo '<div id="tmsm-werecruit-filterresponse">';
		$this->offers_cards($this->jobs_all());
		echo '</div>';

		echo '</div>';
	}


	public function jobs_filter(){

		$jobs = $this->jobs_all();

		$jobs_filtered = [];

		if ( ! empty( $jobs ) && is_array( $jobs ) ) {
			foreach ($jobs as $job){
				$match = true;
				foreach( ['sector', 'jobType', 'type', 'contract', 'addressCity', 'company' ] as $filter_key ){

					if( (!empty($_POST[$filter_key]) && $job->{$filter_key} != stripslashes($_POST[$filter_key]))){
						$match &= false;
					}

				}
				if($match){
					$jobs_filtered[] = $job;
				}
			}

		}

		$this->offers_cards($jobs_filtered);
		die();
	}


	/**
	 * Get All Jobs
	 */
	private function jobs_all(){
		return get_option($this->plugin_name . '-offers');
	}

	/**
	 * Print Job Table
	 *
	 */
	private function offers_cards($jobs){
		global $wpdb;

		if(!empty($jobs)){

			echo '<p id="tmsm-werecruit-loadingexplaination">'.sprintf( esc_html( _n( '%d job offers', '%d job offers', count($jobs), 'tmsm-werecruit'  ) ), count($jobs) ).'</p>';

			foreach($jobs as $offer){

				$image = null;

				if ( ! empty( $offer->company ) ) {
					$image_object = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = %s AND post_type='attachment'", trim('WeRecruit Logo '.$offer->company) ));
					if(!empty($image_object)) {
						$image = wp_get_attachment_url($image_object->ID);
					}

				}

				$image = null; // dont display the image after June 15th 2021

				if(class_exists('\Elementor\Plugin')){
					$widget = Plugin::instance()->elements_manager->create_element_instance(
						[
							'elType' => 'widget',
							'widgetType' => 'call-to-action',
							'id' => 'joboffer-',
							'settings' => [
								'title' => $offer->title,
								'title_tag' => 'h3',
								'url' => $offer->url,
								'link' => [
									'url' => $offer->url,
									'is_external' => '',
									'nofollow' => '',
									'custom_attributes' => '',
								],
								'image' => [
									'url' => $image,
								],
								'bg_image' => [
									'url' => $image,
								],

								'description' => '
							'.'<p><span class="tmsm-werecruit-joboffer-company">'.$offer->company.'</span></p>
							'.'<p><span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-clipboard"></i> '.$offer->type.'</span>
							'.'<span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-calendar-alt"></i> '.$offer->contract.'</span>
							'.'<span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-map-marker-alt"></i> '.$offer->addressCity.'</span></p>
							',
								'button' => __('Apply','tmsm-werecruit'),
								'skin' => 'classic',
								'layout' => 'left',
								'layout_mobile' => 'above',
								'alignment' => 'left',
								'vertical_position' => 'top',
								'image_min_width' => [
									'unit' => 'px',
									'size' => '169',
									'sizes' => [],
								],
								'image_min_height' => [
									'unit' => 'px',
									'size' => '43',
									'sizes' => [],
								],
							],
						],
						[]
					);
					//$widget->print_element();

					$widget = Plugin::instance()->elements_manager->create_element_instance(
						[
							'elType' => 'widget',
							'widgetType' => 'image-box',
							'id' => 'joboffer-'.$offer->id,

							'settings' => [
								'_css_classes' => 'tmsm-werecruit-joboffer-item',
								'title_text' => $offer->title,
								'title_tag' => 'h3',
								'position' => 'left',
								'image_space' => [
									'unit' => 'px',
									'size' => '20',
									'sizes' => [],
								],
								'_padding' => [
									'unit' => 'px',
									'top' => '20',
									'left' => '20',
									'bottom' => '20',
									'right' => '20',
									'isLinked' => 1,
								],
								'_background_background' => 'classic',
								'_background_color' => 'white',

								'image' => [
									'url' => $image,
								],

								'description_text' => '
							'.'<p><span class="tmsm-werecruit-joboffer-company">'.$offer->company.'</span></p>
							'.'<p class="tmsm-werecruit-joboffer-button-wrapper"><a href="'.$offer->url.'" class="elementor-button">'.__('Apply','tmsm-werecruit').'</a></p>
							'.'<p><span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-clipboard"></i> '.$offer->type.'</span>
							'.'<span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-calendar-alt"></i> '.$offer->contract.'</span>
							'.'<span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-map-marker-alt"></i> '.$offer->addressCity.'</span></p>
							',
							],
						],
						[]
					);
					$widget->print_element();
				}

			}

		}
		else{
			echo '<p>'.__('No job offers at the moment','tmsm-werecruit').'</p>';
		}

	}

	/**
	 * Check Availpro Prices
	 */
	public function refresh_data() {

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Function WeRecruit refresh_data()' );
		}

		if(empty($this->get_option('apikey')) && defined( 'WP_DEBUG' ) && WP_DEBUG ){
			error_log('WeRecruit undefined API key');
			return;
		}
		$url = 'https://app.werecruit.io/api/externalpostfeed/'.$this->get_option('apikey').'?state=published';

		$headers=[
			'accept' => 'text/plain',
		];

		$response = wp_remote_get($url, $headers);

		// Check for errors
		if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
			if(defined( 'WP_DEBUG' ) && WP_DEBUG){
				error_log('WeRecruit response error');
			}
			return;
		}

		// Get remote body val
		$body = wp_remote_retrieve_body( $response );

		if( empty($response) ){
			if(defined( 'WP_DEBUG' ) && WP_DEBUG){
				error_log('WeRecruit empty response');
			}
			return;
		}

		$response_json = json_decode($body);

		if( empty($body) ){
			if(defined( 'WP_DEBUG' ) && WP_DEBUG){
				error_log('WeRecruit empty response body');
			}
			return;
		}

		if( (empty($response_json->isSuccessful) ||  $response_json->isSuccessful != 1) ){
			if(defined( 'WP_DEBUG' ) && WP_DEBUG){
				error_log('WeRecruit response not successful');
			}
			return;
		}
		if(!is_array($response_json->result) ){
			if(defined( 'WP_DEBUG' ) && WP_DEBUG){
				error_log('WeRecruit response results not an array');
			}
			return;
		}
		$sectors = []; // Sector
		$jobtypes = []; // Job Type
		$types = []; // Contract Type, example Permanent, Fixed-term
		$contracts = []; // Rythm, example Full Time
		$cities = []; // Cities
		$companies = []; // Companies

		$offers_count = 0;
		$offers = [];

		foreach($response_json->result as $result){

			// Exclude "Spontaneous application"
			if($result->isSpontaneousApplication !== false){
				continue;
			}
			$offers_count++;
			$offers[] = $result;

			if ( ! in_array( $result->sector, $sectors ) && ! empty( $result->sector ) ) {
				$sectors[] = $result->sector;
			}
			if ( ! in_array( $result->jobType, $jobtypes ) && ! empty( $result->jobType ) ) {
				$jobtypes[] = $result->jobType;
			}
			if ( ! in_array( $result->type, $types ) && ! empty( $result->type ) ) {
				$types[] = $result->type;
			}
			if ( ! in_array( $result->contract, $contracts ) && ! empty( $result->contract ) ) {
				$contracts[] = $result->contract;
			}
			if ( ! in_array( $result->addressCity, $cities ) && ! empty( $result->addressCity ) ) {
				$cities[] = $result->addressCity;
			}
			if ( ! in_array( $result->company, $companies ) && ! empty( $result->company ) ) {
				$companies[] = $result->company;
			}


		}

		$filters = [
			'sector' => $sectors,
			'jobType' => $jobtypes,
			'type' => $types,
			'contract' => $contracts,
			'addressCity' => $cities,
			'company' => $companies,
		];
		update_option('tmsm-werecruit-filters', $filters);
		update_option('tmsm-werecruit-offers', $offers);

	}


}
