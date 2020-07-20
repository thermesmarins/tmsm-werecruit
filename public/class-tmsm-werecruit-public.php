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
				'fromprice'          => _x( 'From', 'price', 'tmsm-werecruit' ),
				'yearbestpricelabel' => $this->get_option( 'yearbestpricelabel' ),
				'otacomparelabel' => $this->get_option( 'otacomparelabel' ),
				'selecteddatepricelabel' => $this->get_option( 'selecteddatepricelabel' ),
			],
			'options'  => [
				'currency' => $this->get_option( 'currency' ),
			],
			'data'     => $this->get_options_bestprice(),
		];

		wp_localize_script( $this->plugin_name, 'tmsm_werecruit_params', $params);
	}

	/**
	 * Register the shortcodes
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'tmsm-werecruit-calendar', array( $this, 'calendar_shortcode') );
		add_shortcode( 'tmsm-werecruit-bestprice-year', array( $this, 'bestpriceyear_shortcode') );
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
	 * Calendar shortcode
	 *
	 * @since    1.0.0
	 */
	public function calendar_shortcode($atts) {
		$atts = shortcode_atts( array(
			'option' => '',
		), $atts, 'tmsm-werecruit-calendar' );

		$output = $this->calendar_template();
		$output .= $this->form_template();

		$output = '<div id="tmsm-werecruit-container">'.$output.'</div>';
		return $output;
	}

	/**
	 * Best Price Year shortcode
	 *
	 * @since    1.0.7
	 */
	public function bestpriceyear_shortcode($atts) {
		$atts = shortcode_atts( array(
			'roomid' => '',
			'rateid' => '',
		), $atts, 'tmsm-werecruit-bestprice-year' );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log(print_r($atts, true));
		}

		$price = null;
		$output = null;
		$date = null;
		$bestprice_year_requested = null;

		$bestprice_year = get_option( 'tmsm-werecruit-bestprice-year', false );

		if(!empty($atts['roomid']) && !empty($bestprice_year['Room'.$atts['roomid']])){
			$bestprice_year_requested = $bestprice_year['Room'.$atts['roomid']];
		}
		elseif(!empty($atts['rateid']) && !empty($bestprice_year['Rate'.$atts['rateid']])){
			$bestprice_year_requested = $bestprice_year['Rate'.$atts['rateid']];
		}
		else{
			$bestprice_year_requested = (!empty($bestprice_year['Overall']) ? $bestprice_year['Overall'] : null);
		}

		if ( !empty($bestprice_year_requested) && !empty($bestprice_year_requested['Price'])) {
			$price = sanitize_text_field($bestprice_year_requested['Price']);
			if ( !empty($bestprice_year_requested['Date'])) {
				$date = sanitize_text_field($bestprice_year_requested['Date']);
			}
		}

		if(!empty($price)){
			$output = '<span class="tmsm-werecruit-bestprice-year" data-date="'.$date.'" data-price="'.$price.'" data-roomid="'.(!empty($atts['roomid'])?esc_attr__($atts['roomid']):'').'" data-rateid="'.(!empty($atts['rateid'])?esc_attr__($atts['rateid']):'').'"></span>';
		}
		return $output;
	}

	/**
	 * Legend template
	 *
	 * @return string
	 */
	private function legend_template(){
		$output = '
		        <div class="tmsm-werecruit-form-legend">
                <p class="legend-item legend-item-notavailable">'.__('Not available','tmsm-werecruit').'</p>
                <p class="legend-item legend-item-available">'.__('Available','tmsm-werecruit').'</p>
                <p class="legend-item legend-item-lowestprice">'.__('Lowest price','tmsm-werecruit').'</p>
                <p class="legend-item legend-item-lastrooms">'.__('Last rooms','tmsm-werecruit').'</p>
                <p class="legend-item legend-item-minstay">'.__('Minimum stay','tmsm-werecruit').'</p>
	        </div>';
		return $output;
	}	
		
	/**
	 * Form template
	 *
	 * @return string
	 */
	private function form_template(){

		$today = new Datetime();
		$tomorrow = (new DateTime())->modify('+1 day');

		$output = '
		<form target="_blank" action="'.self::ENGINE_URL.$this->get_option('engine').'" method="get" id="tmsm-werecruit-form">
		
		<input type="hidden" name="language" value="'.$this->get_locale().'">
		<input type="hidden" name="arrivalDate" value="" id="tmsm-werecruit-form-arrivaldate">
		<input type="hidden" name="nights" value="1" id="tmsm-werecruit-form-nights">
		<input type="hidden" name="checkinDate" value="" id="tmsm-werecruit-form-checkindate">
		<input type="hidden" name="checkoutDate" value="" id="tmsm-werecruit-form-checkoutdate">
		<input type="hidden" name="selectedAdultCount" value="2">
		<input type="hidden" name="selectedChildCount" value="0">
		<input type="hidden" name="guestCountSelector" value="ReadOnly">
		<input type="hidden" name="rate" value="">
		<input type="hidden" name="roomid" value="">
		<input type="hidden" name="showSearch" value="true">
		
        <div class="tmsm-werecruit-form-fields">

			'.(!empty($this->get_option('intro')) ? '<p id="tmsm-werecruit-form-intro">'.html_entity_decode( $this->get_option('intro')).'</p>' : '' ).'
			
			<p id="tmsm-werecruit-form-dates-container" style="display: none">
				' . _x( 'From', 'date selection',  'tmsm-werecruit' ) . ' <span id="tmsm-werecruit-form-checkindateinfo"></span> ' . _x( 'to', 'date selection', 'tmsm-werecruit' ) . ' <span id="tmsm-werecruit-form-checkoutdateinfo"></span>
			</p>
            <p id="tmsm-werecruit-form-nights-message" data-value="0">'.__('Number of nights:','tmsm-werecruit').' <span id="tmsm-werecruit-form-nights-number"></span></p>
            <p id="tmsm-werecruit-form-minstay-message" data-value="0">'.__('Minimum stay:','tmsm-werecruit').' <span id="tmsm-werecruit-form-minstay-number"></span></p>
			';

		/*$output.='

			<p>
				<label for="tmsm-werecruit-form-adults" id="tmsm-werecruit-form-adults-label">'.__( 'Number of adults:', 'tmsm-werecruit' ).'</label>
				<select name="selectedAdultCount" id="tmsm-werecruit-form-adults">
				<option value="2">'.__( 'Number of adults', 'tmsm-werecruit' ).'</option>';


				for ( $adults = 1; $adults <= 6; $adults ++ ) {
					$output .= '<option value="' . $adults . '">';
					$output .= sprintf( _n( '%s adult', '%s adults', $adults, 'tmsm-werecruit' ), number_format_i18n( $adults ) );
					$output .= '</option>';
				}

		$output.='

				</select>
			</p>';
		*/

		$theme = wp_get_theme();
		$buttonclass = '';
		if ( 'StormBringer' == $theme->get( 'Name' ) || 'stormbringer' == $theme->get( 'Template' ) ) {
			$buttonclass = 'btn btn-primary';
		}
		if ( 'OceanWP' == $theme->get( 'Name' ) || 'oceanwp' == $theme->get( 'Template' ) ) {
			$buttonclass = 'button';
		}

		/**
		 *             <a href="'.self::ENGINE_URL.$this->get_option('engine').'" id="tmsm-werecruit-form-submit" class="'.$buttonclass.'">' .(!empty($this->get_option('bookbuttonlabel')) ? html_entity_decode($this->get_option('bookbuttonlabel')) : __( 'Book now', 'tmsm-werecruit' ) ). '</a>
		 */

        $output.='  
            <p id="tmsm-werecruit-calculatetotal-results">
                <span id="tmsm-werecruit-calculatetotal-totalprice" style="display: none"></span>
                <span id="tmsm-werecruit-calculatetotal-errors" style="display: none"></span>
                <i class="fa fa-spinner fa-spin" aria-hidden="true" id="tmsm-werecruit-calculatetotal-loading" style="display: none"></i>
			</p>
            <p>
            <button type="submit" id="tmsm-werecruit-form-submit" class="'.$buttonclass.'">' .(!empty($this->get_option('bookbuttonlabel')) ? html_entity_decode($this->get_option('bookbuttonlabel')) : __( 'Book now', 'tmsm-werecruit' ) ). '</button>
            </p>
            <p id="tmsm-werecruit-calculatetotal-ota" style="display: none"></p>
            '.(!empty($this->get_option('outro')) ? '<div id="tmsm-werecruit-form-outro">'.html_entity_decode($this->get_option('outro')).'</div>' : '' ).'
            </div>
            </form>
            <form action="" method="post" id="tmsm-werecruit-calculatetotal">
			'.wp_nonce_field( 'tmsm-werecruit-calculatetotal-nonce-action', 'tmsm-werecruit-calculatetotal-nonce', true, false ).'        
			</form>
		';//<button type="submit" id="tmsm-werecruit-calculatetotal-submit">Submit</button>
		return $output;
	}

	/**
	 * Display calendar template
	 *
	 * @return string
	 */
	private function calendar_template(){
		$output = '
<div id="tmsm-werecruit-calendar">
<script id="tmsm-werecruit-calendar-template" type="text/template">

        <table class="table-calendarprices table-condensed" border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr class="clndr-controls">
                <th class="clndr-control-button clndr-control-button-previous">
                    <span class="clndr-previous-button">&larr;</span>
                </th>
                <th class="month" colspan="5">
                    <%= month %> <%= year %>
                </th>
                <th class="clndr-control-button clndr-control-button-next">
                    <span class="clndr-next-button">&rarr;</span>
                </th>
            </tr>
            <tr class="header-days">

                <% for(var i = 0; i < daysOfTheWeek.length; i++) { %>
<th class="header-day">
                    <span class="hide"><%= moment().weekday(i).format(\'dd\').charAt(0) %></span>
                    <span class=""><%= daysOfTheWeek[i] %></span>
                </th>
                <% } %>
            </tr>
            </thead>
            <tbody>
            <% for(var i = 0; i < numberOfRows; i++){ %>
            <tr>
                <% for(var j = 0; j < 7; j++){ %>
                <% var d = j + i * 7; %>
                <td class="<%= days[d].classes %>" data-daynumber="<%= days[d].day %>">

                    <% if (days[d].events.length != 0) { %>
                    <% _.each(days[d].events, function(event) { %>
                    <div class="cell" data-price="<%= event.Price %>" data-status="<%= event.Status %>"  data-lowestprice="<%= event.LowestPrice %>" data-availability="<%= event.Availability %>" data-minstay="<%= event.MinimumStayThrough %>">
                        <span class="day-number"><%= days[d].day %></span>
                        <span class="minstay">⇾</span>
                        <p class="price" data-test="<%= event.Test %>"><%= event.PriceWithCurrency %></p>
                    </div>
                    <% }) %>

                    <% } else { %>

                    <div class="cell">
                        <span class="day-number"><%= days[d].day %></span>
                        <p class="price">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
                    </div>
                    <% } %>
                </td>
                <% } %>
            </tr>
            <% } %>
            </tbody>
        </table>

</script>
</div>
';

		$output = '<div id="tmsm-werecruit-calendar-container">'.$output.$this->legend_template().'</div>';
		return $output;
	}

	/**
	 * Get options for all bestprices months
	 *
	 * @return array
	 * @throws Exception
	 */
	private function get_options_bestprice(){

		$data = [];

		// Browse 12 next months
		$date = new Datetime();
		$date->modify('-1 month');
		$i=0;
		while($i<=12){
			$date->modify('+1 month');
			$month_data = get_option('tmsm-werecruit-bestprice-'.$date->format('Y-m'), false);
			if(!empty($month_data)){
				$data[$date->format('Y-m')] = $month_data;
			}
			$i++;
		}

		return $data;
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


	/**
	 * Ajax calculate total price
	 *
	 * @since    1.0.0
	 */
	public static function ajax_calculate_totalprice() {

		$security = sanitize_text_field( $_POST['security'] );
		$date_begin = sanitize_text_field( $_POST['date_begin'] );
		$date_end = sanitize_text_field( $_POST['date_end'] );
		$nights = sanitize_text_field( $_POST['nights'] );

		$errors = array(); // Array to hold validation errors
		$jsondata   = array(); // Array to pass back data

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('ajax_calculate_totalprice');
		}

		// Check security
		if ( empty( $security ) || ! wp_verify_nonce( $security, 'tmsm-werecruit-calculatetotal-nonce-action' ) ) {
			$errors[] = __('Token security not valid', 'tmsm-werecruit');
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Ajax security not OK');
			}
		}
		else{
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Ajax security OK');
			}

			check_ajax_referer( 'tmsm-werecruit-calculatetotal-nonce-action', 'security' );

			// Check date begin
			if(empty($date_begin)){
				$errors[] = __('Date is empty', 'tmsm-werecruit');
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log('Date is empty');
				}
			}
			// Check nights number
			if(empty($nights)){
				$errors[] = __('Nights number are empty', 'tmsm-werecruit');
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log('Nights number is empty');
				}
			}


			// All rates
			$rates = [ 'accommodation', 'ota'];
			$options = get_option('tmsm-werecruit-options', false);
			foreach($rates as $rate){
				$rateids = $options[$rate.'rateids'];

				if(!empty($rateids)){
					// Calculate price
					$webservice = new Tmsm_Werecruit_Webservice();
					$response   = $webservice->get_stayplanning( $date_begin, $nights, $rateids);
					$data       = $webservice::convert_to_array( $response );
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log('response:');
						error_log($response);
					}

					// Init data var
					$dailyplanning_bestprice = [];
					if ( ! empty( $data ) ) {
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							error_log( 'Data responsee' );
						}

						if ( isset( $data['response']['success'] ) ) {
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								error_log( 'data success' );
							}
							if ( isset( $data['response']['stayPlanning'] ) ) {

								if ( isset( $data['response']['stayPlanning']['ratePlan']['hotel'] )
								     && is_array( $data['response']['stayPlanning']['ratePlan']['hotel'] ) ) {

									foreach ( $data['response']['stayPlanning']['ratePlan']['hotel']['entity'] as $entity ) {
										if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
											error_log( 'Entity: roomId=' . $entity['@attributes']['roomId'] . ' rateId=' . $entity['@attributes']['rateId'] );
										}

										$properties = $entity['property'];

										if(!isset($properties[0])){
											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												error_log( 'properties not multiple');
											}
											$tmp = $properties;
											unset($properties);
											$properties[0] = $tmp;
										}

										$dailyplanning_bestprice_entity = [];

										foreach ( $properties as $property ) {

											$propertyname = $property['@attributes']['name'];

											@$dailyplanning_bestprice_entity[$propertyname] = $property['@attributes']['value'];

										}

										ksort($dailyplanning_bestprice_entity);
										if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
											//error_log('dailyplanning_bestprice_entity:');
											//error_log(print_r($dailyplanning_bestprice_entity, true));
										}

										// Merge data
										if(empty($dailyplanning_bestprice) && @$dailyplanning_bestprice_entity['Status'] !== 'NotAvailable'){
											$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
										}
										else{
											if(!empty($dailyplanning_bestprice_entity['Price']) && !empty($dailyplanning_bestprice['Price']) ){
												// New Price is less than merged data
												if(
													$dailyplanning_bestprice_entity['Price'] < $dailyplanning_bestprice['Price']
													&& @$dailyplanning_bestprice_entity['Status'] !== 'NotAvailable'
												){
													$dailyplanning_bestprice = $dailyplanning_bestprice_entity;
												}
											}
										}
									}
								}
							}
						}
					}
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						//error_log('dailyplanning_bestprice:');
						//error_log(print_r($dailyplanning_bestprice, true));
					}

					$totalprice = null;
					if ( ! empty( $dailyplanning_bestprice ) && @$dailyplanning_bestprice['Status'] !== 'NotAvailable' ) {
						$totalprice = $dailyplanning_bestprice['Price'];
						$jsondata['data'][$rate] = [
							'totalprice' => money_format( '%.2n', $totalprice ),
						];
					} else {
						if($rate == 'accommodation'){
							$errors[] = __( 'No availability', 'tmsm-werecruit' );
						}
					}
				}
				else{
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log('rateids empty:');
					}
				}
			}
		}


		// Return a response
		if( ! empty($errors) ) {
			$jsondata['success'] = false;
			$jsondata['errors']  = $errors;
		}
		else {
			$jsondata['success'] = true;
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('json data:');
			error_log(print_r($jsondata, true));
		}

		wp_send_json($jsondata);
		wp_die();

    }

}
