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

		// Styling vars
		$tmsm_werecruit_calendar_selectedcolor 	= get_theme_mod( 'tmsm_werecruit_calendar_selectedcolor', '#333333' );
		$tmsm_werecruit_calendar_rangecolor 	= get_theme_mod( 'tmsm_werecruit_calendar_rangecolor', '#808080' );
		$tmsm_werecruit_calendar_bestpricecolor 	= get_theme_mod( 'tmsm_werecruit_calendar_bestpricecolor', '#0f9d58' );

		// Define css var
		$css 			= '';

		if ( ! empty( $tmsm_werecruit_calendar_rangecolor ) && $tmsm_werecruit_calendar_rangecolor!=='#808080') {
			$css .= '
			#tmsm-werecruit-calendar .table-calendarprices tbody .day.selected .cell {background: '.$tmsm_werecruit_calendar_rangecolor.';}
			#tmsm-werecruit-calendar .table-calendarprices tbody .day.selected-hover .cell { background: '.$tmsm_werecruit_calendar_rangecolor.'; }
			';
		}

		if ( ! empty( $tmsm_werecruit_calendar_selectedcolor ) && $tmsm_werecruit_calendar_selectedcolor!=='#333333') {
			$css .= '
			#tmsm-werecruit-calendar .table-calendarprices tbody .day.selected-begin .cell,
			#tmsm-werecruit-calendar .table-calendarprices tbody .day.selected-end .cell {
			background: '.$tmsm_werecruit_calendar_selectedcolor.';
			}';
		}

		if ( ! empty( $tmsm_werecruit_calendar_rangecolor ) && $tmsm_werecruit_calendar_rangecolor!=='#808080') {
			$css .= '
			#tmsm-werecruit-calendar .table-calendarprices tbody .day.selected-begin .price,
			#tmsm-werecruit-calendar .table-calendarprices tbody .day.selected-end .price {
			color:'.$tmsm_werecruit_calendar_rangecolor.';
			}';
		}

		if ( ! empty( $tmsm_werecruit_calendar_selectedcolor ) && $tmsm_werecruit_calendar_selectedcolor!=='#333333') {
			$css .= '
			#tmsm-werecruit-calendar .table-calendarprices tbody .day.mouseover .cell {
			background: '.$tmsm_werecruit_calendar_selectedcolor.';
			}';
		}

		if ( ! empty( $tmsm_werecruit_calendar_rangecolor ) && $tmsm_werecruit_calendar_rangecolor!=='#808080') {
			$css .= '
			#tmsm-werecruit-calendar .table-calendarprices tbody .day.mouseover .price {
			color: '.$tmsm_werecruit_calendar_rangecolor.';
			}';
		}

		if ( ! empty( $tmsm_werecruit_calendar_bestpricecolor ) && $tmsm_werecruit_calendar_bestpricecolor !== '#0f9d58') {
			$css .= '
			#tmsm-werecruit-calendar .table-calendarprices tbody .day:not(.selected):not(.past):not(.mouseover) .cell[data-lowestprice=\'1\'] .price {
			color: '.$tmsm_werecruit_calendar_bestpricecolor.';
			}
			#tmsm-werecruit-form .tmsm-werecruit-form-legend .legend-item.legend-item-lowestprice:before {
			background: '.$tmsm_werecruit_calendar_bestpricecolor.';
			}
			';

		}

		// Return CSS
		if ( ! empty( $css ) ) {
			$css = '/* Availpro CSS */'. $css;
		}

		wp_add_inline_style( $this->plugin_name, $css );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Scripts
		wp_dequeue_script('moment');
		wp_deregister_script('moment');
		wp_enqueue_script( 'moment', plugin_dir_url( dirname(__FILE__) ) . 'vendor/moment/min/moment.min.js', array( 'jquery' ), $this->version, true );
		if ( function_exists( 'PLL' ) && $language = PLL()->model->get_language( get_locale() ) && pll_current_language() !== 'en')
		{
			$moment_locale = pll_current_language();
			if ( pll_current_language() === 'zh' ) {
				$moment_locale = 'zh-cn';
			}

			wp_enqueue_script( 'moment-'.$moment_locale, plugin_dir_url( dirname(__FILE__) ) . 'vendor/moment/locale/'.$moment_locale.'.js', array( 'jquery' ), $this->version, true );
		}

		wp_enqueue_script( 'clndr', plugin_dir_url( dirname(__FILE__) ) . 'vendor/clndr/clndr.min.js', array( 'jquery', 'moment', 'underscore' ), $this->version, true );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tmsm-werecruit-public.js', array( 'jquery', 'wp-util' ), $this->version, true );


		// Params
		$params = [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'locale'   => $this->get_locale(),
			'security' => wp_create_nonce( 'security' ),
			'i18n'     => [
				//'fromprice'          => _x( 'From', 'price', 'tmsm-werecruit' ),
				//'yearbestpricelabel' => $this->get_option( 'yearbestpricelabel' ),
				//'otacomparelabel' => $this->get_option( 'otacomparelabel' ),
				//'selecteddatepricelabel' => $this->get_option( 'selecteddatepricelabel' ),
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

			/*echo '<table>
			<thead>
			<th>'.__('Title','tmsm-werecruit').'</th>
			<th>'.__('Sectors','tmsm-werecruit').'</th>
			<th>'.__('Category','tmsm-werecruit').'</th>
			<th>'.__('Location','tmsm-werecruit').'</th>
			<th>'.__('Contract Type','tmsm-werecruit').'</th>
			<th>'.__('Rythm','tmsm-werecruit').'</th>
			<th>'.__('Company','tmsm-werecruit').'</th>
			</thead>
			<tbody>
			';*/

			foreach($offers as $offer){

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
							// $offer->sector
							//$offer->jobType
						],
					],
					[]
				);
				$heading_widget->print_element();

				/*$output .= '
					<tr>
						<td><a href="'.$offer->url.'">'.$offer->title.'</a></td>
						<td>'.$offer->sector.'</td>
						<td>'.$offer->jobType.'</td>
						<td>'.$offer->addressCity.'</td>
						
						<td>'.$offer->type.'</td>
						<td>'.$offer->contract.'</td>
						
						<td>'.$offer->company.'</td>
					</tr>
				';*/
			}


			//$output .= '</tbody></table>';
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

		// https://app.werecruit.io/api/externalpostfeed/e358f0c5-bf66-47db-8bfb-23d860438d92?state=published
		$url = 'https://app.werecruit.io/api/externalpostfeed/e358f0c5-bf66-47db-8bfb-23d860438d92?state=published
