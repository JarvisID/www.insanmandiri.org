<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;
$imageResults = [];
$galleryResults = $wpdb->get_results( "SELECT * FROM $wpdb->easyGalleries" );

//Select gallery
if(isset($_POST['select_gallery']) || isset($_POST['galleryId'])) {
	if(check_admin_referer('wpeg_gallery','wpeg_gallery')) {
	  $gid = intval((isset($_POST['select_gallery'])) ? esc_sql($_POST['select_gallery']) : esc_sql($_POST['galleryId']));
	  $imageResults = $wpdb->get_results( "SELECT * FROM $wpdb->easyImages WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $wpdb->easyGalleries WHERE Id = $gid" );
	}
}

if(isset($_POST['editing_gid'])) {
	if(check_admin_referer('wpeg_gallery','wpeg_gallery')) {
	  $gid = intval(sanitize_text_field($_POST['editing_gid']));
	  $imageResults = $wpdb->get_results( "SELECT * FROM $wpdb->easyImages WHERE gid = $gid ORDER BY sortOrder ASC" );
	  $gallery = $wpdb->get_row( "SELECT * FROM $wpdb->easyGalleries WHERE Id = $gid" );
	}
}

$styles = get_option('wp_easy_gallery_defaults');
$show_overlay = ($styles['hide_overlay'] == 'true') ? 'false' : 'true';
$show_social = ($styles['hide_social'] == 'true') ? ', show_social: false' : '';
_e("<script>var wpegSettings = {gallery_theme: '".$styles['gallery_theme']."', show_overlay: ".$show_overlay.$show_social."};</script>");

ob_start();
?>

