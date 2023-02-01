<?php

/**
 * Videos: "Video Sources" meta box.
 *
 * @link    https://plugins360.com
 * @since   1.0.0
 *
 * @package All_In_One_Video_Gallery
 */
?>

<table class="aiovg-table widefat">
  	<tbody>
    	<tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Source Type', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>        
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Source Type', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				  
				<select name="type" id="aiovg-video-type" class="select">
                	<?php 
					$types = aiovg_get_video_source_types( true );
					foreach ( $types as $key => $label ) {
						printf( '<option value="%s"%s>%s</option>', $key, selected( $key, $type, false ), $label );
					}
					?>
        		</select>
      		</td>
    	</tr>
    	<tr class="aiovg-toggle-fields aiovg-type-default">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Video', 'all-in-one-video-gallery' ); ?></label>
				<div class="aiovg-text-muted">(mp4, m4v, mov)</div>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Video', 'all-in-one-video-gallery' ); ?></strong> <span class="aiovg-text-muted">(mp4, m4v, mov)</span>
				</p>
				
				<div class="aiovg-input-wrap aiovg-media-uploader">                                                
					<input type="text" name="mp4" id="aiovg-mp4" class="text" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $mp4 ); ?>" />
					<div class="aiovg-upload-media hide-if-no-js">
						<a href="javascript:;" id="aiovg-upload-mp4" class="button button-secondary" data-format="mp4">
							<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
						</a>
					</div>
				</div> 

				<br />

        		<ul class="aiovg-checkbox horizontal">
          			<li>
                    	<label>
                        	<input type="checkbox" name="has_webm" id="aiovg-has-webm" value="1" <?php checked( $has_webm, 1 ); ?> />
							<?php esc_html_e( 'WebM', 'all-in-one-video-gallery' ); ?>
                        </label>
                    </li>
          			<li>
                    	<label>
                        	<input type="checkbox" name="has_ogv" id="aiovg-has-ogv" value="1" <?php checked( $has_ogv, 1 ); ?> />
							<?php esc_html_e( 'OGV', 'all-in-one-video-gallery' ); ?>
                       	</label>
                	</li>
        		</ul>
      		</td>
    	</tr>
    	<tr id="aiovg-field-webm" class="aiovg-toggle-fields">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'WebM', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'WebM', 'all-in-one-video-gallery' ); ?></strong>
				</p>

				<div class="aiovg-input-wrap aiovg-media-uploader">                                                
					<input type="text" name="webm" id="aiovg-webm" class="text" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $webm ); ?>" />
					<div class="aiovg-upload-media hide-if-no-js">
						<a href="javascript:;" id="aiovg-upload-webm" class="button button-secondary" data-format="webm">
							<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
						</a>
					</div>
				</div>
      		</td>
    	</tr>  
    	<tr id="aiovg-field-ogv" class="aiovg-toggle-fields">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'OGV', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'OGV', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				  
				<div class="aiovg-input-wrap aiovg-media-uploader">                                                
					<input type="text" name="ogv" id="aiovg-ogv" class="text" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $ogv ); ?>" />
					<div class="aiovg-upload-media hide-if-no-js">
						<a href="javascript:;" id="aiovg-upload-ogv" class="button button-secondary" data-format="ogv">
							<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
						</a>
					</div>
				</div> 
      		</td>
    	</tr>  
    	<tr class="aiovg-toggle-fields aiovg-type-youtube">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'YouTube', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'YouTube', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				  
				<div class="aiovg-input-wrap">
          			<input type="text" name="youtube" id="aiovg-youtube" class="text" placeholder="<?php printf( '%s: https://www.youtube.com/watch?v=twYp6W6vt2U', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $youtube ); ?>" />
				</div>
      		</td>
    	</tr>
    	<tr class="aiovg-toggle-fields aiovg-type-vimeo">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Vimeo', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Vimeo', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				  
				<div class="aiovg-input-wrap">
          			<input type="text" name="vimeo" id="aiovg-vimeo" class="text" placeholder="<?php printf( '%s: https://vimeo.com/108018156', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $vimeo ); ?>" />
				</div>
      		</td>
    	</tr>
        <tr class="aiovg-toggle-fields aiovg-type-dailymotion">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Dailymotion', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Dailymotion', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				  
				<div class="aiovg-input-wrap">
          			<input type="text" name="dailymotion" id="aiovg-dailymotion" class="text" placeholder="<?php printf( '%s: https://www.dailymotion.com/video/x11prnt', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $dailymotion ); ?>" />
				</div>
      		</td>
    	</tr>
        <tr class="aiovg-toggle-fields aiovg-type-facebook">
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Facebook', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Facebook', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				  
				<div class="aiovg-input-wrap">
          			<input type="text" name="facebook" id="aiovg-facebook" class="text" placeholder="<?php printf( '%s: https://www.facebook.com/facebook/videos/10155278547321729', esc_attr__( 'Example', 'all-in-one-video-gallery' ) ); ?>" value="<?php echo esc_url( $facebook ); ?>" />
				</div>
      		</td>
    	</tr>
        <tr class="aiovg-toggle-fields aiovg-type-embedcode">
            <td class="label aiovg-hidden-xs">
                <label><?php esc_html_e( 'Embed Code', 'all-in-one-video-gallery' ); ?></label>
            </td>
            <td>
                <p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Embed Code', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				
				<textarea name="embedcode" id="aiovg-embedcode" class="textarea" rows="6" placeholder="<?php esc_attr_e( 'Enter your Iframe Embed Code', 'all-in-one-video-gallery' ); ?>"><?php echo esc_textarea( $embedcode ); ?></textarea>

				<p>
					<?php
					printf(
						'<span class="aiovg-text-error"><strong>%s</strong></span>: %s',
						esc_html__( 'Warning', 'all-in-one-video-gallery' ),
						esc_html__( 'This field allows "iframe" and "script" tags. So, make sure the code you\'re adding with this field is harmless to your website.', 'all-in-one-video-gallery' )
					);
					?>
				</p>
            </td>
        </tr>
        <?php do_action( 'aiovg_admin_add_video_source_fields', $post->ID ); ?>
   	 	<tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Image', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Image', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				
				<div id="aiovg-image-uploader" class="aiovg-input-wrap aiovg-media-uploader">                                                
					<input type="text" name="image" id="aiovg-image" class="text" placeholder="<?php esc_attr_e( 'Enter your direct file URL (OR) upload your file using the button here', 'all-in-one-video-gallery' ); ?> &rarr;" value="<?php echo esc_attr( $image ); ?>" />
					<div class="aiovg-upload-media hide-if-no-js">
						<a href="javascript:;" id="aiovg-upload-image" class="button button-secondary" data-format="image">
							<?php esc_html_e( 'Upload File', 'all-in-one-video-gallery' ); ?>
						</a>
					</div>
				</div>

				<?php do_action( 'aiovg_admin_after_image_field' ); ?> 
      		</td>
    	</tr> 
    	<tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Duration', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Duration', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				  
				<div class="aiovg-input-wrap">
          			<input type="text" name="duration" id="aiovg-duration" class="text" placeholder="6:30" value="<?php echo esc_attr( $duration ); ?>" />
       			</div>
      		</td>
    	</tr>
    	<tr>
      		<td class="label aiovg-hidden-xs">
        		<label><?php esc_html_e( 'Views', 'all-in-one-video-gallery' ); ?></label>
      		</td>
      		<td>
        		<p class="aiovg-hidden-sm aiovg-hidden-md aiovg-hidden-lg">
					<strong><?php esc_html_e( 'Views', 'all-in-one-video-gallery' ); ?></strong>
				</p>
				  
				<div class="aiovg-input-wrap">
          			<input type="text" name="views" id="aiovg-views" class="text" value="<?php echo esc_attr( $views ); ?>" />
       			</div>
      		</td>
    	</tr>     
  	</tbody>
</table>

<?php
// Add a nonce field
wp_nonce_field( 'aiovg_save_video_sources', 'aiovg_video_sources_nonce' );