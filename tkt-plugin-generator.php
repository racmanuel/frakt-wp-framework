<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.tukutoi.com/
 * @since             1.0.0
 * @since             2.0.0 Replaced Source Files.
 * @package           Tkt_Plugin_Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Frakt WP Generator
 * Plugin URI:        https://racmanuel.dev
 * Description:       Modern WordPress plugin framework based on Better WP Plugin Boilerplate. Includes SCF, Local/Sync JSON, Composer, Bulma CSS and more. Build powerful, modular plugins with clean architecture.
 * Version:           2.0.0
 * Author:            racmanuel
 * Author URI:        https://racmanuel.dev
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tkt-plugin-generato
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
define( 'TKT_PLUGIN_GENERATOR_VERSION', '2.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tkt-plugin-generator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tkt_plugin_generator() {

	$plugin = new Tkt_Plugin_Generator();
	$plugin->run();

}
run_tkt_plugin_generator();
