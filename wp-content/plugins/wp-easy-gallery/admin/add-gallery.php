<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

	ob_start();
?>
<div class='wrap wp-easy-gallery-admin'>
	<h2>WP Easy Gallery</h2>
    <div style="width: 50%; float: left;"><h1 class="wp-heading-inline"><?php _e('Add Gallery', 'wp-easy-gallery'); ?></h1></div>    
	<p style="float: right;"><a href="https://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em><?php _e('Upgrade to WP Easy Gallery Pro', 'wp-easy-gallery'); ?></em></strong></a></p>
    <div style="Clear: both;"></div>
	<div class="updated" id="wpeg-gallery-added" style="display: none;"><p><?php _e('Copy and paste this code into the page or post that you would like to display the gallery.', 'wp-easy-gallery'); ?></p>
    <p><input type="text" name="galleryCode" id="galleryCode" class="wpeg-gallery-form" value="" size="40" /></p></div>
    
    <form name="hcg_add_gallery_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="hcg_add_gallery" value="true" />
    <?php wp_nonce_field('wpeg_add_gallery','wpeg_add_gallery'); ?>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th class="eg-cell-spacer-250"><?php _e('Property or Attribute', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Value', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Description', 'wp-easy-gallery'); ?></th>
        </tr>
        </thead>
        <tbody>
        	<tr>
            	<td><?php _e('Gallery Name', 'wp-easy-gallery'); ?></td>
                <td><input type="text" size="30" name="galleryName" id="galleryName" class="wpeg-gallery-form" value="" /></td>
                <td>This name is the internal name for the gallery.<br />Please avoid non-letter characters such as &lsquo;, ", *, etc.</td>
            </tr>
            <tr>
            	<td><?php _e('Gallery Description', 'wp-easy-gallery'); ?></td>
                <td><input type="text" size="50" name="galleryDescription" id="galleryDescription" class="wpeg-gallery-form" value="" /></td>
                <td><?php _e('This description is for internal use.', 'wp-easy-gallery'); ?></td>
            </tr>
            <tr>
            	<td><?php _e('Thumbnail Imagepath (optional)', 'wp-easy-gallery'); ?></td>
                <td><input id="upload_image" type="text" size="36" name="upload_image" id="upload_image" class="wpeg-gallery-form" value="" />
					<input id="upload_image_button" onclick="wpeg_media_uploader(event, false, 'preview'); return false;" class="button-primary" type="button" value="<?php _e('Insert from Media Library', 'wp-easy-gallery'); ?>" /></td>
                <td><?php _e('This is the file path for an optional gallery thumbnail image.  If left blank first gallery image will be thumbnail.', 'wp-easy-gallery'); ?></td>
            </tr>
            <tr>
            	<td><?php _e('Thumbnail Width (optional)', 'wp-easy-gallery'); ?></td>
                <td><input type="text" size="10" name="gallerythumbwidth" id="gallerythumbwidth" class="wpeg-gallery-form" value="" /></td>
                <td><?php _e('This is the width of the gallery thumbnail image.', 'wp-easy-gallery'); ?></td>
            </tr>
            <tr>
            	<td><?php _e('Thumbnail Height (optional)', 'wp-easy-gallery'); ?></td>
                <td><input type="text" size="10" name="gallerythumbheight" id="gallerythumbheight" class="wpeg-gallery-form" value="" /></td>
                <td><?php _e('This is the height of the gallery thumbnail image.', 'wp-easy-gallery'); ?></td>
            </tr>
            <tr>
            	<td class="major-publishing-actions"><input type="submit" name="Submit" class="button-primary" id="btn-wp-easy-gallery-add-gallery" value="<?php _e('Add Gallery', 'wp-easy-gallery'); ?>" />
				<input type="button" class="button-primary" id="btn-wp-easy-gallery-add-gallery-clear" style="display:none;" value="<?php _e('Add Another Gallery', 'wp-easy-gallery'); ?>" /></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
	</table>
    </form>
<br />   
<?php include('includes/banners.php'); ?>
</div>
<div id="wp-easy-gallery-update-status"><div id="loading-image-wrap"><img src="<?php echo WP_PLUGIN_URL; ?>/wp-easy-gallery/images/loading_spinner.gif" width="75" height="75" /></div></div>
<?php ob_end_flush(); ?>