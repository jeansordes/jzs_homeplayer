<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.jzs.fr
 * @since             1.0.0
 * @package           Jzs_homeplayer
 *
 * @wordpress-plugin
 * Plugin Name:       JZS Homeplayer
 * Plugin URI:        https://github.com/jeansordes/jzs_homeplayer
 * Description:       Plugin qui gère le player sur la page d'accueil
 * Version:           1.0.0
 * Author:            Jean Z. SORDES
 * Author URI:        http://www.jzs.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jzs_homeplayer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('PLUGIN_NAME_VERSION', '1.0.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jzs_homeplayer-activator.php
 */
function activate_jzs_homeplayer()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-jzs_homeplayer-activator.php';
    Jzs_homeplayer_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jzs_homeplayer-deactivator.php
 */
function deactivate_jzs_homeplayer()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-jzs_homeplayer-deactivator.php';
    Jzs_homeplayer_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_jzs_homeplayer');
register_deactivation_hook(__FILE__, 'deactivate_jzs_homeplayer');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-jzs_homeplayer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jzs_homeplayer()
{

    $plugin = new Jzs_homeplayer();
    $plugin->run();

}
run_jzs_homeplayer();
