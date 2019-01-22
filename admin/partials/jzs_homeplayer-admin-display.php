<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.jzs.fr
 * @since      1.0.0
 *
 * @package    Jzs_homeplayer
 * @subpackage Jzs_homeplayer/admin/partials
 */
?>

<div class="wrap">
<h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <form method="post" action="options.php">
    <?php
$options = get_option($this->plugin_name);
$cleanup = $options['cleanup'];

settings_fields($this->plugin_name);
do_settings_sections($this->plugin_name);

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    // Put your plugin code here
    $products = wc_get_products(['status' => 'publish', 'limit' => -1]);
    foreach ($products as $product) {
        echo "<p>URL du produit <strong>" . $product->get_permalink() . "</strong></p>";
        echo "<p>Nom du produit <strong>" . $product->get_name() . "</strong></p>";
        echo "<p>Le produit est <strong>" . $product->get_stock_status() . "</strong></p>"; // instock | outofstock | inbackorder
        // echo ($product->get_price()) . "€<br>";


        $terms = get_terms([
            'taxonomy' => 'pa_thecolor',
            'hide_empty' => false,
        ]);
        echo "<p>Couleurs ";
        foreach ($terms as $term) {
            $color_hex = get_term_meta($term->term_id)["color"][0];
            // echo $term->name;
            echo "<span style='border-radius: 3em; height: 1em; width: 1em; display: inline-block; background: " . $color_hex . "'></span>&nbsp;";

            // echo "<pre>";
            // var_dump($term);
            // echo "</pre>";
        }
        echo "</p>";
        
        echo "<pre>";
        var_dump($product->get_variation_attributes());
        var_dump($product);
        echo "</pre>";
    }
}

?>

        <fieldset>
            <legend class="screen-reader-text">
                <span>Clean WordPress head section</span>
            </legend>
            <label>
                <input type="checkbox" name="<?php echo $this->plugin_name; ?>[cleanup]" value="1" <?php checked($cleanup, 1);?> />
                <span>Une simple checkbox</span>
            </label>
        </fieldset>

        <?php submit_button('Enregistrer', 'primary', 'submit', true);?>
    </form>
</div>