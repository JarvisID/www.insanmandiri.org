<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

$default_options = get_option('wp_easy_gallery_defaults');
$allowed_html = array(
    'a' => array(
        'href' => array(),
        'target' => array()
    ),
    'br' => array(),
    'em' => array(),
    'strong' => array(),
);
ob_start();
?>
<div class='wrap wp-easy-gallery-admin'>
	<h2>WP Easy Gallery</h2>
    <div style="width: 50%; float: left;"><h1 class="wp-heading-inline"><?php _e('Options', 'wp-easy-gallery'); ?></h1></div>
    <p style="float: right;"><a href="https://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em><?php _e('Upgrade to WP Easy Gallery Pro', 'wp-easy-gallery'); ?></em></strong></a></p>
    <div style="Clear: both;"></div>
    <form name="save_default_settings" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <?php wp_nonce_field('wpeg_settings','wpeg_settings'); ?>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th><?php _e('Property or Attribute', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Value', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Description', 'wp-easy-gallery'); ?></th>
        </tr>
        </thead>        
        <tbody>
			<tr>            	
            	<td><?php _e('Display Mode', 'wp-easy-gallery'); ?></td>
                <td>
					<select id="display_mode" name="display_mode" id="display_mode">
						<option value="wp_easy_gallery"<?php echo esc_attr(($default_options['display_mode'] == 'wp_easy_gallery') ? " selected" : ""); ?>>WP Easy Gallery</option>
						<option value="wp_default"<?php echo esc_attr(($default_options['display_mode'] == 'wp_default') ? " selected" : ""); ?>>WordPress Default</option>
						<option value="wp_slider"<?php echo esc_attr(($default_options['display_mode'] == 'wp_slider') ? " selected" : ""); ?>>Image Slider Style</option>
						<option value="" disabled>Masonry Gallery - Pro version</option>
					</select>
				</td>
                <td>Set the display mode for WP Easy Gallery.<br /><a href="https://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em>Upgrade to WP Easy Gallery Pro for more gallery options</em></strong></a></td>            
            </tr>
			<tr id="num_columns_wrap" style="display: none;">
            	<td><?php _e('Number of Columns', 'wp-easy-gallery'); ?></td>
                <td><input type="number" name="num_columns" id="num_columns" value="<?php echo esc_attr($default_options['num_columns']); ?>" /></td>
                <td><?php _e('This is the number of columns per row (for default WordPress gallery view).', 'wp-easy-gallery'); ?></td>
            </tr>
			<tr>            	
            	<td><?php _e('Show Gallery Name', 'wp-easy-gallery'); ?></td>
                <td><input type="checkbox" name="show_gallery_name" id="show_gallery_name"<?php echo esc_attr(($default_options['show_gallery_name'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td><?php _e('Show or Hide gallery name.', 'wp-easy-gallery'); ?></td>            
            </tr>
			<tr>            	
            	<td><?php _e('Gallery Name Alignment', 'wp-easy-gallery'); ?></td>
                <td>
					<select id="gallery_name_alignment" name="gallery_name_alignment">
						<option value="left"<?php echo esc_attr(($default_options['gallery_name_alignment'] == 'left') ? " selected" : ""); ?>><?php _e('Left', 'wp-easy-gallery'); ?></option>
						<option value="center"<?php echo esc_attr(($default_options['gallery_name_alignment'] == 'center') ? " selected" : ""); ?>><?php _e('Center', 'wp-easy-gallery'); ?></option>
						<option value="right"<?php echo esc_attr(($default_options['gallery_name_alignment'] == 'right') ? " selected" : ""); ?>><?php _e('Right', 'wp-easy-gallery'); ?></option>
					</select>
				</td>
                <td><?php _e('Set the text alignment of the gallery name display.', 'wp-easy-gallery'); ?></td>            
            </tr>
			<tr class="wp_easy_gallery_settings_wrap">
				<td>Gallery Modal Theme - <strong>Pro version only</strong></td>
				<td>
					<select id="gallery_theme" name="gallery_theme">
						<option value="light_rounded"<?php _e(($default_options['gallery_theme'] == 'light_rounded') ? " selected" : ""); ?>><?php _e('Light Rounded', 'wp-easy-gallery'); ?></option>
						<option value="dark_rounded" disabled><?php _e('Dark Rounded', 'wp-easy-gallery'); ?></option>
						<option value="light_square" disabled><?php _e('Light Square', 'wp-easy-gallery'); ?></option>						
						<option value="dark_square" disabled><?php _e('Dark Square', 'wp-easy-gallery'); ?></option>
						<option value="facebook" disabled><?php _e('Facebook', 'wp-easy-gallery'); ?></option>
						<option value="default" disabled><?php _e('Default', 'wp-easy-gallery'); ?></option>
					</select>
				</td>
				<td><?php printf( wp_kses( __('Set the theme to be used for the gallery modal window.<br /><a href="https://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em>Upgrade to WP Easy Gallery Pro to use this feature</em></strong></a>', 'wp-easy-gallery'), $allowed_html)); ?></td>
			</tr>
            <tr class="wp_easy_gallery_settings_wrap">            	
            	<td><?php _e('Hide Gallery Overlay', 'wp-easy-gallery'); ?></td>
                <td><input type="checkbox" name="hide_overlay" id="hide_overlay"<?php echo esc_attr(($default_options['hide_overlay'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td><?php _e('Show or Hide thumbnail gallery overlay in modal window popup. Check to hide the overlay.', 'wp-easy-gallery'); ?></td>            
            </tr>
            <tr class="wp_easy_gallery_settings_wrap">            	
            	<td><?php _e('Hide Gallery Social Buttons', 'wp-easy-gallery'); ?></td>
                <td><input type="checkbox" name="hide_social" id="hide_social"<?php echo esc_attr(($default_options['hide_social'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td><?php _e('Show or Hide the social sharing buttons in modal window popup. Check to hide the social sharing buttons.', 'wp-easy-gallery'); ?></td>            
            </tr>
            <tr class="wp_easy_gallery_settings_wrap">            	
            	<td><?php _e('Use Default Thumbnail Theme', 'wp-easy-gallery'); ?></td>
                <td><input type="checkbox" name="use_default_style" id="use_default_style"<?php echo esc_attr(($default_options['use_default_style'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td><?php _e('Use default thumbnail style (uncheck to disable new thumbnail CSS).', 'wp-easy-gallery'); ?></td>            
            </tr>
			<tr class="wp_easy_gallery_settings_wrap">            	
            	<td><?php _e('Thumbnail Dropshadow', 'wp-easy-gallery'); ?></td>
                <td><input type="checkbox" name="drop_shadow" id="drop_shadow"<?php echo esc_attr(($default_options['drop_shadow'] == 'true') ? "checked='checked'" : ""); ?> value="true" /></td>
                <td><?php _e('Use default thumbnail dropshadow (uncheck to disable dropshadow CSS).', 'wp-easy-gallery'); ?></td>            
            </tr>
            <tr class="wp_easy_gallery_settings_wrap">
            	<td><?php _e('Custom Thumbnail Style', 'wp-easy-gallery'); ?></td>
                <td><textarea name="custom_style" id="custom_style" rows="4" cols="40"><?php echo esc_html( $default_options['custom_style'] ); ?></textarea></td>
                <td><?php printf( wp_kses( __('This is where you would add custom styles for the gallery thumbnails.<br />(ex: border: solid 1px #cccccc; padding: 2px; margin-right: 10px;)', 'wp-easy-gallery'), $allowed_html)); ?></td>
            </tr>
            <tr>
            	<td>                
                	<input type="hidden" name="defaultSettings" value="true" />
                    <input type="submit" name="Submit" class="button-primary" id="btn-wp-easy-gallery-settings" value="<?php _e('Save', 'wp-easy-gallery'); ?>" />                
                </td>
                <td></td>
                <td></td>
            </tr>			
        </tbody>
     </table>
     <br />
<div style="float: left; width: 60%; min-width: 488px;">     
<?php include('includes/banners.php'); ?>
</div>
<div id="rss" style="float: right; width: 25%; height: 700px; padding: 10px; min-width: 165px;">
</div>
<script type="text/javascript">
jQuery(document).ready(function(){	
  if (jQuery('#display_mode').val() == "wp_default") {
				jQuery('#num_columns_wrap').show();
				jQuery('.wp_easy_gallery_settings_wrap').hide();
			}
	else if (jQuery('#display_mode').val() == "wp_slider") {
				jQuery('#num_columns_wrap').hide();
				jQuery('.wp_easy_gallery_settings_wrap').hide();
			}
			jQuery('#display_mode').on('change', function() {
				if (jQuery('#display_mode').val() == "wp_default") {
					jQuery('#num_columns_wrap').show();
					jQuery('.wp_easy_gallery_settings_wrap').hide();
				} 
				else if (jQuery('#display_mode').val() == "wp_slider") {
					jQuery('.wp_easy_gallery_settings_wrap').hide();
					jQuery('#num_columns_wrap').hide();
				}
				else {
					jQuery('#num_columns_wrap').hide();
					jQuery('.wp_easy_gallery_settings_wrap').show();
				}
			});
});
</script>
</div>
<div id="wp-easy-gallery-update-status"><div id="loading-image-wrap"><img src="<?php echo WP_PLUGIN_URL; ?>/wp-easy-gallery/images/loading_spinner.gif" width="75" height="75" /></div></div>
<?php ob_end_flush(); ?>