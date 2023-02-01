<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;

$galleryResults = $wpdb->get_results( "SELECT * FROM $wpdb->easyGalleries " );

//Select gallery
if(isset($_GET['id'])) {
	  $gid = intval((isset($_POST['select_gallery'])) ? esc_sql($_POST['select_gallery']) : esc_sql($_GET['id']));
	  $imageResults = $wpdb->get_results( "SELECT * FROM $wpdb->easyImages WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $wpdb->easyGalleries WHERE Id = $gid" );	
}

if(isset($_POST['hcg_edit_gallery'])) {
	if(check_admin_referer('wpeg_gallery','wpeg_gallery')) {
	  $gid = intval(esc_sql($_POST['hcg_edit_gallery']));
	  $imageResults = $wpdb->get_results( "SELECT * FROM $wpdb->easyImages WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $wpdb->easyGalleries WHERE Id = $gid" );
	}
}
ob_start();
?>
<div class='wrap wp-easy-gallery-admin'>
	<h2>WP Easy Gallery</h2>       
    <div style="width: 50%; float: left;"><h1 class="wp-heading-inline"><?php printf( esc_html__('Editing Gallery: %s', 'wp-easy-gallery'), $gallery->name); ?></h1></div>
    <p style="float: right;"><a href="https://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em><?php _e('Upgrade to WP Easy Gallery Pro', 'wp-easy-gallery'); ?></em></strong></a></p>
    <div style="Clear: both;"></div>
    <form name="hcg_add_gallery_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="easy_gallery_edit" id="easy_gallery_edit" value="<?php _e($gid); ?>" />
    <?php wp_nonce_field('wpeg_gallery', 'wpeg_gallery'); ?>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th class="eg-cell-spacer-250"><?php _e('Field Name', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Entry', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Description', 'wp-easy-gallery'); ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th><?php _e('Field Name', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Entry', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Description', 'wp-easy-gallery'); ?></th>
        </tr>
        </tfoot>
        <tbody>
        	<tr>
            	<td><strong><?php _e('Enter Gallery Name', 'wp-easy-gallery'); ?>:</strong></td>
                <td><input type="text" size="30" name="galleryName" id="galleryName" value="<?php echo esc_attr($gallery->name); ?>" /></td>
                <td>This name is the internal name for the gallery.<br />Please avoid non-letter characters such as &lsquo;, ", *, etc.</td>
            </tr>
            <tr>
            	<td><strong><?php _e('Enter Gallery Description', 'wp-easy-gallery'); ?>:</strong></td>
                <td><input type="text" size="50" name="galleryDescription" id="galleryDescription" value="<?php echo esc_attr($gallery->description) ?>" /></td>
                <td><?php _e('This description is for internal use.', 'wp-easy-gallery'); ?></td>
            </tr>
            <tr>
            	<td><strong><?php _e('Enter Thumbnail Imagepath', 'wp-easy-gallery'); ?>:</strong></td>
                <td><input id="upload_image" type="text" size="36" name="upload_image" id="upload_image" value="<?php echo esc_attr($gallery->thumbnail); ?>" />
					<input id="upload_image_button" onclick="wpeg_media_uploader(event, false, 'preview'); return false;" class="button-primary" type="button" value="<?php _e('Insert from Media Library', 'wp-easy-gallery'); ?>" /></td>
                <td><?php _e('This is the file path for an optional gallery thumbnail image.  If left blank first gallery image will be thumbnail.', 'wp-easy-gallery'); ?></td>
            </tr>
            <tr>
            	<td><strong><?php _e('Enter Thumbnail Width', 'wp-easy-gallery'); ?>:</strong></td>
                <td><input type="text" size="10" name="gallerythumbwidth" id="gallerythumbwidth" value="<?php echo esc_attr($gallery->thumbwidth); ?>" /></td>
                <td><?php _e('This is the width of the gallery thumbnail image.', 'wp-easy-gallery'); ?></td>
            </tr>
            <tr>
            	<td><strong><?php _e('Enter Thumbnail Height', 'wp-easy-gallery'); ?>:</strong></td>
                <td><input type="text" size="10" name="gallerythumbheight" id="gallerythumbheight" value="<?php echo esc_attr($gallery->thumbheight); ?>" /></td>
                <td><?php _e('This is the height of the gallery thumbnail image.', 'wp-easy-gallery'); ?></td>
            </tr>
            <tr>
            	<td class="major-publishing-actions"><input type="submit" name="Submit" class="button-primary" id="btn-wp-easy-gallery-edit-gallery" value="<?php _e('Save Changes', 'wp-easy-gallery'); ?>" /></td>
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
<script>
jQuery('#toplevel_page_wpeg-admin').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu').addClass('wp-menu-open');
jQuery('#toplevel_page_wpeg-admin').children('a').removeClass('wp-not-current-submenu').addClass('wp-has-current-submenu').addClass('wp-menu-open');
</script>
<?php ob_end_flush(); ?>