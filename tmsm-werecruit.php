<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/thermesmarins/tmsm-werecruit
 * @since             1.0.0
 * @package           Tmsm_Werecruit
 *
 * @wordpress-plugin
 * Plugin Name:       TMSM WeRecruit
 * Plugin URI:        https://github.com/thermesmarins/tmsm-werecruit
 * Description:       Display Recruit job offers
 * Version:           1.0.4
 * Author:            Nicolas Mollet
 * Author URI:        https://github.com/nicomollet
 * Requires PHP:      7.3
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tmsm-werecruit
 * Domain Path:       /languages
 * Github Plugin URI: https://github.com/thermesmarins/tmsm-werecruit
 * Github Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TMSM_WERECRUIT_VERSION', '1.0.4' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tmsm-werecruit-activator.php
 */
function activate_tmsm_werecruit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-werecruit-activator.php';
	Tmsm_Werecruit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tmsm-werecruit-deactivator.php
 */
function deactivate_tmsm_werecruit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-werecruit-deactivator.php';
	Tmsm_Werecruit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tmsm_werecruit' );
register_deactivation_hook( __FILE__, 'deactivate_tmsm_werecruit' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-werecruit.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tmsm_werecruit() {

	$plugin = new Tmsm_Werecruit();
	$plugin->run();

}
run_tmsm_werecruit();
