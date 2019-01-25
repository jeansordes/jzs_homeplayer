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
?><div class="wrap">
    <h2><?=esc_html(get_admin_page_title())?></h2>
    <form method="post" action="options.php">
        <?php settings_fields($this->plugin_name)?>
        <!-- taxonomy color name -->
        <p><label>Nom de l'attribut pour la couleur<br />
            <input type='text' name='<?php echo $this->plugin_name ?>[colorAttrName]' placeholder='color' value='<?=(empty($options["colorAttrName"]) ? "" : $options["colorAttrName"])?>'/></label>
        </p><?php
if (!empty($options["colorAttrName"])) {
    $products = wc_get_products(['status' => 'publish', 'limit' => -1]);
    if (empty($products)) {

        ?><div class="notice notice-error inline"><p>Il n'y a aucun produit à afficher</p></div><?php

    } else {
        if (array_key_exists('pa_' . $options["colorAttrName"], $products[0]->get_variation_attributes())) {
            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                // Put your plugin code here
                foreach ($products as $product) {
                    echo "<strong><a href='" . $product->get_permalink() . "'>" . $product->get_name() . "</a></strong><br />";
                    echo "Product ID <strong>" . $product->get_slug() . "</strong><br />";
                    echo "Le produit est <strong>" . $product->get_stock_status() . "</strong><br />";
                    // instock | outofstock | inbackorder
                    // echo ($product->get_price()) . "€<br>";
                    ?><label>
                        <input type='checkbox' name="<?php echo $this->plugin_name . "[" . $product->get_slug() . "]" ?>" value='1' <?php (!empty($options[$product->get_slug()])) ? checked($options[$product->get_slug()], 1) : null?> />
                        <span>Afficher ce produit sur le player</span>
                    </label><br /><?php

                    $terms = get_terms([
                        'taxonomy' => 'pa_' . $options["colorAttrName"],
                        'hide_empty' => false,
                    ]);
                    if (count($terms) > 0) {
                        // Il y a au moins une couleur
                        echo "<p>Vidéos pour chaques couleurs ";
                        foreach ($terms as $term) {
                            $color_hex = get_term_meta($term->term_id)["color"][0];
                            // echo $term->name;
                            echo "<span style='border-radius: 3em; height: 1em; width: 1em; display: inline-block; background: " . $color_hex . "'></span>&nbsp;";

                            // echo "<pre>";
                            // var_dump($term);
                            // echo "</pre>";
                        }
                        echo "</p>";
                    } else {
                        // pas de couleurs donc un seul choix
                        // prendre le thumbnail du plugin
                    }
                    echo wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' )[0];

                    echo "<pre>";
                    // var_dump($product->get_variation_attributes());
                    var_dump($product);
                    echo "</pre>";
                }
            } else {

                ?><div class="notice notice-error inline"><p>Vous devez avoir installé et activé le plugin WooCommerce pour utiliser ce plugin</p></div><?php

            }
        } else {

            ?><div class="notice notice-error inline"><p>Le nom de l'attribut que vous avez rentré ne correspond à aucun attribut</p></div><?php

        }
    }
}
submit_button('Enregistrer', 'primary', 'submit', true);?>
    </form>
    <h1>Aperçu</h1>
    <?=do_shortcode("[jzs_homeplayer]")?>
</div>