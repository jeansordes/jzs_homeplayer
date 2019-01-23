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
$options = get_option($this->plugin_name);
do_settings_sections($this->plugin_name);
?>

<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <form method="post" action="options.php"><?php settings_fields($this->plugin_name)?>
        <!-- taxonomy color name -->
        <p><label>Nom de l'attribut pour la couleur<br />
            <input type='text' name='<?php echo $this->plugin_name ?>[colorAttrName]' placeholder='color' value='<?php echo (empty($options["colorAttrName"]) ? "" : $options["colorAttrName"]) ?>'/></label>
        </p>
<?php

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    // Put your plugin code here
    $products = wc_get_products(['status' => 'publish', 'limit' => -1]);
    foreach ($products as $product) {
        echo "<strong><a href='" . $product->get_permalink() . "'>" . $product->get_name() . "</a></strong><br />";
        echo "Product ID <strong>" . $product->get_slug() . "</strong><br />";
        echo "Le produit est <strong>" . $product->get_stock_status() . "</strong><br />";
        // instock | outofstock | inbackorder
        // echo ($product->get_price()) . "€<br>";
        ?>
        <label>
            <input type='checkbox' name="<?php echo $this->plugin_name . "[" . $product->get_slug() . "]" ?>" value='1' <?php checked($options[$product->get_slug()], 1)?> />
            Afficher ce produit sur le player
            </label><br />
        <?php

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
        // var_dump($product);
        echo "</pre>";
    }
}

submit_button('Enregistrer', 'primary', 'submit', true);?>
    </form>
    <h1>Aperçu</h1>
    <?=do_shortcode("[jzs_homeplayer]")?>
</div>