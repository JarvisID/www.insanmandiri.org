<?php

/**
 * Video Player: Iframe Embed Code.
 *
 * @link     https://plugins360.com
 * @since    1.6.0
 *
 * @package All_In_One_Video_Gallery
 */
 
$player_html = '';
$type = '';
$embed_url = '';

if ( ! empty( $post_meta ) ) {
    $type = $post_meta['type'][0];

    if ( 'embedcode' == $type ) {        
        $document = new DOMDocument();
        @$document->loadHTML( $post_meta['embedcode'][0] );
        
        $iframes = $document->getElementsByTagName( 'iframe' ); 

        if ( $iframes->length > 0 ) {
            if ( $iframes->item(0)->hasAttribute( 'src' ) ) {
                $embed_url = $iframes->item(0)->getAttribute( 'src' );
            }
        } else {
            $player_html = $post_meta['embedcode'][0];
        }
	}	
}

if ( 'embedcode' != $type ) {
    foreach ( $embedded_sources as $source ) {
        $is_src_found = 0;

        if ( ! empty( $post_meta ) ) {
			if ( $source == $type ) {
                $is_src_found = 1;
                $embed_url = $post_meta[ $type ][0];
			}			
		} elseif ( isset( $_GET[ $source ] ) ) {
            $is_src_found = 1;
            $embed_url = urldecode( $_GET[ $source ] );
        }
        
        if ( $is_src_found ) {            
            switch ( $source ) {
                case 'youtube':
                    parse_str( $embed_url, $parsed_url );
                    
                    $embed_url = 'https://www.youtube.com/embed/' . aiovg_get_youtube_id_from_url( $embed_url ) . '?showinfo=0&rel=0&iv_load_policy=3';	                    
                    if ( isset( $parsed_url['start'] ) ) {
                        $embed_url = add_query_arg( 'start', $parsed_url['start'], $embed_url );
                    }				
                    break;
                case 'vimeo':
                    $oembed = aiovg_get_vimeo_oembed_data( $embed_url );
                    $embed_url = 'https://player.vimeo.com/video/' . $oembed['video_id'] . '?title=0&byline=0&portrait=0';
                    break;				
                case 'dailymotion':
                    $embed_url = 'https://www.dailymotion.com/embed/video/' . aiovg_get_dailymotion_id_from_url( $embed_url ) . '?queue-autoplay-next=0&queue-enable=0&sharing-enable=0&ui-logo=0&ui-start-screen-info=0';
                    break;
                case 'facebook':
                    $embed_url = 'https://www.facebook.com/plugins/video.php?href=' . urlencode( $embed_url ) . '&width=560&height=315&show_text=false&appId';
                    break;
            }
    
            $features = array( 'playpause', 'current', 'progress', 'duration', 'volume', 'fullscreen' );
            $controls = array();
            
            foreach ( $features as $feature ) {	
                if ( isset( $_GET[ $feature ] ) ) {	
                    if ( 1 == (int) $_GET[ $feature ] ) {
                        $controls[] = $feature;
                    }		
                } else {	
                    if ( isset( $player_settings['controls'][ $feature ] ) ) {
                        $controls[] = $feature;
                    }		
                }	
            }
    
            if ( empty( $controls ) ) {
                $embed_url = add_query_arg( 'controls', 0, $embed_url );
            } else {
                if ( ! in_array( 'fullscreen', $controls ) ) {
                    $embed_url = add_query_arg( 'fs', 0, $embed_url );
                }
            }
    
            $autoplay = isset( $_GET[ 'autoplay' ] ) ? $_GET['autoplay'] : $player_settings['autoplay'];
            $embed_url = add_query_arg( 'autoplay', (int) $autoplay, $embed_url );
    
            $loop = isset( $_GET[ 'loop' ] ) ? $_GET['loop'] : $player_settings['loop'];
            $embed_url = add_query_arg( 'loop', (int) $loop, $embed_url );

            $muted = isset( $_GET[ 'muted' ] ) ? $_GET['muted'] : $player_settings['muted'];
            $embed_url = add_query_arg( 'muted', (int) $muted, $embed_url );
        }
    }
}

if ( ! empty( $embed_url ) ) {
    $player_html = sprintf(
        '<iframe width="560" height="315" src="%s" frameborder="0" scrolling="no" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
        esc_url_raw( $embed_url )
    );
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
          
    <?php if ( $post_id > 0 ) : ?>    
        <title><?php echo wp_kses_post( get_the_title( $post_id ) ); ?></title>    
        <link rel="canonical" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" />
    <?php endif; ?>
    
	<style type="text/css">
        html, 
        body, 
        iframe {
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important; 
            padding: 0 !important; 
            overflow: hidden;
        }
    </style>
</head>
<body>    
    <?php echo $player_html; ?>

    <?php if ( 'aiovg_videos' == $post_type ) : ?>
        <script type="text/javascript">
            /**
            * Update video views count.
            *
            * @since 1.6.5
            */
            function ajaxSubmit() {
                var xmlhttp;

                if ( window.XMLHttpRequest ) {
                    xmlhttp = new XMLHttpRequest();
                } else {
                    xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
                };
                
                xmlhttp.onreadystatechange = function() {				
                    if ( 4 == xmlhttp.readyState && 200 == xmlhttp.status ) {					
                        if ( xmlhttp.responseText ) {
                            // Do nothing
                        }						
                    }					
                };	

                xmlhttp.open( 'GET', '<?php echo admin_url( 'admin-ajax.php' ); ?>?action=aiovg_update_views_count&post_id=<?php echo $post_id; ?>&security=<?php echo wp_create_nonce( "aiovg_video_{$post_id}_views_nonce" ); ?>', true );
                xmlhttp.send();							
            }

            ajaxSubmit();		
        </script>
    <?php endif; ?>
</body>
</html>