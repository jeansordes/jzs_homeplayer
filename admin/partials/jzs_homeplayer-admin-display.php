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
        // check if the taxonomy is found
        $terms = get_terms([
            'taxonomy' => 'pa_' . $options["colorAttrName"],
            'hide_empty' => false,
        ]);
        if (is_array($terms) && $terms[0]->taxonomy == 'pa_' . $options["colorAttrName"]) {
            // if WooCommerce is installed
            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                ?><div class="notice notice-info inline"><p>Il est conseillé de ne pas selectionner trop de produits au risque de rendre le chargement de la page d'accueil très long, surtout si l'utilisateur est sur mobile</p></div>

                <div class="postbox">
                    <div class="hndle">
                        <label>
                            <input class="jzs-settings-checkbox" style="display: none" type='checkbox' checked/>
                            <span><span class="dashicons dashicons-arrow-down"></span> Apercu</span>
                        </label>
                    </div>
                    <div class="inside" style="background: whitesmoke; margin: 0; padding: 12px;">
                        <link rel="stylesheet" href="/wp-content/plugins/jzs_homeplayer/public/css/jzs_global-public.css">
                        <link rel="stylesheet" href="/wp-content/plugins/jzs_homeplayer/public/css/jzs_player-public.css">
                        <link rel="stylesheet" href="/wp-content/plugins/jzs_homeplayer/public/css/jzs_rainbow_btns-public.css">
                        <script src="/wp-content/plugins/jzs_homeplayer/public/js/jzs_homeplayer-public.js"></script>
                        <div id="main-header">
                            <div class="et_menu_container"></div>
                        </div>
                        <?=do_shortcode("[jzs_homeplayer]")?>
                    </div>
                </div>
                <?php

                foreach ($products as $product) {
                    $slug = $product->get_slug();
                    ?>
                    <div class="postbox">
                        <div class="hndle">
                            <label>
                                <input class="jzs-settings-checkbox" type='checkbox' name="<?=$pluginName . "[products][" . $slug . "][mustDisplay]"?>" value='1' <?php empty($options["products"][$slug]["mustDisplay"]) ? null : checked($options["products"][$slug]["mustDisplay"], 1)?> />
                                <span>Afficher <strong><?=$slug?></strong> sur le player (aka "<?=$product->get_name()?>")</span>
                            </label>
                        </div>
                        <div class="inside<?=empty($options["products"][$slug]["mustDisplay"]) ? ' hidden' : ''?>">
                        <p>
                            <a target="_blank" class="button-secondary" href="<?=get_edit_post_link($product->get_id())?>"><span class="dashicons dashicons-edit"></span> Modifier ce produit</a>
                            <a target="_blank" class="button-secondary" href="<?=$product->get_permalink()?>"><span class="dashicons dashicons-visibility"></span> Visualiser ce produit</a>
                        </p>
                            <?php

                    /* <p>
                    <h3>Infos produit</h3>
                    Slug du produit <strong><?=$slug?></strong><br />
                    Etat des stocks <strong><?=$product->get_stock_status()?></strong><br />
                    <!-- instock | outofstock | inbackorder -->
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
                    - - - - - - - - [videoURL] => .mp4,
                    - - - - - - - - [stockStatus] => instock|inbackorder|outofstock
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

                    $productVariations = (new WC_Product_Variable($product))->get_available_variations();
                    $i = 0;
                    for ($j = 0; $j < count($terms); $j++) {
                        $term = $terms[$j];
                        if (empty($product_colors) || in_array($term->term_id, $product_colors)) {
                            echo "<p>";
                            if (empty($product_colors)) {
                                echo "<em>Aucune variation</em>";
                                ?><input type="hidden" name="<?=$variation_prefix?>[<?=$i?>][stockStatus]" value="<?=$product->get_stock_status() == "instock" ? "live" : "offline"?>"><?php

                                $j = count($terms);
                            } else {
                                $color_hex = get_term_meta($term->term_id)["color"][0];

                                ?><span class='product-variation' style='background:<?=$color_hex?>'></span> Variation "<?=$term->name?>"
                                <input type="hidden" name="<?=$variation_prefix?>[<?=$i?>][stockStatus]" value="<?=(!empty($productVariations[$i]["is_in_stock"]) && $productVariations[$i]["is_in_stock"]) || $product->get_stock_status() == "instock" ? "live" : "offline"?>">

                                <input type="hidden" name="<?=$variation_prefix?>[<?=$i?>][color]" value="<?=$color_hex?>"></p><?php

                            }
                            echo "</p>";
                            ?>
                                <p>
                                    URL de la vidéo à afficher<br>
                                    <select name="<?=$variation_prefix?>[<?=$i?>][videoURL]">
                                        <?php

                            $query_images_args = array(
                                'post_type' => 'attachment',
                                'post_mime_type' => 'video',
                                'post_status' => 'inherit',
                                'posts_per_page' => -1,
                            );

                            $query_images = new WP_Query($query_images_args);

                            $images = array();
                            foreach ($query_images->posts as $image) {
                                echo "<option value='" . wp_get_attachment_url($image->ID) . "'";
                                if (!empty($options["products"][$slug]["variations"][$i]["videoURL"]) && $options["products"][$slug]["variations"][$i]["videoURL"] == wp_get_attachment_url($image->ID)) {
                                    echo " selected";
                                }
                                echo ">" . basename(wp_get_attachment_url($image->ID)) . "</option>";
                            }
                            ?>
                                    </select>
                                </p><p>
                                    URL de l'image de backup<br>
                                    <select name="<?=$variation_prefix?>[<?=$i?>][bgImgURL]">
                                        <?php

                            $query_images_args = array(
                                'post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'post_status' => 'inherit',
                                'posts_per_page' => -1,
                            );

                            $query_images = new WP_Query($query_images_args);

                            $images = array();
                            foreach ($query_images->posts as $image) {
                                echo "<option value='" . wp_get_attachment_url($image->ID) . "'";
                                if ((!empty($options["products"][$slug]["variations"][$i]["bgImgURL"])
                                    && $options["products"][$slug]["variations"][$i]["bgImgURL"] == wp_get_attachment_url($image->ID))
                                    || wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail')[0] == wp_get_attachment_url($image->ID)) {
                                    echo " selected";
                                }
                                echo ">" . basename(wp_get_attachment_url($image->ID)) . "</option>";
                            }
                            ?>
                                    </select>


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
                echo '<div class="notice notice-warning inline"><p><span class="dashicons dashicons-warning"></span> Pensez à revenir ici quand vous modifiez un produit pour synchroniser les informations WooCommerce avec ce plugin (en cliquant sur <code>Enregistrer</code>)</p></div>';
            } else {
                ?><div class="notice notice-error inline"><p>Vous devez avoir installé et activé le plugin WooCommerce pour utiliser ce plugin</p></div><?php

            }
        } else {
            ?><div class="notice notice-error inline"><p>Le nom de l'attribut que vous avez rentré ne correspond à aucun attribut. Assurez vous d'avoir créé l'attribut avec le plugin <a href="https://wordpress.org/plugins/variation-swatches-for-woocommerce/" target="_blank">WooCommerce Variation Swatches</a></p></div><?php

        }
    }
}
submit_button("Enregistrer", 'primary', 'submit', true);?>
    </form>
    <div class="postbox">
        <div class="hndle">
            <label>
                <input class="jzs-settings-checkbox" style="display: none" type='checkbox'/>
                <span><span class="dashicons dashicons-arrow-down"></span> Outils de debugging</span>
            </label>
        </div>
        <div class="inside hidden" style="background: whitesmoke; margin: 0; padding: 12px;">
            <pre><code><?php var_dump($options)?></code></pre>
        </div>
    </div>
</div>