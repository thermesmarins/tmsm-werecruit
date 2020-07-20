<?php

/**
 * Availpro web service
 *
 * @since      1.0.0
 *
 * @package    Tmsm_Werecruit
 * @subpackage Tmsm_Werecruit/includes
 */

class Tmsm_Werecruit_Webservice {

	/**
	 * Webservice Namespace
	 *
	 * @access 	const
	 * @since 	1.0.0
	 * @var 	string
	 */
	const WSNAMESPACE = 'http://ws.availpro.com/schemas/planning/2012A';

	/**
	 * Webservice URL
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	string
	 */
	const URL = 'https://ws.availpro.com/Planning/2012A/PlanningService.asmx?WSDL';

	/**
	 * Webservice Oauth identifiers
	 *
	 * @access 	private
	 * @since 	1.0.0
	 * @var 	array
	 */
	private $oauth_identifiers = [];

	/**
	 * Constructor
	 */
	public function __construct() {

		$options = get_option('tmsm-werecruit-options', false);

		$this->set_oauth_identifiers();

	}

	/**
	 * Set oauth identifiers
	 */
	private function set_oauth_identifiers(){
		$options = get_option('tmsm-werecruit-options', false);
		$this->oauth_identifiers = [
			'consumerKey'    => $options['consumerkey'],
			'consumerSecret' => $options['consumersecret'],
			'accessToken'    => $options['accesstoken'],
			'accessSecret'   => $options['tokensecret'],
		];
	}

	/**
	 * Get Layout
	 *
	 * @return string
	 */
	private function get_layout(){
		return '<level name="ArticleRate"><property name="Status" /><property name="Price" /><property name="Availability" /><property name="MinimumStayThrough" /></level>';

	}
	/**
	 * Get Filters
	 *
	 * @param null $rateids
	 *
	 * @return string
	 */
	private function get_filters($rateids = null){
		$options = get_option('tmsm-werecruit-options', false);

		$option_rateids = $rateids;
		$option_ratecode = null;
		$option_roomids = $options['roomids'];
		$option_groupid = $options['groupid'];
		$option_hotelid = $options['hotelid'];

		// rates
		$option_rateids_array = [];
		if(!empty($option_rateids) ){
			$option_rateids_array = explode(',', $option_rateids);
			foreach($option_rateids_array as &$item){
				$item = trim($item);
			}
		}
		$filters_rateids = '';
		if(!empty($option_rateids_array) && is_array($option_rateids_array) && count($option_rateids_array) > 0){
			$filters_rateids = '<rates default="Excluded">';
			foreach($option_rateids_array as $item){
				$filters_rateids .= '<exception id="'.$item.'"/>';
			}
			$filters_rateids .= '</rates>';
		}

		//rooms
		$option_roomids_array = [];
		if(!empty($option_roomids) ){
			$option_roomids_array = explode(',', $option_roomids);
			foreach($option_roomids_array as &$item){
				$item = trim($item);
			}
		}
		$filters_roomids = '';
		if(!empty($option_roomids_array) && is_array($option_roomids_array) && count($option_roomids_array) > 0){
			$filters_roomids = '<rooms default="Excluded">';
			foreach($option_roomids_array as $item){
				$filters_roomids .= '<exception id="'.$item.'"/>';
			}
			$filters_roomids .= '</rooms>';
		}

		// ratecode
		$filters_ratecode='';
		if(!empty($option_ratecode)){
			$filters_ratecode = 'referenceRateCode="'.$option_ratecode.'"';
		}

		// @TODO include $filters_roomids but it doesn't give any result with it
		// @TODO not hardcode OTABAR
		//referenceRateCode="BARPROM"
		$filters = '
					<ratePlans><ratePlan groupId="'.$option_groupid.'" '.$filters_ratecode.'><hotels default="Excluded"><exception id="'.$option_hotelid.'" /></hotels></ratePlan></ratePlans>'.
	                $filters_rateids.
	                //$filters_roomids.
	                '<currencies default="Excluded"><exception currency="EUR"/></currencies>'.
	                '<status><include status="Available" /><include status="NotAvailable" /></status>'.
		'';

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('filters:');
			error_log($filters);
		}

