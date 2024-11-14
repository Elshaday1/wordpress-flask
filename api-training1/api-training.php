<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress
 * @since             1.0.0
 * @package           Api_Training
 *
 * @wordpress-plugin
 * Plugin Name:       API Training
 * Plugin URI:        https://khalinoid.com/api-training
 * Description:       This plugin is a simple start on how to make  APIs in wordpress
 * Version:           1.0.0
 * Author:            Khalid
 * Author URI:        https://wordpress/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       api-training
 * Domain Path:       /languages
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
define( 'API_TRAINING_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-api-training-activator.php
 */
function activate_api_training() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-api-training-activator.php';
	//include the controller file
	require_once plugin_dir_path(__FILE__) . 'public/controller/api.php';

	Api_Training_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-api-training-deactivator.php
 */
function deactivate_api_training() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-api-training-deactivator.php';
	Api_Training_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_api_training' );
register_deactivation_hook( __FILE__, 'deactivate_api_training' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-api-training.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_api_training() {

	$plugin = new Api_Training();
	$plugin->run();

}
run_api_training();

