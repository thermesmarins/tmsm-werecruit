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
	public function checkprices() {

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Function checkprices()' );
		}

		$lastmonthchecked = get_option( 'tmsm-werecruit-lastmonthchecked', false );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Last month checked: ' . $lastmonthchecked );
		}

		// Check if the last checked value was created
		if ( $lastmonthchecked === false ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Check not initiated yet' );
			}
			// Initialize value
			$monthtocheck = date( 'Y-m' );
		} else {
			$lastmonthchecked_object = DateTime::createFromFormat( 'Y-m-d', $lastmonthchecked.'-01' );

			$lastmonthchecked_object->modify( '+1 month' );
			$lastmonthchecked_limit = new Datetime();
			$lastmonthchecked_limit->modify( '+1 year' );

			if ( $lastmonthchecked_object->getTimestamp() >= $lastmonthchecked_limit->getTimestamp() ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'Limit month passed' );
				}
				$monthtocheck = date( 'Y-m' );
			} else {
				$monthtocheck = $lastmonthchecked_object->format( 'Y-m' );
			}
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Month to check: ' . $monthtocheck );
		}

		// Empty year price
		$bestprice_year = get_option( 'tmsm-werecruit-bestprice-year', false );
		if(!empty($bestprice_year) && is_array($bestprice_year)){
			foreach($bestprice_year as $bestprice_year_item_key => $bestprice_year_item_value){
				if(!empty($bestprice_year_item_value) && isset($bestprice_year_item_value['Date']) ){
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log('key $bestprice_year: '.$bestprice_year_item_key);
						error_log('current $bestprice_year[Date]: '.$bestprice_year_item_value['Date']);
					}

					// unset value if we are checking again the prices of the month
					if(strpos($bestprice_year_item_value['Date'], $monthtocheck) !== false){
						unset($bestprice_year[$bestprice_year_item_key]);
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							error_log('current $bestprice_year[Date] is in month to check');
						}
					}

					// unset value if the value date is passed
					$bestprice_year_item_object = DateTime::createFromFormat( 'Y-m-d', $bestprice_year_item_value['Date'] );
					if($bestprice_year_item_object->getTimestamp() < time()){
						unset($bestprice_year[$bestprice_year_item_key]);
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							error_log('current $bestprice_year[Date] is passed');
						}
					}

				}
			}

		}

		// Update last check value
		$result = update_option( 'tmsm-werecruit-lastmonthchecked', $monthtocheck, true );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Result saving new month: ' . $result );
		}

		// API call
		$webservice = new Tmsm_Werecruit_Webservice();
		$response   = $webservice->get_data( $monthtocheck );
		$data       = $webservice::convert_to_array( $response );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'webservice response as array:' );
			error_log( print_r( $data, true ) );
		}

		// Init data var
		$dailyplanning_bestprice = [];
		$dailyplanning_bestprice_year = null;

		$interval = new \DateInterval( 'P1D' );

		if ( ! empty( $data ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( 'Data responsee' );
			}

			if ( isset( $data['response']['success'] ) ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'data success' );
				}
				if ( isset( $data['response']['dailyPlanning'] ) ) {

					if ( isset( $data['response']['dailyPlanning']['ratePlan']['hotel'] )
					     && is_array( $data['response']['dailyPlanning']['ratePlan']['hotel'] ) ) {

						foreach ( $data['response']['dailyPlanning']['ratePlan']['hotel']['entity'] as $entity ) {
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								error_log( '******************Entity: roomId=' . $entity['@attributes']['roomId'] . ' rateId=' . $entity['@attributes']['rateId'] );
								error_log( print_r($entity,true));

							}

							$properties = $entity['property'];

							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								//error_log( '******properties before:');
								//error_log( print_r($properties,true));
							}

							if(!isset($properties[0])){
								if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
									error_log( 'properties not multiple');
								}
								$tmp = $properties;
								unset($properties);
								$properties[0] = $tmp;
							}

							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								//error_log( '******properties after:');
								//error_log( print_r($properties,true));
							}

							$dailyplanning_bestprice_entity = [];

							//foreach ( $entity['property'] as $properties ) {

								foreach ( $properties as $property ) {
									if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
										//error_log( '***property:');
										//error_log( print_r($property,true));
									}

									$propertyname = (!empty($property['@attributes']['name']) ? $property['@attributes']['name'] : $property['name']);


									if ( ! empty( $property['period'] ) ) {
										foreach ( $property['period'] as $period ) {

											$attributes = ( isset( $period['@attributes'] ) ? $period['@attributes'] : $period );

											if ( ! empty( $attributes['beginDate'] ) && ! empty( $attributes['endDate'] ) && ! empty( $attributes['value'] ) ) {

												if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
													//error_log( $propertyname . ': beginDate=' . $attributes['beginDate'] . ' endDate='. $attributes['endDate'] . ' value=' . $attributes['value'] );
												}


												$begindate = Datetime::createFromFormat( 'Y-m-d', $attributes['beginDate'] );
												$enddate   = Datetime::createFromFormat( 'Y-m-d', $attributes['endDate'] );
												$value   = $attributes['value'];

												$daterange = new \DatePeriod( $begindate, $interval, $enddate->modify( '+1 day' ) );

												/* @var $date Datetime */
												foreach ( $daterange as $date ) {
													//error_log( 'date: ' . $date->format( 'Y-m-d' ) );
													if(empty($dailyplanning_bestprice_entity[ $date->format( 'Y-m-d' )])){
														$dailyplanning_bestprice_entity[ $date->format( 'Y-m-d' )] = array();
													}
													$dailyplanning_bestprice_entity[ $date->format( 'Y-m-d' )][$propertyname] = $value;
												}


											}
										}
									}
								}


							//}

							ksort($dailyplanning_bestprice_entity);
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								//error_log('dailyplanning_bestprice_entity:');
								//error_log(print_r($dailyplanning_bestprice_entity, true));
							}


							foreach($dailyplanning_bestprice_entity as $date => $attributes){
								//if($date == '2018-07-05'){
									//error_log('*roomid: '.$entity['@attributes']['roomId']);
									//error_log('*Date: '.$date);
									//error_log('*Price: '.@$attributes['Price']);
									//error_log('*Status: '.@$attributes['Status']);
									if(@$attributes['Status'] !=='NotAvailable' && !empty($attributes['Price'])){

										$attributes['Date'] = $date;

										// Check year price overall

										// Init overall year price
										if(empty($dailyplanning_bestprice_year['Overall']) && empty($dailyplanning_bestprice_year['Overall']['Price'])){
											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												error_log('Overall is empty');
											}
											$dailyplanning_bestprice_year['Overall'] = $attributes;
										}

										// Compare existing overall year price
										if(!empty($dailyplanning_bestprice_year['Overall']) && !empty($dailyplanning_bestprice_year['Overall']['Price']) &&
										   @$dailyplanning_bestprice_year['Overall']['Price'] > $attributes['Price']
										){
											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												error_log('New price: inferior');
												error_log(print_r($attributes, true));
												error_log('old price:'.$dailyplanning_bestprice_year['Overall']['Price']);
											}
											$dailyplanning_bestprice_year['Overall'] = $attributes;
										}

										// Check if price date has passed
										/*if(!empty($dailyplanning_bestprice_year['Overall']) && !empty($dailyplanning_bestprice_year['Overall']['Date']) &&
                                           @$dailyplanning_bestprice_year['Overall']['Date'] < date('Y-m-d')
										){
											if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
												error_log('New price: date passed');
												error_log(print_r($attributes, true));
												error_log('old date:'.$dailyplanning_bestprice_year['Overall']['Date']);
											}
											$dailyplanning_bestprice_year['Overall'] = $attributes;
										}*/


										// Check year price Room

										// Init overall year price Room
										if(empty($dailyplanning_bestprice_year['Room'.$entity['@attributes']['roomId']]) && empty($dailyplanning_bestprice_year['Room'.$entity['@attributes']['roomId']]['Price'])){
											$dailyplanning_bestprice_year['Room'.$entity['@attributes']['roomId']] = $attributes;
										}
										// Compare existing overall year price Room
										if(!empty($dailyplanning_bestprice_year['Room'.$entity['@attributes']['roomId']]) && !empty($dailyplanning_bestprice_year['Room'.$entity['@attributes']['roomId']]['Price']) && $dailyplanning_bestprice_year['Room'.$entity['@attributes']['roomId']]['Price'] > $attributes['Price']){
											$dailyplanning_bestprice_year['Room'.$entity['@attributes']['roomId']] = $attributes;
										}


										// Check year price Rate

										// Init overall year price Rate
										if(empty($dailyplanning_bestprice_year['Rate'.$entity['@attributes']['rateId']]) && empty($dailyplanning_bestprice_year['Rate'.$entity['@attributes']['rateId']]['Price'])){
											$dailyplanning_bestprice_year['Rate'.$entity['@attributes']['rateId']] = $attributes;
										}
										// Compare existing overall year price Rate
										if(!empty($dailyplanning_bestprice_year['Rate'.$entity['@attributes']['rateId']]) && !empty($dailyplanning_bestprice_year['Rate'.$entity['@attributes']['rateId']]['Price']) && $dailyplanning_bestprice_year['Rate'.$entity['@attributes']['rateId']]['Price'] > $attributes['Price']) {
											$dailyplanning_bestprice_year[ 'Rate' . $entity['@attributes']['rateId'] ] = $attributes;
										}

										// Check month price
										if(!empty($dailyplanning_bestprice[$date]['Price'])){
											//error_log('*Current Best Price: '.$dailyplanning_bestprice[$date]['Price']);
											if(
												$attributes['Price'] < $dailyplanning_bestprice[$date]['Price']
											){
												//error_log('*Price Inferior to Current Best Price: '.$attributes['Price']);
												$dailyplanning_bestprice[$date]['Price'] = $attributes['Price'];
												//error_log('*New Best Price: '.$dailyplanning_bestprice[$date]['Price']);
											}
										}
										else{
											if(empty($dailyplanning_bestprice[$date])){
												$dailyplanning_bestprice[$date] = array();
											}
											$dailyplanning_bestprice[$date]['Price'] = $attributes['Price'];
											$dailyplanning_bestprice[$date] = $dailyplanning_bestprice_entity[$date];
											//error_log('*Setting  Best Price: '.$dailyplanning_bestprice[$date]['Price']);
										}
									}
								//}

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
		// Save Month to check data
		update_option('tmsm-werecruit-bestprice-'.$monthtocheck, $dailyplanning_bestprice);

		// Check year best price
		$bestprice_year = get_option( 'tmsm-werecruit-bestprice-year', false );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('dailyplanning_bestprice_year:');
			error_log(print_r($dailyplanning_bestprice_year, true));
		}
		if(($bestprice_year === false || $bestprice_year === '') && !empty($dailyplanning_bestprice_year)){
			$bestprice_year = $dailyplanning_bestprice_year;
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Init bestprice_year');
			}
		}
		else{
			if(is_array($dailyplanning_bestprice_year)){
				foreach($dailyplanning_bestprice_year as $bestprice_year_item_key => $bestprice_year_item_value){
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						error_log('key bestprice_year: '.$bestprice_year_item_key);
						error_log('isset:'.isset($dailyplanning_bestprice_year[$bestprice_year_item_key]));
						error_log('price:'.(@$bestprice_year[$bestprice_year_item_key]['Price'] > @$dailyplanning_bestprice_year[$bestprice_year_item_key]['Price']));
						error_log('best:'.@$bestprice_year[$bestprice_year_item_key]['Price']);
						error_log('current:'.@$dailyplanning_bestprice_year[$bestprice_year_item_key]['Price']);
						error_log('date:'.(@$bestprice_year[$bestprice_year_item_key]['Date'] < date('Y-m-d')));
						error_log('date best:'.@$bestprice_year[$bestprice_year_item_key]['Date']);
						error_log('date current:'.date('Y-m-d'));
					}

					if(
						!isset($bestprice_year[$bestprice_year_item_key])
						||
						(
							isset($dailyplanning_bestprice_year[$bestprice_year_item_key])
							&&
							(
								@$bestprice_year[$bestprice_year_item_key]['Price'] > @$dailyplanning_bestprice_year[$bestprice_year_item_key]['Price']
								||
								@$bestprice_year[$bestprice_year_item_key]['Date'] < date('Y-m-d')
							)
						)

					){
						$bestprice_year[$bestprice_year_item_key] = $dailyplanning_bestprice_year[$bestprice_year_item_key];
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							error_log('New bestprice_year');
							error_log(print_r($dailyplanning_bestprice_year[$bestprice_year_item_key], true));
						}
					}
					else{
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							error_log('Not best bestprice_year');
						}
					}
				}

			}
		}

		update_option('tmsm-werecruit-bestprice-year', $bestprice_year);


		/*$bestprice_year = get_option( 'tmsm-werecruit-bestprice-year', false );
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('$dailyplanning_bestprice_year:');
			error_log(print_r($dailyplanning_bestprice_year, true));
		}

		if(($bestprice_year === false || $bestprice_year === '') && $dailyplanning_bestprice_year !== null){
			update_option('tmsm-werecruit-bestprice-year', $dailyplanning_bestprice_year);
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log('Init bestprice_year');
				error_log(print_r($dailyplanning_bestprice_year, true));
			}
		}
		else{
			if(isset($dailyplanning_bestprice_year) && @$bestprice_year['Price'] > @$dailyplanning_bestprice_year['Price']){
				update_option('tmsm-werecruit-bestprice-year', $dailyplanning_bestprice_year);
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log('New bestprice_year');
					error_log(print_r($dailyplanning_bestprice_year, true));
				}
			}
			else{
				error_log('Not best bestprice_year');
			}
		}
		*/

		// Delete previous month data
		$today = new Datetime();
		delete_option( 'tmsm-werecruit-bestprice-'.$today->modify('-1 month')->format('Y-m') );
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
