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
