<?php
/**
 *
 * @link              http://alignpixel.com
 * @since             1.0.0
 * @package           APx_Link_Status
 *
 * @wordpress-plugin
 * Plugin Name:       APX Link Status
 * Plugin URI:        https://alignpixel.com/plugin/apx-link-status
 * Description:       Internal and External Link Status for WordPress Posts, Pages and Custom Post Types.
 * Version:           1.0.1
 * Author:            Align Pixel <contact@alignpixel.com>
 * Author URI:        https://alignpixel.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       apx-link-status
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'APX_LINK_STATUS_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 */
function activate_apx_link_status() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-apx-link-status-activator.php';
	APx_Link_Status_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_apx_link_status() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-apx-link-status-deactivator.php';
	APx_Link_Status_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_apx_link_status' );
register_deactivation_hook( __FILE__, 'deactivate_apx_link_status' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-apx-link-status.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_apx_link_status() {

	$plugin = new APx_Link_Status();
	$plugin->run();

}
run_apx_link_status();
