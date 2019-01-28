<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.jzs.fr
 * @since      1.0.0
 *
 * @package    Jzs_homeplayer
 * @subpackage Jzs_homeplayer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Jzs_homeplayer
 * @subpackage Jzs_homeplayer/public
 * @author     Jean Z. SORDES <jean.sordes@yahoo.fr>
 */
class Jzs_homeplayer_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_shortcode('jzs_homeplayer', ['Jzs_homeplayer_Public', 'pluginContent']);
    }

    // https://codex.wordpress.org/Shortcode_API
    public static function pluginContent()
    {
        return file_get_contents(__DIR__ . "/partials/public-display.html");
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Jzs_homeplayer_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Jzs_homeplayer_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name . "global", plugin_dir_url(__FILE__) . 'css/jzs_global-public.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "player", plugin_dir_url(__FILE__) . 'css/jzs_player-public.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . "rainbow", plugin_dir_url(__FILE__) . 'css/jzs_rainbow_btns-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Jzs_homeplayer_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Jzs_homeplayer_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/jzs_homeplayer-public.js', array('jquery'), $this->version, false);

    }

}
