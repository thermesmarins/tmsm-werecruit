<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/nicomollet
 * @since      1.0.0
 *
 * @package    Tmsm_Werecruit
 * @subpackage Tmsm_Werecruit/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tmsm_Werecruit
 * @subpackage Tmsm_Werecruit/includes
 * @author     Nicolas Mollet <nico.mollet@gmail.com>
 */
class Tmsm_Werecruit_Activator {

	/**
	 * Activate
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! wp_next_scheduled( 'tmsmwerecruit_cronaction' ) ) {
			wp_schedule_event( time(), 'tmsm_werecruit_refresh_schedule', 'tmsmwerecruit_cronaction' );
		}
	}

}
