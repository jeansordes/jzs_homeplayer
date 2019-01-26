<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.jzs.fr
 * @since      1.0.0
 *
 * @package    Jzs_homeplayer
 * @subpackage Jzs_homeplayer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Jzs_homeplayer
 * @subpackage Jzs_homeplayer/admin
 * @author     Jean Z. SORDES <jean.sordes@yahoo.fr>
 */
class Jzs_homeplayer_Admin
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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/jzs_homeplayer-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/jzs_homeplayer-admin.js', array('jquery'), $this->version, false);

    }

    public function add_plugin_admin_menu()
    {
        // add_options_page('JZS Homeplayer Options and Setup', 'JZS Homeplayer', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
        add_menu_page('JZS Homeplayer Options and Setup', 'JZS Homeplayer', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'), 'dashicons-format-video');
    }

    public function add_action_links($links)
    {
        $settings_link = array(
            '<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);
    }

    public function display_plugin_setup_page()
    {
        include_once 'partials/jzs_homeplayer-admin-display.php';
    }

    public function options_update()
    {
        register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
    }

    public function validate($input)
    {
        $output = '<div class="jzs_player"><div class="sub-players">' . "\n\n" . '<!-- products loop -->' . "\n";

        $productsLoopOutput = ["players" => '', "rainbow_btns" => ''];
        $i = 1;
        foreach ($input["products"] as $slug => $product) {
            if ($product["mustDisplay"] == 1) {
                $productsLoopOutput["players"] .= '<div class="player"><div class="jzs-homeplayer-videos">' . "\n\n" . '<!-- variations loop -->' . "\n";
                $productsLoopOutput["rainbow_btns"] .= '<a class="rainbow-btn gr-' . $i . '" href="#"><span>' . $slug . '</span></a>';

                $variationsLoopOutput = ["videos" => '', "color_selectors" => ''];
                foreach ($product["variations"] as $variation) {
                    $variationsLoopOutput["videos"] .= '<video class="jzs-video jzs-playing" style="background-image: url(\'' . $variation["bgImgURL"] . '\')"
                        loop="" autoplay="" muted="" src="' . $variation["videoURL"] . '"></video>';
                        if (!empty($variation["color"])) {
                            $variationsLoopOutput["color_selectors"] .= '<a class="jzs-video-btn" style="background:' . $variation["color"] . '"></a>';
                        }
                }

                $productsLoopOutput["players"] .= $variationsLoopOutput["videos"] . "\n" . '<!-- end variations loop -->' . "\n\n" . '</div><div class="jzs-homeplayer-controls"><div><div class="jzs-player-txt">' . "\n\n" . '<!-- HTML overlay -->' . "\n" . $product["htmlOverlay"] . "\n" . '<!-- end HTML overlay -->' . "\n\n" . '</div><div class="jzs-player-action-btns">' . "\n\n" . '<!-- variations loop -->' . "\n" . $variationsLoopOutput["color_selectors"] . "\n" . '<!-- end variations loop -->' . "\n\n" . '<a href="' . $product["permalink"] . '" class="jzs-title-framed jzs-title-font">BUY</a></div></div></div></div>';

                $i++;
            }
        }

        $output .= $productsLoopOutput["players"] . "\n" . '<!-- end products loop -->' . "\n\n" . '</div><div class="jzs-select-product"><div class="jzs-rainbow-btns"><span class="jzs-title-font">SELECT PRODUCT</span><span class="rainbow-btns">' . "\n\n" . '<!-- products loop -->' . "\n" . $productsLoopOutput["rainbow_btns"] . "\n" . '<!-- end products loop -->' . "\n\n" . '</span></div><svg class="jzs-after-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 87.170302 47.92891"><path d="M0 0c53.376895 0 42.471685 47.9536 87.170305 47.9289H0C.556356 13.26789 0 0 0 0z" fill="white" paint-order="stroke fill markers"></path></svg></div></div>';

        file_put_contents(realpath(__DIR__ . "/../public/partials/public-display.html"), $output);

        return $input;
    }
}