		return $filters;

	}

	/**
	 * Get Data from Availpro API call
	 *
	 * @param string $month (YYYY-MM)
	 *
	 * @return string
	 */
	public function get_data($month){

		$timezone = new DateTimeZone( "Europe/Paris" );

		$month_firstday = DateTime::createFromFormat('Y-m-d', $month.'-01', $timezone);
		$month_firstday->modify('first day of this month');
		$month_lastday = clone $month_firstday;
		$month_lastday->modify('last day of this month');
		//$month_lastday->modify('first day of this month')->modify('+6 days');

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			//error_log('get_data');
			//error_log('firstday:'.$month_firstday->format('Y-m-d'));
			//error_log('lastday:'.$month_lastday->format('Y-m-d'));
		}

		if(!class_exists('SoapOAuthWrapper')){
			return 'SoapOAuthWrapper doesn\'t exist';
		}

		$options = get_option('tmsm-werecruit-options', false);
		$rateids = $options['accommodationrateids'];

		$soap_parameters = array(
			'groupId'   => $options['groupid'],
			'hotelId'   => $options['hotelid'],
			'beginDate' => $month_firstday->format('Y-m-d'),
			'endDate'   => $month_lastday->format('Y-m-d'),
			'layout'    => $this->get_layout(),
			'filter'    => $this->get_filters($rateids),
		);

		try {
			$result = SoapOAuthWrapper::Invoke( self::URL, self::WSNAMESPACE, 'GetDailyPlanning', $soap_parameters, $this->oauth_identifiers );
			return $result;
		} catch ( OAuthException2 $e ) {
			return $e;
		}
	}

	/**
	 * Convert XML results in array
	 *
	 * @param string $xml
	 *
	 * @return array
	 */
	static public function convert_to_array($xml){

		$domObject = new DOMDocument();
		$domObject->loadXML($xml);

		$domXPATH = new DOMXPath($domObject);
		$results = $domXPATH->query("//soap:Body/*");

		$array = [];
		foreach($results as $result)
		{
			$array = json_decode(json_encode(simplexml_load_string($result->ownerDocument->saveXML($result))), true);
		}
		return $array;
	}



	/**
	 * Get Stay Planning from Availpro API call
	 *
	 * @param string $arrivaldate (YYYY-MM-DD)
	 * @param int $nights
	 * @param string $rateids
	 *
	 * @return string
	 */
	public function get_stayplanning($arrivaldate, $nights, $rateids){

		$timezone = new DateTimeZone( "Europe/Paris" );

		$arrivaldate = DateTime::createFromFormat('Y-m-d', $arrivaldate, $timezone);

		if(!class_exists('SoapOAuthWrapper')){
			return 'SoapOAuthWrapper doesn\'t exist';
		}


		$options = get_option('tmsm-werecruit-options', false);
		$soap_parameters = array(
			'groupId'   => $options['groupid'],
			'hotelId'   => $options['hotelid'],
			'arrivalDate' => $arrivaldate->format('Y-m-d'),
			'nightCount'   => $nights,
			'layout'    => '<level name="ArticleRate"><property name="Status" /><property name="Price" /><property name="Availability" /><property name="MinimumStayThrough" /></level>',
			'filter'    => $this->get_filters($rateids),
		);

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log('soap_parameters:');
			error_log(var_export($soap_parameters,true));
		}

		try {
			$result = SoapOAuthWrapper::Invoke( self::URL, self::WSNAMESPACE, 'GetStayPlanning', $soap_parameters, $this->oauth_identifiers );
			return $result;
		} catch ( OAuthException2 $e ) {
			return $e;
		}

	}

}