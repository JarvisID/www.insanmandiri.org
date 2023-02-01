<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb;
$galleryResults = $wpdb->get_results( "SELECT * FROM $wpdb->easyGalleries" );

ob_start();
?>
<div class='wrap wp-easy-gallery-admin'>
	<h2>WP Easy Gallery</h2>
    <div style="width: 50%; float: left;"><h1 class="wp-heading-inline"><?php _e('Galleries', 'wp-easy-gallery'); ?></h1><a href="<?php echo str_replace( '%7E', '~', '?page=wpeg-add-gallery'); ?>" class="page-title-action">Add New</a></div>
    <p style="float: right;"><a href="https://plugingarden.com/wordpress-gallery-plugin/?src=wpeg" target="_blank"><strong><em><?php _e('Upgrade to WP Easy Gallery Pro', 'wp-easy-gallery'); ?></em></strong></a></p>
    <div style="Clear: both;"></div>
    <table class="widefat post fixed eg-table">
    	<thead>
        <tr>
        	<th><?php _e('Gallery Name', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Gallery Shortcode', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Description', 'wp-easy-gallery'); ?></th>
            <th class="eg-cell-spacer-136"></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
        	<th><?php _e('Gallery Name', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Gallery Shortcode', 'wp-easy-gallery'); ?></th>
            <th><?php _e('Description', 'wp-easy-gallery'); ?></th>
            <th></th>
        </tr>
        </tfoot>
        <tbody>
        	<?php foreach($galleryResults as $gallery) { ?>				
            <tr id="gallery-row-<?php echo esc_attr($gallery->Id); ?>">
            	<td><p><a href="<?php echo str_replace( '%7E', '~', '?page=wpeg-edit-gallery&id='.$gallery->Id); ?>"><strong><?php echo esc_html($gallery->name); ?></strong></p>
				<div class="row-actions">
					<a href="<?php echo str_replace( '%7E', '~', '?page=wpeg-edit-gallery&id='.$gallery->Id); ?>"><?php _e('Edit', 'wp-easy-gallery'); ?></a> | 
                    <a class="button-link-delete btn-wp-easy-gallery-delete" data-gallery-id="<?php _e($gallery->Id); ?>"><?php _e('Delete Gallery', 'wp-easy-gallery'); ?></a>
					</div>
				</td>
                <td><input type="text" size="30" value="[EasyGallery key='<?php echo esc_attr($gallery->Id); ?>']" /></td>
                <td><?php echo esc_html($gallery->description); ?></td>
                <td class="major-publishing-actions">
                </td>
            </tr>
			<?php } ?>
        </tbody>
     </table>
     <br />
     <h3><?php _e('Options', 'wp-easy-gallery'); ?></h3>
     <p>Go to: <a href="?page=wpeg-settings-page">Settings</a> page.</p>
     <hr />     
     <br />
<div style="float: left; width: 60%; min-width: 488px;">     
<?php include('includes/banners.php'); ?>
</div>
<div id="rss" style="float: right; width: 25%; height: 700px; padding: 10px; min-width: 165px;">
</div>

</div>
<div id="wp-easy-gallery-update-status"><div id="loading-image-wrap"><img src="<?php echo WP_PLUGIN_URL; ?>/wp-easy-gallery/images/loading_spinner.gif" width="75" height="75" /></div></div>
<?php ob_end_flush(); ?>