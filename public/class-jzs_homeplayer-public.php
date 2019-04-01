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
        add_shortcode('jzs_homeplayer', ['Jzs_homeplayer_Public', 'getHomePage']);
        add_shortcode('jzs_product', ['Jzs_homeplayer_Public', 'getProductPage']);
        add_shortcode('jzs_checkout', ['Jzs_homeplayer_Public', 'getCheckoutPage']);
        add_shortcode('jzs_cart_amount', ['Jzs_homeplayer_Public', 'getCartAmount']);
    }

    // https://codex.wordpress.org/Shortcode_API
    public static function getCartAmount()
    {
        global $woocommerce;
        if ($woocommerce) {
            $pluginFolder = "/wp-content/plugins/jzs_homeplayer/public";
            ?><script>const jzs_cart_amount = <?=$woocommerce->cart->cart_contents_count?></script>
            <script type="text/javascript">
    var MTUserId='a7d60c27-1034-499b-b1c0-8f4bdcf2cc8e';
    var MTFontIds = new Array();

    MTFontIds.push("5663858"); // Neue Helvetica® W05 73 Extended Bold
    (function() {
        var mtTracking = document.createElement('script');
        mtTracking.type='text/javascript';
        mtTracking.async='true';
        mtTracking.src='<?=$pluginFolder?>/js/mtiFontTrackingCode.js';

        (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(mtTracking);
    })();
</script>

<style type="text/css">
    @font-face{
        font-family:"HelveticaNeue";
        src:url("<?=$pluginFolder?>/Fonts/5663858/8b114d9e-dbed-4c3e-b509-6c1c4f84cbf2.eot?#iefix");
        src:url("<?=$pluginFolder?>/Fonts/5663858/8b114d9e-dbed-4c3e-b509-6c1c4f84cbf2.eot?#iefix") format("eot"),url("<?=$pluginFolder?>/Fonts/5663858/3e37e0b1-8062-42c6-8664-4ea78aa2de25.woff2") format("woff2"),url("<?=$pluginFolder?>/Fonts/5663858/a25e22f2-80cf-4f21-a365-b77c87a3edf8.woff") format("woff"),url("<?=$pluginFolder?>/Fonts/5663858/32502e84-a824-43c8-9792-9aa597884d9f.ttf") format("truetype");
    }
</style><?php

        }
    }
    public static function getHomePage()
    {
        return file_get_contents(__DIR__ . "/partials/public-display.html") . self::getShopStocksJson();
    }
    public static function getProductPage($args)
    {
        global $product;
        $fp = __DIR__ . "/partials/product_" . (empty($product) ? (empty($args["productid"]) ? "NO_PRODUCT_DETECTED_AND_NO_PRODUCTID_GIVEN" : $args["productid"]) : $product->get_id()) . ".html";
        if (file_exists($fp)) {
            return file_get_contents($fp) . self::getShopStocksJson();
        } else {
            return "Nope there is a problem (jzsPublicPHP:" . $fp . ":" . __LINE__ . ")";
        }
    }
    public static function getCheckoutPage()
    {
        return file_get_contents(__DIR__ . "/partials/checkout-page.html");
    }

    public static function getShopStocksJson()
    {
        $products = wc_get_products(['status' => 'publish', 'limit' => -1]);
        $stocks = [];
        foreach ($products as $product) {
            $stocks[$product->get_id()] = $product->get_stock_status() == "instock" ? "live" : "offline";
        }
        return "<script>let jzs_shop_stock_status = " . json_encode($stocks) . "</script>";
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

        wp_enqueue_style($this->plugin_name . "global", plugin_dir_url(__FILE__) . 'css/jzs-global.css', array(), $this->version, 'all');
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
