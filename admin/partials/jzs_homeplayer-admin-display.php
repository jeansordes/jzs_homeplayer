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

$pluginName = $this->plugin_name;
$options = get_option($pluginName);
?><div class="wrap">
    <h2><?=esc_html(get_admin_page_title())?></h2>
    <form method="post" action="options.php">
        <?php settings_fields($pluginName)?>
        <!-- taxonomy color name -->
        <p><label>Nom de l'attribut pour la couleur<br />
            <input type='text' name='<?=$pluginName?>[colorAttrName]' placeholder='color' value='<?=(empty($options["colorAttrName"]) ? "" : $options["colorAttrName"])?>'/></label>
        </p><?php
if (!empty($options["colorAttrName"])) {
    $products = wc_get_products(['status' => 'publish', 'limit' => -1]);
    if (empty($products)) {

        ?><div class="notice notice-error inline"><p>Il n'y a aucun produit à afficher</p></div><?php

    } else {
        // check each product to see if the taxonomy is found
        $is_taxonomy_real = false;
        for ($i = 0;!$is_taxonomy_real && $i < count($products); $i++) {
            if (array_key_exists('pa_' . $options["colorAttrName"], $products[$i]->get_attributes())) {
                $is_taxonomy_real = true;
            }
        }
        if ($is_taxonomy_real) {
            // if WooCommerce is installed
            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                foreach ($products as $product) {
                    $slug = $product->get_slug();
                    ?>
                    <div class="postbox">
                        <div class="hndle">
                            <label>
                                <input class="jzs-settings-checkbox" type='checkbox' name="<?=$pluginName . "[products][" . $slug . "][mustDisplay]"?>" value='1' <?php empty($options["products"][$slug]["mustDisplay"]) ? null : checked($options["products"][$slug]["mustDisplay"], 1)?> />
                                <span>Afficher "<strong><?=$product->get_name()?></strong>" sur le player</span>
                            </label>
                            <!-- <a href="<?=$product->get_permalink()?>">Voir le produit</a> -->
                        </div>
                        <div class="inside<?=empty($options["products"][$slug]["mustDisplay"]) ? ' hidden' : ''?>">
                            <?php

                    /* <p>
                    <h3>Infos produit</h3>
                    Slug du produit <strong><?=$slug?></strong><br />
                    Etat des stocks <strong><?=$product->get_stock_status()?></strong><br />
                    <!-- instock | outofstock | inbackorder -->
                    <!-- <?=$product->get_price()?>€<br> -->
                    </p> */

                    // check if there is color(s)
                    if (array_key_exists('pa_' . $options["colorAttrName"], $product->get_attributes())) {
                        $product_colors = $product->get_attributes()['pa_' . $options["colorAttrName"]]->get_options();
                        $terms = get_terms([
                            'taxonomy' => 'pa_' . $options["colorAttrName"],
                            'hide_empty' => false,
                        ]);
                    } else {
                        $product_colors = false;
                    }

                    /*
                    [pluginName][products] => [
                    - - [slug1] => [
                    - - - - [variations] => [
                    - - - - - - [0] => [
                    - - - - - - - - [color] => #fff,
                    - - - - - - - - [bgImgURL] => .png,
                    - - - - - - - - [videoURL] => .mp4
                    - - - - - - ],
                    - - - - - - [1] => ...
                    - - - - ],
                    - - - - [permalink] => http,
                    - - - - [htmlOverlay] => <p>yo</p>
                    - - ], [slug2] => ...
                    ]
                     */

                    // do things for each variation
                    $variation_prefix = $pluginName . '[products][' . $slug . '][variations]';
                    $i = 0;
                    for ($j = 0; $j < count($terms); $j++) {
                        $term = $terms[$j];
                        if (empty($product_colors) || in_array($term->term_id, $product_colors)) {
                            echo "<p>";
                            if (empty($product_colors)) {
                                echo "<em>Aucune variation</em>";
                                $j = count($terms);
                            } else {
                                $color_hex = get_term_meta($term->term_id)["color"][0];

                                ?><span class='product-variation' style='background:<?=$color_hex?>'></span> Variation "<?=$term->name?>"

                                <input type="hidden" name="<?=$variation_prefix?>[<?=$i?>][color]" value="<?=$color_hex?>"></p><?php

                            }
                            echo "</p>";
                            ?>
                                <p>
                                    URL de la vidéo à afficher<br><input type="text" name="<?=$variation_prefix?>[<?=$i?>][videoURL]" value="<?=empty($options["products"][$slug]["variations"][$i]["videoURL"]) ? '' : $options["products"][$slug]["variations"][$i]["videoURL"]?>">
                                </p><p>

                                    URL de l'image de backup<br><input type="text" name="<?=$variation_prefix?>[<?=$i?>][bgImgURL]" value="<?=empty($options["products"][$slug]["variations"][$i]["bgImgURL"]) ?
                            wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail')[0] : $options["products"][$slug]["variations"][$i]["bgImgURL"]?>" >
                                </p><hr><?php

                            echo "<pre>";
                            // var_dump($product->get_attributes()['pa_' . $options["colorAttrName"]]->get_options());
                            // var_dump($terms);
                            echo "</pre>";
                            $i++;
                        }
                    }

                    // [products][slug][permalink] => http
                     ?><input type='hidden' name='<?=$pluginName?>[products][<?=$slug?>][permalink]' value='<?=$product->get_permalink()?>'><?php

                    // [products][slug][htmlOverlay] => <p>yo</p>
                    ?><p>HTML en overlay au dessus du produit<br><textarea name="<?=$pluginName?>[products][<?=$slug?>][htmlOverlay]" cols="30" rows="10"><?=empty($options["products"][$slug]["htmlOverlay"]) ? '' : $options["products"][$slug]["htmlOverlay"]?></textarea></p><?php

                    echo "<pre>";
                    // var_dump($product->get_variation_attributes());
                    // var_dump($product);
                    // var_dump($product->get_attributes()['pa_' . $options["colorAttrName"]]->get_options());
                    echo "</pre>";

                    ?>
                        </div>
                    </div><?php

                }
            } else {
                ?><div class="notice notice-error inline"><p>Vous devez avoir installé et activé le plugin WooCommerce pour utiliser ce plugin</p></div><?php

            }
        } else {
            ?><div class="notice notice-error inline"><p>Le nom de l'attribut que vous avez rentré ne correspond à aucun attribut. Assurez vous d'avoir créé l'attribut avec le plugin <a href="https://wordpress.org/plugins/variation-swatches-for-woocommerce/" target="_blank">WooCommerce Variation Swatches</a> et d'avoir assigné cet attribut à au moins 1 produit de la boutique</p></div><?php

        }
    }
}
submit_button('Enregistrer', 'primary', 'submit', true);?>
    </form><?php
// if (false) {
//     echo '<h1>Outils de debugging</h1><pre><code>';
//     var_dump($options);
//     echo '</code></pre>';
// }
?><h1>Aperçu</h1>
    <link rel="stylesheet" href="/wp-content/plugins/jzs_homeplayer/public/css/jzs_homeplayer-public.css">
    <link rel="stylesheet" href="/wp-content/plugins/jzs_homeplayer/public/css/jzs_player-public.css">
    <link rel="stylesheet" href="/wp-content/plugins/jzs_homeplayer/public/css/jzs_rainbow_btns-public.css">
    <script src="/wp-content/plugins/jzs_homeplayer/public/js/jzs_homeplayer-public.js"></script>
    <?=do_shortcode("[jzs_homeplayer]")?>
</div>