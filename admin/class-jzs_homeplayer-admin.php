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
        add_menu_page('JZS Manager Settings page', 'JZS Manager', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'), 'dashicons-format-video');
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

    private function e($input) {
        return htmlspecialchars($input);
    }

    public function validate($input)
    {
        $output = '<div class="jzs-homeplayer" id="jzs-homeplayer" data-collection="' . $input["collection"] . '"><div class="sub-players">' . "\n\n" . '<!-- products loop -->' . "\n";

        $productsLoopOutput = ["players" => '', "rainbow_btns" => ''];
        $i = 1;
        foreach ($input["products"] as $product) {
            if ($product["mustDisplay"] == 1) {
                $productsLoopOutput["players"] .= '<div class="player' . ($i == 1 ? ' focused' : '') . '"><div class="videos">' . "\n\n" . '<!-- variations loop -->' . "\n";
                $productsLoopOutput["rainbow_btns"] .= '<a class="rainbow-btn uppercase gr-' . $i . '" href="#jzs-homeplayer"><span>' . $product["slug"] . '</span></a>';

                $variationsLoopOutput = ["videos" => '', "color_selectors" => ''];
                foreach ($product["variations"] as $key => $variation) {
                    $variationsLoopOutput["videos"] .= '<video class="jzs-video' . ($key == 0 ? ' jzs-playing' : '') . '" style="background-image: url(\'' . $variation["bgImgURL"] . '\')"
                        loop autoplay muted src="' . $variation["videoURL"] . '" data-stockStatus="' . $variation["stockStatus"] . '"></video>';
                    if (!empty($variation["color"]) && count($product["variations"]) > 1) {
                        $variationsLoopOutput["color_selectors"] .= '<a class="jzs-video-btn" style="background:' . $variation["color"] . '"></a> ';
                    }
                }

                $productsLoopOutput["players"] .= $variationsLoopOutput["videos"] . "\n" . '<!-- end variations loop -->' . "\n\n" . '</div><div class="controls"><div class="jzs-player-txt">' . "\n\n" . '<!-- HTML overlay -->' . "\n" . $product["htmlOverlay"] . "\n" . '<!-- end HTML overlay -->' . "\n\n" . '</div><div class="jzs-player-action-btns">' . "\n\n" . '<!-- variations loop -->' . "\n" . $variationsLoopOutput["color_selectors"] . "\n" . '<!-- end variations loop -->' . "\n\n" . '<a href="' . $product["permalink"] . '" class="jzs-title-framed jzs-title-font uppercase">BUY</a></div></div></div>';

                $i++;
            }
        }

        $output .= $productsLoopOutput["players"] . "\n" . '<!-- end products loop -->' . "\n\n" . '</div><div class="jzs-other-products"><div class="btns"><span class="jzs-title-font">SELECT PRODUCT</span><span class="all-rainbow-btns">' . "\n\n" . '<!-- products loop -->' . "\n" . $productsLoopOutput["rainbow_btns"] . "\n" . '<!-- end products loop -->' . "\n\n" . '</span></div><svg class="jzs-after-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 87.170302 47.93187"><path d="M0 .00297063C47.881188-.43604815 7.0065567 47.931871 87.170305 47.931871H0z" fill="currentColor" paint-order="stroke fill markers"/></svg></div></div>';

        $output .= "<script>let jzs_collection_name = '" . $input["collection"] . "'</script>";

        file_put_contents(realpath(__DIR__ . "/../public/partials/public-display.html"), $output);

        /************** */
        /* Product pages */
        /************** */
        foreach ($input["products"] as $productID => $product) {
            $slug = $product["slug"];
            $output = '<div class="jzs-product" id="jzs-product"><div class="product-section"><div class="imgs">';
            $default_size = $input["sizes"][0];
            $default_color = $product["variations"][0];
            $color_btns = '';
            $tmp_i = 0;
            $small_thumbnails = '';

            foreach ($default_color["product_thumbnails"] as $thumbnail_url) {
                $small_thumbnails .= '<img class="btn' . ($tmp_i == 0 ? ' focused' : '') . '" src="' . $thumbnail_url . '" alt="">';

                $output .= '<img ' . ($tmp_i == 0 ? 'class="focused"' : '') . ' src="' . $thumbnail_url . '" alt="">';
                $tmp_i++;
            }

            $output .= '</div><div class="controls"><div class="left-ctrls"><div class="description"><p>MODEL: <strong id="jzs-model">' . strtoupper($default_color["model"]) . '</strong> <span class="separator"></span>HEIGHT: <strong id="jzs-height">' . e(strtoupper($default_color["modelsize"])) . '</strong> <span class="separator"></span>
            WEARING: <strong id="jzs-wearing">' . e(strtoupper($default_color["modelsizelabel"])) . '</strong></p>';
            foreach ($product["variations"] as $k => $color_fields) {
                $color_btns .= '<strong class="btn' . ($k == 0 ? ' hover' : '') . '" data-target="' . $color_fields["colorSlug"] . '"></strong>';
            }
            $output .= '<p id="jzs-description" class="details">' . e($product["description"]) . '</p></div><div class="thumbnails">' . $small_thumbnails . '</div></div><div class="gap"></div><div class="right-ctrls"><div class="product-slug"><img src="https://chlores.io/wp-content/uploads/2019/02/rainbow-btn.png" alt=""></div><div class="btns"><div id="jzs-color-btns"><span class="label">COLOR</span>' . $color_btns . '</div>';
            $output .= '<div id="jzs-size-btns"><span class="label">SIZE</span>';
            foreach ($input["sizes"] as $k => $size) {
                $output .= '<strong class="btn' . ($k == 0 ? ' hover' : '') . '" data-target="' . $size["slug"] . '"></strong>';
            }

            $output .= '</div></div><div class="buy-section"><strong class="price"></strong><strong class="btn buy-btn">ADD TO CART</strong></div></div></div></div>';

            // other products
            $output .= '<div class="jzs-other-products"><div class="btns"><span class="jzs-title-font">SELECT PRODUCT</span><span class="all-rainbow-btns">';
            $tmp_i = 1;
            foreach ($input["products"] as $productID => $product) {
                $output .= '<a href="' . $product["permalink"] . '"class="btn rainbow-btn uppercase gr-' . $tmp_i . '"><span>' . $product["slug"] . '</span></a>';
                $tmp_i++;
            }
            $output .= '</span></div><svg class="jzs-after-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 87.170302 47.93187"><path d="M0 .00297063C47.881188-.43604815 7.0065567 47.931871 87.170305 47.931871H0z" fill="currentColor" paint-order="stroke fill markers"/></svg></div></div>';
            $output .= "<script>let jzs_product_data = " . json_encode(["sizes" => $input["sizes"], "product" => $product]) . "; let jzs_collection_name = '" . $input["collection"] . "'</script>";

            file_put_contents(realpath(__DIR__ . "/../public/partials") . "/" . $productID . ".html", $output);
        }

        return $input;
    }
}
