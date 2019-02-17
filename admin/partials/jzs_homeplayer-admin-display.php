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

function array2inputs($arr, $parent_name = '', $exceptions = [])
{
    if (is_array($arr)) {
        foreach ($arr as $key => $value) {
            if (!in_array($key, $exceptions)) {
                array2inputs($value, $parent_name . '[' . $key . ']');
            }
        }
    } else {
        echo "<input type='hidden' name='" . $parent_name . "' value='" . $arr . "'/>\n";
    }
}
?><div class="wrap">
    <h2><?=esc_html(get_admin_page_title())?></h2>
    <form method="post" action="options.php">
        <?php settings_fields($pluginName)?>
        <!-- taxonomy color name -->
        <p><label>Nom de l'attribut pour la couleur<br />
            <input type='text' name='<?=$pluginName?>[colorAttrName]' placeholder='color' value='<?=(empty($options["colorAttrName"]) ? "" : $options["colorAttrName"])?>'/></label>
        </p>
        <p><label>Nom de l'attribut pour la taille<br />
            <input type='text' name='<?=$pluginName?>[sizeAttrName]' placeholder='size' value='<?=(empty($options["sizeAttrName"]) ? "" : $options["sizeAttrName"])?>'/></label>
        </p>
        <?php
if (!empty($options["colorAttrName"]) && !empty($options["sizeAttrName"])) {
    $products = wc_get_products(['status' => 'publish', 'limit' => -1]);
    if (empty($products)) {
        // IMPORTANT : it saves everything even if something didn't work
        array2inputs($options, $pluginName);
        ?><div class="notice notice-error inline"><p>Il n'y a aucun produit à afficher</p></div><?php

    } else {
        // check if the taxonomy is found
        $color_terms = get_terms([
            'taxonomy' => ['pa_' . $options["colorAttrName"]],
            'hide_empty' => false,
        ]);
        $size_terms = get_terms([
            'taxonomy' => ['pa_' . $options["sizeAttrName"]],
            'hide_empty' => false,
        ]);

        // echo "<pre>";
        // var_dump($size_terms);
        // echo "</pre>";

        foreach ($size_terms as $size_i => $size) {
            ?>
            <input type="hidden" name="<?=$pluginName?>[sizes][<?=$size_i?>][name]" value="<?=$size->name?>">
            <input type="hidden" name="<?=$pluginName?>[sizes][<?=$size_i?>][slug]" value="<?=$size->slug?>">
            <input type="hidden" name="<?=$pluginName?>[sizes][<?=$size_i?>][description]" value="<?=$size->description?>">
            <?php

        }

        $color_is_good = is_array($color_terms) && $color_terms[0]->taxonomy == 'pa_' . $options["colorAttrName"];
        $size_is_good = is_array($size_terms) && $size_terms[0]->taxonomy == 'pa_' . $options["sizeAttrName"];
        if ($size_is_good && $color_is_good) {
            // if WooCommerce is installed
            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                ?>
                <p><label>Nom de la collection en cours<br />
                    <input type="text" name="<?=$pluginName?>[collection]" value="<?=(empty($options["collection"]) ? "" : $options["collection"])?>">
                </p>

                <div class="postbox">
                    <div class="hndle">
                        <label>
                            <input class="jzs-settings-checkbox" style="display: none" type='checkbox'/>
                            <span><span class="dashicons dashicons-arrow-down"></span> Apercu</span>
                        </label>
                    </div>
                    <div class="inside hidden" style="background: whitesmoke; margin: 0; padding: 12px;">
                        <link rel="stylesheet" href="/wp-content/plugins/jzs_homeplayer/public/css/jzs-global.css">
                        <script src="/wp-content/plugins/jzs_homeplayer/public/js/jzs_homeplayer-public.js"></script>
                        <div id="main-header">
                            <div class="et_menu_container"></div>
                        </div>
                        <?=do_shortcode("[jzs_homeplayer]")?>
                    </div>
                </div>
                <?php

                foreach ($products as $product) {
                    $product_page_color_section = '';
                    $productID = "product_" . $product->get_ID();
                    // $productID = $product->get_slug();
                    ?>
                    <div class="postbox">
                        <div class="hndle">
                            <label>
                                <input type="hidden" name="<?=$pluginName . "[products][" . $productID . "][slug]"?>" value="<?=$product->get_slug()?>">
                                <input type='checkbox' name="<?=$pluginName . "[products][" . $productID . "][mustDisplay]"?>" value='1' <?php empty($options["products"][$productID]["mustDisplay"]) ? null : checked($options["products"][$productID]["mustDisplay"], 1)?> />
                                <span>Afficher <strong><?=$product->get_slug()?></strong> sur le player (aka "<?=$product->get_name()?>")</span>
                            </label>
                        </div>
                        <div class="inside">
                        <p>
                            <a target="_blank" class="button-secondary" href="<?=get_edit_post_link($product->get_id())?>"><span class="dashicons dashicons-edit"></span> Modifier les autres paramètres de ce produit</a>
                        </p>
                            <?php

                    /* <p>
                    <h3>Infos produit</h3>
                    Slug du produit <strong><?=$productID?></strong><br />
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
                    $variation_prefix = $pluginName . '[products][' . $productID . '][variations]';

                    $productVariations = (new WC_Product_Variable($product))->get_available_variations();
                    $i = 0;
                    for ($j = 0; $j < count($terms); $j++) {
                        $term = $terms[$j];
                        if (empty($product_colors) || in_array($term->term_id, $product_colors)) {
                            echo "<p class='admin-title'>";
                            if (empty($product_colors)) {
                                echo "<em>Aucune variation</em>";
                                ?><input type="hidden" name="<?=$variation_prefix?>[<?=$i?>][stockStatus]" value="<?=$product->get_stock_status() == "instock" ? "live" : "offline"?>"><?php

                                $j = count($terms);
                            } else {
                                $color_hex = get_term_meta($term->term_id)["color"][0];

                                /********************* */
                                /* PARTIE PAGE PRODUIT */
                                /********************* */
                                $product_page_color_section .= "<p class='admin-title'><span class='product-variation' style='background:" . $color_hex . "'></span> Variation \"" . $term->name . "\"</p>" . "<p>Nom du modèle sur les photos<br><input type='text' name='" . $variation_prefix . "[" . $i . "][model]' placeholder='Ex: Romane' value='" . (empty($options["products"][$productID]["variations"][$i]["model"]) ? '' : $options["products"][$productID]["variations"][$i]["model"]) . "'/></p>";

                                $query_images = new WP_Query(['post_type' => 'attachment',
                                    'post_mime_type' => 'image',
                                    'post_status' => 'inherit',
                                    'posts_per_page' => -1,
                                ]);
                                $tmp = 0;
                                for ($i_thumbnail = 0; $i_thumbnail < 4; $i_thumbnail++) {
                                    $product_page_color_section .= "<p><label>Miniature produit " . ($i_thumbnail + 1) . " / 4<br><select name='" . $variation_prefix . "[" . $i . "][product_thumbnails][" . $i_thumbnail . "]'>";

                                    foreach ($query_images->posts as $file_index => $image) {
                                        $product_page_color_section .= "<option value='" . wp_get_attachment_url($image->ID) . "'";
                                        if ((!empty($options["products"][$productID]["variations"][$i]["product_thumbnails"][$i_thumbnail])
                                            && $options["products"][$productID]["variations"][$i]["product_thumbnails"][$i_thumbnail] == wp_get_attachment_url($image->ID))
                                            || (empty($options["products"][$productID]["variations"][$i]["product_thumbnails"][$i_thumbnail]) && wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail')[0] == wp_get_attachment_url($image->ID))) {
                                            $product_page_color_section .= " selected";
                                            $tmp = $file_index;
                                        }
                                        $product_page_color_section .= ">" . basename(wp_get_attachment_url($image->ID)) . "</option>";
                                    }
                                    $product_page_color_section .= "</select><img src='" . wp_get_attachment_url($query_images->posts[$tmp]->ID) . "' id='" . $variation_prefix . "[" . $i . "][product_thumbnails][" . $i_thumbnail . "]preview' alt='preview' class='jzs-preview'></label></p>";
                                }

                                /********************* */
                                /*         FIN         */
                                /********************* */

                                ?><span class='product-variation' style='background:<?=$color_hex?>'></span> Variation "<?=$term->name?>"
                                <input type="hidden" name="<?=$variation_prefix?>[<?=$i?>][stockStatus]" value="<?=(!empty($productVariations[$i]["is_in_stock"]) && $productVariations[$i]["is_in_stock"]) || $product->get_stock_status() == "instock" ? "live" : "offline"?>">

                                <input type="hidden" name="<?=$variation_prefix?>[<?=$i?>][color]" value="<?=$color_hex?>"></p>
                                <input type="hidden" name="<?=$variation_prefix?>[<?=$i?>][colorSlug]" value="<?=$term->slug?>"></p><?php

                            }
                            ?>
                                <p>
                                    <label>
                                    Vidéo à afficher<br>
                                    <select name="<?=$variation_prefix?>[<?=$i?>][videoURL]">
                                        <?php

                            $query_images = new WP_Query(['post_type' => 'attachment',
                                'post_mime_type' => 'video',
                                'post_status' => 'inherit',
                                'posts_per_page' => -1,
                            ]);
                            $tmp = 0;
                            foreach ($query_images->posts as $file_index => $image) {
                                echo "<option value='" . wp_get_attachment_url($image->ID) . "'";
                                if (!empty($options["products"][$productID]["variations"][$i]["videoURL"]) && $options["products"][$productID]["variations"][$i]["videoURL"] == wp_get_attachment_url($image->ID)) {
                                    echo " selected";
                                    $tmp = $file_index;
                                }
                                echo ">" . basename(wp_get_attachment_url($image->ID)) . "</option>";
                            }
                            ?>
                                    </select>
                                    </label>
                                    <video controls muted src="<?=wp_get_attachment_url($query_images->posts[$tmp]->ID)?>" id="<?=$variation_prefix?>[<?=$i?>][videoURL]preview" alt="preview" class="jzs-preview">
                                </p><p>
                                    <label>
                                    Image de backup<br>
                                    <select name="<?=$variation_prefix?>[<?=$i?>][bgImgURL]">
                                        <?php

                            $query_images = new WP_Query(['post_type' => 'attachment',
                                'post_mime_type' => 'image',
                                'post_status' => 'inherit',
                                'posts_per_page' => -1,
                            ]);
                            $tmp = 0;
                            foreach ($query_images->posts as $file_index => $image) {
                                echo "<option value='" . wp_get_attachment_url($image->ID) . "'";
                                if ((!empty($options["products"][$productID]["variations"][$i]["bgImgURL"])
                                    && $options["products"][$productID]["variations"][$i]["bgImgURL"] == wp_get_attachment_url($image->ID))
                                    || (empty($options["products"][$productID]["variations"][$i]["bgImgURL"]) && wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'single-post-thumbnail')[0] == wp_get_attachment_url($image->ID))) {
                                    echo " selected";
                                    $tmp = $file_index;
                                }
                                echo ">" . basename(wp_get_attachment_url($image->ID)) . "</option>";
                            }
                            ?>
                                    </select>
                                    <img src="<?=wp_get_attachment_url($query_images->posts[$tmp]->ID)?>" id="<?=$variation_prefix?>[<?=$i?>][bgImgURL]preview" alt="preview" class="jzs-preview">
                                    </label>
                                </p><hr><?php

                            echo "<pre>";
                            // var_dump($product->get_attributes()['pa_' . $options["colorAttrName"]]->get_options());
                            echo "</pre>";
                            $i++;
                        }
                    }

                    // [products][slug][permalink] => http
                     ?><input type='hidden' name='<?=$pluginName?>[products][<?=$productID?>][permalink]' value='<?=$product->get_permalink()?>'><?php

                    // [products][slug][htmlOverlay] => <p>yo</p>
                    ?><p>HTML en overlay au dessus du produit<br><textarea name="<?=$pluginName?>[products][<?=$productID?>][htmlOverlay]" cols="30" rows="10"><?=empty($options["products"][$productID]["htmlOverlay"]) ? '' : $options["products"][$productID]["htmlOverlay"]?></textarea></p><?php

                    // echo "<pre>";
                    // var_dump($product->get_variation_attributes());
                    // var_dump($product);
                    // var_dump($product->get_attributes()['pa_' . $options["colorAttrName"]]->get_options());
                    // echo "</pre>";

                    /**************************** */
                    /*     PARTIE PAGE PRODUIT    */
                    /**************************** */
                    ?>
                            <hr><p class="admin-title-big">Partie page produit</p>
                            <input type="hidden" name="<?=$pluginName?>[products][<?=$productID?>][description]" value="<?=$product->get_data()["short_description"]?>">

                            </p>
                            <?=$product_page_color_section?>
                        </div>
                    </div><?php

                }
                /*
                ?><div class='notice notice-warning inline'><p><span class='dashicons dashicons-warning'></span> Pensez à revenir ici quand votre produit change pour enregistrer les nouvelles informations dans le plugin</p><p>Exemple de données qui peuvent changer : <ul style="margin-left: 2em; list-style: initial">
                <li>état des stocks</li>
                <li>slug du produit (qu'on retrouve dans l'url pour accéder au produit)</li>
                <li>nom d'un attribut</li>
                <li>valeur d'un attribut</li>
                </ul>
                Pour synchroniser le tout, venez ici et cliquez sur "Enregistrer"
                </p></div><?php
                */

            } else {
                // IMPORTANT : it saves everything even if something didn't work
                array2inputs($options, $pluginName);
                ?><div class="notice notice-error inline"><p>Vous devez avoir installé et activé le plugin WooCommerce pour utiliser ce plugin</p></div><?php

            }
        } else {
            // IMPORTANT : it saves everything even if something didn't work
            // except the faulty field
            array2inputs($options, $pluginName, [($color_is_good ? '' : 'colorAttrName'), ($size_is_good ? '' : 'sizeAttrName')]);
            ?>
            <div class="notice notice-error inline"><p>L'attribut pour <?=($color_is_good ? '' : "<u>la couleur</u>") . (!($color_is_good || $size_is_good) ? " et " : '') . ($size_is_good ? '' : "<u>la taille</u>")?> que vous avez rentré ne correspond à aucun attribut. Assurez vous d'avoir créé l'attribut avec le plugin <a href="https://wordpress.org/plugins/variation-swatches-for-woocommerce/" target="_blank">WooCommerce Variation Swatches</a></p></div><?php

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