<div class='wrap wp-easy-gallery-admin'>
	<h2>WP Easy Gallery</h2>
	<?php if(!isset($_POST['select_gallery']) && !isset($_POST['galleryId']) && !isset($_POST['galleryId_add']) && !isset($_POST['editing_images'])) { ?>
    <div style="width: 50%; float: left;"><h1 class="wp-heading-inline"><?php _e('Add Images', 'wp-easy-gallery'); ?></h1></div>
	<p style="float: right;"><a href="https://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em><?php _e('Upgrade to WP Easy Gallery Pro', 'wp-easy-gallery'); ?></em></strong></a></p>
    <div style="Clear: both;"></div>
<table class="widefat post fixed wp-easy-gallery-table" id="galleryResults">
	<thead>
    	<tr>
          <th><?php _e('Gallery Name', 'wp-easy-gallery'); ?></th>
          <th><?php _e('Description', 'wp-easy-gallery'); ?></th>
          <th></th>
          <th></th>
        </tr>
    </thead>
    <tfoot>
    	<tr>
          <th><?php _e('Gallery Name', 'wp-easy-gallery'); ?></th>
          <th><?php _e('Description', 'wp-easy-gallery'); ?></th>
          <th></th>
          <th></th>
        </tr>
    </tfoot>
    <tbody>
    	<?php
			foreach($galleryResults as $gallery) {
				?>
                <tr>
                	<td><?php _e($gallery->name); ?></td>
                    <td><?php _e($gallery->description); ?></td>
                    <td></td>
                    <td>
                    	<form name="select_gallery_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
                    	<input type="hidden" name="galleryId" value="<?php _e($gallery->Id); ?>" />
                        <input type="hidden" name="galleryName" value="<?php _e($gallery->name); ?>" />
                        <?php wp_nonce_field('wpeg_gallery','wpeg_gallery'); ?>
                        <input type="submit" name="Submit" class="button-primary" value="<?php _e('Select Gallery', 'wp-easy-gallery'); ?>" />
                		</form>
                    </td>
                </tr>
		<?php } ?>        
    </tbody>
</table>
    <?php } else if(isset($_POST['select_gallery']) || isset($_POST['galleryId']) || isset($_POST['galleryId_add']) || isset($_POST['editing_images'])) { ?>    
    <h3 style="width: 50%; float: left;"><?php printf( esc_html__('Add Images to: %s', 'wp-easy-gallery'), $gallery->name); ?></h3>
    
    <p style="float: right;"><a href="https://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em><?php _e('Upgrade to WP Easy Gallery Pro', 'wp-easy-gallery'); ?></em></strong></a></p>
    <div style="clear: both;"></div>
    <br />
     <hr />
	 <form name="switch_gallery" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	 <p>
		<input id="upload_image_button" onclick="wpeg_media_uploader(event, true, <?php _e($gallery->Id); ?>); return false;" class="button-primary" type="button" value="Insert from Media Library" />
		<input type="submit" name="Submit" class="button-primary" value="<?php _e('Switch Gallery', 'wp-easy-gallery'); ?>" />
	 </p>
	 </form>
     <?php } ?> 
	 <div id="imageResults-wrap">
     <?php
	 if(count($imageResults) > 0) {
	 ?>
     
	 <form name="add_image_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
		<input type="hidden" name="galleryId_add" id="galleryId_add" value="<?php _e($gallery->Id); ?>" />
		<?php wp_nonce_field('wpeg_gallery','wpeg_gallery'); ?>
		
	</form>
     <h3><?php printf( esc_html__('Edit Images to: %s', 'wp-easy-gallery'), $gallery->name); ?></h3>
    
	<table class="widefat post fixed eg-table" id="imageResults">
    	<thead>
        <tr>
        	<th class="eg-cell-spacer-80"><?php _e('Image Preview', 'wp-easy-gallery'); ?></th>
            <th class="eg-cell-spacer-700"><?php _e('Image Info', 'wp-easy-gallery'); ?></th>
            <th></th>            
        </tr>
        </thead>        
        <tbody>
<form name="edit_image_form" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">	
<input type="hidden" name="editing_gid" id="editing_gid" value="<?php _e($gallery->Id); ?>" />
<input type="hidden" name="editing_images" value="true" />
<?php wp_nonce_field('wpeg_gallery', 'wpeg_gallery'); ?>	
        	<?php foreach($imageResults as $image) { ?>				
            <tr id="image-<?php echo esc_attr($image->Id); ?>">
            	<td>
				<a onclick="var images=['<?php echo esc_js($image->imagePath); ?>']; var titles=['<?php echo esc_js($image->title); ?>']; var descriptions=['<?php echo esc_js($image->description); ?>']; jQuery.prettyPhoto.open(images,titles,descriptions);" style="cursor: pointer;"><img src="<?php echo esc_attr($image->imagePath); ?>" width="75" alt="<?php echo esc_attr($image->title); ?>" /></a><br /><i><?php _e('Click to preview', 'wp-easy-gallery-pro'); ?></i>
                <p style="margin-top: 15px;"><a class="button-link-delete delete-image" data-imageid="<?php echo esc_attr($image->Id); ?>"><?php _e('Delete Image', 'wp-easy-gallery'); ?></a></p>
				</td>
				<td>                	
                	<input type="hidden" name="edit_gId[]" value="<?php echo esc_attr($image->gid); ?>" />
					<input type="hidden" name="edit_image[]" value="<?php echo esc_attr($image->Id); ?>" />                                        
                	<p><strong><?php _e('Image Path', 'wp-easy-gallery'); ?>:</strong> <input type="text" name="edit_imagePath[]" size="75" value="<?php echo esc_attr($image->imagePath); ?>" /></p>
                    <p><strong><?php _e('Image Title', 'wp-easy-gallery'); ?>:</strong> <input type="text" name="edit_imageTitle[]" size="75" value="<?php echo esc_attr($image->title); ?>" /></p>
                    <p><strong><?php _e('Image Description', 'wp-easy-gallery'); ?>:</strong> <input type="text" name="edit_imageDescription[]" size="75" value="<?php echo esc_attr($image->description); ?>" /></p>
                    <p><strong><?php _e('Sort Order', 'wp-easy-gallery'); ?>:</strong> <input type="number" name="edit_imageSort[]" size="4" value="<?php echo esc_attr($image->sortOrder); ?>" /></p>
                </td>
                <td></td>                
            </tr>
			<?php } ?>
        </tbody>		
     </table>
	 <p class="major-publishing-actions left-float eg-right-margin"><input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', 'wp-easy-gallery'); ?>" id="btn-wpeg-edit-images" /></p>
     </form>	 
     
	 <?php } ?>
	 </div>
	 <div style="clear:both;"></div>
     <br />   
<?php include('includes/banners.php'); ?>
</div>
<div id="wp-easy-gallery-update-status"><div id="loading-image-wrap"><img src="<?php echo WP_PLUGIN_URL; ?>/wp-easy-gallery/images/loading_spinner.gif" width="75" height="75" /></div></div>
<?php ob_end_flush(); ?>