';
		$headers=[
			'accept' => 'text/plain',
		];

		$response = wp_remote_get($url, $headers);

		error_log('response:');
		error_log(print_r($response, true));
		if( empty($response)){
			error_log('Empty response');
		}
		if( empty($response['body'])){
			error_log('Empty response body');
		}
		if( empty($response['isSuccessful']) ||  $response['isSuccessful'] != true ){
			error_log('Response not successful');
		}

		$response_json = json_decode($response['body']);

		error_log('result number:'.count($response_json->result));

		if(!is_array($response_json->result)){
			error_log('Response results not an array');
		}

		$sectors = []; // Catégorie
		$jobtypes = []; // Type d'emploi
		$types = []; // Type de contract, exemple CDI
		$contracts = []; // Rythme de travail, exemple "Temps plein"
		$cities = []; // Ville, exemple Saint-Malo
		$companies = []; // Entreprise, exemple Hôtel le Nouveau Monde

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

		error_log('offers_count:'.$offers_count);

		error_log('sectors:');
		error_log(print_r($sectors, true));

		error_log('jobtypes:');
		error_log(print_r($jobtypes, true));

		error_log('types:');
		error_log(print_r($types, true));

		error_log('contracts:');
		error_log(print_r($contracts, true));

		error_log('cities:');
		error_log(print_r($cities, true));

		error_log('companies:');
		error_log(print_r($companies, true));

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

		/*$lastmonthchecked = get_option( 'tmsm-werecruit-lastmonthchecked', false );

		update_option('tmsm-werecruit-bestprice-'.$monthtocheck, $dailyplanning_bestprice);


		$bestprice_year = get_option( 'tmsm-werecruit-bestprice-year', false );

		update_option('tmsm-werecruit-bestprice-year', $bestprice_year);


		delete_option( 'tmsm-werecruit-bestprice-'.$today->modify('-1 month')->format('Y-m') );*/
	}


}
