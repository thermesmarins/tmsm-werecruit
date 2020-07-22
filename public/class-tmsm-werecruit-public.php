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



		echo '<div id="tmsm-werecruit-container">';
		$this->offers_cards();
		echo '</div>';
	}

	/**
	 * Print Job Table
	 *
	 */
	private function offers_cards(){



		$offers = get_option($this->plugin_name . '-offers');


		if(!empty($offers)){


			echo '<p>'.sprintf( esc_html( _n( '%d job offers', '%d job offers', count($offers), 'tmsm-werecruit'  ) ), count($offers) ).'</p>';

			foreach($offers as $offer){

				if(class_exists('\Elementor\Plugin')){
					$heading_widget = \Elementor\Plugin::instance()->elements_manager->create_element_instance(
						[
							'elType' => 'widget',
							'widgetType' => 'call-to-action',
							'id' => 'joboffer-',
							'settings' => [
								'title' => $offer->title,
								'url' => $offer->url,
								'link' => [
									'url' => $offer->url,
									'is_external' => '',
									'nofollow' => '',
									'custom_attributes' => '',
								],
								'image' => [
									'url' => $offer->ribbonAssetUrl,
								],
								'bg_image' => [
									'url' => $offer->ribbonAssetUrl,
								],

								'description' => '
							'.'<span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-clipboard"></i> '.$offer->type.'</span>
							'.'<span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-calendar-alt"></i> '.$offer->contract.'</span>
							'.'<span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-map-marker-alt"></i> '.$offer->addressCity.'</span>
							'.'<span class="tmsm-werecruit-joboffer-attribute"><i aria-hidden="true" class="fas fa-business-time"></i> '.$offer->company.'</span>
							',
								'button' => __('Apply','tmsm-werecruit'),
								'skin' => 'classic',
								'layout' => 'left',
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
					$heading_widget->print_element();
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

		if( empty($response) && defined( 'WP_DEBUG' ) && WP_DEBUG ){
			error_log('WeRecruit empty response');
			return;
		}
		$response_json = json_decode($response['body']);

		if( empty($response['body']) && defined( 'WP_DEBUG' ) && defined( 'WP_DEBUG' ) && WP_DEBUG ){
			error_log('WeRecruit empty response body');
			return;
		}

		if( (empty($response_json->isSuccessful) ||  $response_json->isSuccessful != 1) && defined( 'WP_DEBUG' ) && WP_DEBUG ){
			error_log('WeRecruit response not successful');
			return;
		}
		if(!is_array($response_json->result) && defined( 'WP_DEBUG' ) && WP_DEBUG ){
			error_log('WeRecruit response results not an array');
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
			'sectors' => $sectors,
			'jobtypes' => $jobtypes,
			'types' => $types,
			'contracts' => $contracts,
			'cities' => $cities,
			'companies' => $companies,
		];
		update_option('tmsm-werecruit-filters', $filters);
		update_option('tmsm-werecruit-offers', $offers);

	}


}
