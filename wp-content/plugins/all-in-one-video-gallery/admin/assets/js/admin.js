(function( $ ) {
	'use strict';

	/**
 	 * Display the media uploader.
 	 *
 	 * @since 1.0.0
 	 */
	function aiovg_render_media_uploader( $elem, form ) { 
    	var file_frame, attachment;
 
     	// If an instance of file_frame already exists, then we can open it rather than creating a new instance
    	if ( file_frame ) {
        	file_frame.open();
        	return;
    	}; 

     	// Use the wp.media library to define the settings of the media uploader
    	file_frame = wp.media.frames.file_frame = wp.media({
        	frame: 'post',
        	state: 'insert',
        	multiple: false
    	});
 
     	// Setup an event handler for what to do when a media has been selected
    	file_frame.on( 'insert', function() { 
        	// Read the JSON data returned from the media uploader
    		attachment = file_frame.state().get( 'selection' ).first().toJSON();
		
			// First, make sure that we have the URL of the media to display
    		if ( 0 > $.trim( attachment.url.length ) ) {
        		return;
    		};
		
			// Set the data
			switch ( form ) {
				case 'tracks':
					var id = $elem.closest( 'tr' ).find( '.aiovg-track-src' ).attr( 'id' );
					$( '#' + id ).val( attachment.url );
					break;
				case 'categories':
					$( '#aiovg-categories-image-id' ).val( attachment.id );
					$( '#aiovg-categories-image-wrapper' ).html( '<img src="' + attachment.url + '" />' );
				
					$( '#aiovg-categories-upload-image' ).hide();
					$( '#aiovg-categories-remove-image' ).show();
					break;
				case 'settings':
					$elem.prev( '.aiovg-url' ).val( attachment.url );
					break;
				default:					
					$elem.closest( '.aiovg-media-uploader' ).find( 'input[type=text]' ).val( attachment.url ).trigger( 'file.uploaded' );
			}; 
    	});
 
    	// Now display the actual file_frame
    	file_frame.open(); 
	};

	/**
	 *  Make tracks inside the video form sortable.
     *
	 *  @since 1.0.0
	 */
	function aiovg_sort_tracks() {		
		var $sortable_element = $( '#aiovg-tracks tbody' );
			
		if ( $sortable_element.hasClass( 'ui-sortable' ) ) {
			$sortable_element.sortable( 'destroy' );
		};
			
		$sortable_element.sortable({
			handle: '.aiovg-handle'
		});		
	};

	/**
 	 * Widget: Initiate color picker 
 	 *
 	 * @since 1.0.0
 	 */
	function aiovg_widget_color_picker( widget ) {
		if ( $.fn.wpColorPicker ) {
			widget.find( '.aiovg-color-picker-field' ).wpColorPicker( {
				change: _.throttle( function() { // For Customizer
					$( this ).trigger( 'change' );
				}, 3000 )
			});
		}
	}

	function on_aiovg_widget_update( event, widget ) {
		aiovg_widget_color_picker( widget );
	}

	/**
	 * Called when the page has loaded.
	 *
	 * @since 1.0.0
	 */
	$(function() {
			   
		// Common: Upload Files
		$( '.aiovg-upload-media' ).on( 'click', function( e ) { 
            e.preventDefault();
            aiovg_render_media_uploader( $( this ), 'default' ); 
		});
		
		// Common: Initiate color picker
		if ( $.fn.wpColorPicker ) {
			$( '.aiovg-color-picker' ).wpColorPicker();
		}

		// Common: Initialize the popup
		$( '.aiovg-modal-button' ).magnificPopup({
			type: 'inline'
		});

		// Dashboard: On shortcode type changed
		$( 'input[type=radio]', '#aiovg-shortcode-selector' ).on( 'change', function( e ) {
			var shortcode = $( 'input[type=radio]:checked', '#aiovg-shortcode-selector' ).val();

			$( '.aiovg-shortcode-form' ).hide();
			$( '.aiovg-shortcode-instructions' ).hide();

			$( '#aiovg-shortcode-form-' + shortcode ).show();
			$( '#aiovg-shortcode-instructions-' + shortcode ).show();
		}).trigger( 'change' );

		// Dashboard: Toggle between field sections
		$( document ).on( 'click', '.aiovg-shortcode-section-header', function( e ) {
			var $elem = $( this ).parent();

			if ( ! $elem.hasClass( 'aiovg-active' ) ) {
				$( this ).closest( '.aiovg-shortcode-form' )
					.find( '.aiovg-shortcode-section.aiovg-active' )
					.toggleClass( 'aiovg-active' )
					.find( '.aiovg-shortcode-controls' )
					.slideToggle();
			}			

			$elem.toggleClass( 'aiovg-active' )
				.find( '.aiovg-shortcode-controls' )
				.slideToggle();
		});		

		// Dashboard: Toggle fields based on the selected video source type
		$( 'select[name=type]', '#aiovg-shortcode-form-video' ).on( 'change', function() {			
			var type = $( this ).val();
			
			$( '#aiovg-shortcode-form-video' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-type-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-type-' + type );
		});

		// Dashboard: Toggle fields based on the selected videos template
		$( 'select[name=template]', '#aiovg-shortcode-form-videos' ).on( 'change', function() {			
			var template = $( this ).val();
			
			$( '#aiovg-shortcode-form-videos' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-template-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-template-' + template );
		});

		// Dashboard: Toggle fields based on the selected categories template
		$( 'select[name=template]', '#aiovg-shortcode-form-categories' ).on( 'change', function() {			
			var template = $( this ).val();
			
			$( '#aiovg-shortcode-form-categories' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-template-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-template-' + template );
		});

		// Dashboard: Generate shortcode
		$( '#aiovg-generate-shortcode' ).on( 'click', function( e ) { 
			e.preventDefault();			

			// Shortcode
			var shortcode = $( 'input[type=radio]:checked', '#aiovg-shortcode-selector' ).val();

			// Attributes
			var props = {};
			
			$( '.aiovg-shortcode-field', '#aiovg-shortcode-form-' + shortcode ).each(function() {							
				var $this = $( this );
				var type  = $this.attr( 'type' );
				var name  = $this.attr( 'name' );				
				var value = $this.val();
				var def   = 0;
				
				if ( 'undefined' !== typeof $this.data( 'default' ) ) {
					def = $this.data( 'default' );
				}				
				
				// type = checkbox
				if ( 'checkbox' == type ) {
					value = $this.is( ':checked' ) ? 1 : 0;
				} else {
					// name = category
					if ( 'category' == name ) {					
						value = $( 'input[type=checkbox]:checked', $this ).map(function() {
							return this.value;
						}).get().join( "," );
					}
				}				
				
				// Add only if the user input differ from the global configuration
				if ( value != def ) {
					props[ name ] = value;
				}				
			});

			var attrs = shortcode;
			for ( var name in props ) {
				if ( props.hasOwnProperty( name ) ) {
					attrs += ( ' ' + name + '="' + props[ name ] + '"' );
				}
			}

			// Shortcode output		
			$( '#aiovg-shortcode').val( '[aiovg_' + attrs + ']' ); 
		});
		
		// Dashboard: Check/Uncheck all checkboxes in the issues table list
		$( '#aiovg-issues-check-all' ).on( 'change', function( e ) {
			var value = $( this ).is( ':checked' ) ? 1 : 0;	

			if ( value ) {
				$( '.aiovg-issue', '#aiovg-issues' ).prop( 'checked', true );
			} else {
				$( '.aiovg-issue', '#aiovg-issues' ).prop( 'checked', false );
			}
		});	

		// Dashboard: Validate the issues form
		$( '#aiovg-issues-form' ).submit(function() {
			var has_input = 0;

			$( '.aiovg-issue:checked', '#aiovg-issues' ).each(function() {
				has_input = 1;
			});

			if ( ! has_input ) {
				alert( aiovg_admin.i18n.no_issues_slected );
				return false;
			}			
		});
		
		// Videos: Toggle fields based on the selected video source type
		$( '#aiovg-video-type' ).on( 'change', function( e ) { 
            e.preventDefault();
 
 			var type = $( this ).val();
			
			$( '.aiovg-toggle-fields' ).hide();
			$( '.aiovg-type-'+type ).show( 300 );
			
			if ( 'default' == type ) {
				$( '#aiovg-has-webm, #aiovg-has-ogv' ).trigger( 'change' );
			}
		}).trigger( 'change' );
		
		// Videos: Toggle WebM fields
		$( '#aiovg-has-webm' ).on( 'change', function( e ) { 
            e.preventDefault();
 
 			if ( $( this ).is( ':checked' ) ) {
				$( '#aiovg-field-webm' ).show( 300 );
			} else {
				$( '#aiovg-field-webm' ).hide( 300 );
			} 
        }).trigger( 'change' );
		
		// Videos: Toggle OGV fields
		$( '#aiovg-has-ogv' ).on( 'change', function( e ) { 
            e.preventDefault();
 
 			if ( $( this ).is( ':checked' ) ) {
				$( '#aiovg-field-ogv' ).show( 300 );
			} else {
				$( '#aiovg-field-ogv' ).hide( 300 );
			} 
        }).trigger( 'change' );
		
		// Videos: Add new subtitle fields when "Add New File" button clicked
		$( '#aiovg-add-new-track' ).on( 'click', function( e ) { 
            e.preventDefault();
			
			var id = $( '.aiovg-tracks-row', '#aiovg-tracks' ).length;
			
			var $row = $( '#aiovg-tracks-clone' ).find( 'tr' ).clone();
			$row.find( '.aiovg-track-src' ).attr( 'id', 'aiovg-track-'+id );
			
            $( '#aiovg-tracks' ).append( $row ); 
        });
		
		if ( ! $( '.aiovg-tracks-row', '#aiovg-tracks' ).length ) {
			$( '#aiovg-add-new-track' ).trigger( 'click' );
		}

		// Videos: Upload Tracks	
		$( 'body' ).on( 'click', '.aiovg-upload-track', function( e ) { 
            e.preventDefault();
            aiovg_render_media_uploader( $( this ), 'tracks' ); 
        });
		
		// Videos: Delete a subtitles fields set when "Delete" button clicked
		$( 'body' ).on( 'click', '.aiovg-delete-track', function( e ) { 
            e.preventDefault();			
            $( this ).closest( 'tr' ).remove(); 
        });
		
		// Videos: Make the subtitles fields sortable
		aiovg_sort_tracks();
		
		// Categories: Upload Image	
		$( '#aiovg-categories-upload-image' ).on( 'click', function( e ) { 
            e.preventDefault();
			aiovg_render_media_uploader( $( this ), 'categories' ); 
        });
		
		// Categories: Remove Image
		$( '#aiovg-categories-remove-image' ).on( 'click', function( e ) {														 
            e.preventDefault();
				
			$( '#aiovg-categories-image-id' ).val( '' );
			$( '#aiovg-categories-image-wrapper' ).html( '' );
			
			$( '#aiovg-categories-remove-image' ).hide();
			$( '#aiovg-categories-upload-image' ).show();	
		});
		
		// Categories: Clear the image field after a category was created
		$( document ).ajaxComplete(function( e, xhr, settings ) {			
			if ( $( "#aiovg-categories-image-id" ).length ) {				
				var queryStringArr = settings.data.split( '&' );
			   
				if ( -1 !== $.inArray( 'action=add-tag', queryStringArr ) ) {
					var xml = xhr.responseXML;
					var response = $( xml ).find( 'term_id' ).text();
					if ( '' != response ) {
						$( '#aiovg-categories-image-id' ).val( '' );
						$( '#aiovg-categories-image-wrapper' ).html( '' );
						
						$( '#aiovg-categories-remove-image' ).hide();
						$( '#aiovg-categories-upload-image' ).show();
					};
				};			
			};			
		});

		// Settings: Set Section ID
		$( '.form-table', '#aiovg-settings' ).each(function() { 
			var str = $( this ).find( 'tr:first th label' ).attr( 'for' );
			var id = str.split( '[' );
			id = id[0].replace( /_/g, '-' );

			$( this ).attr( 'id', id );
		});
		
		// Settings: Upload Files
		$( '.aiovg-browse', '#aiovg-settings' ).on( 'click', function( e ) {																	  
			e.preventDefault();			
			aiovg_render_media_uploader( $( this ), 'settings' );			
		});

		// Settings: Toggle fields based on the selected categories template
		$( 'tr.template', '#aiovg-categories-settings' ).find( 'select' ).on( 'change', function() {			
			var template = $( this ).val();
			
			$( '#aiovg-categories-settings' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-template-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-template-' + template );
		}).trigger( 'change' );

		// Settings: Toggle fields based on the selected videos template
		$( 'tr.template', '#aiovg-videos-settings' ).find( 'select' ).on( 'change', function() {			
			var template = $( this ).val();
			
			$( '#aiovg-videos-settings' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-template-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-template-' + template );
		}).trigger( 'change' );	

		// Categories Widget: Toggle fields based on the selected categories template
		$( document ).on( 'change', '.aiovg-widget-form-categories .aiovg-widget-input-template', function() {			
			var template = $( this ).val();
			
			$( this ).closest( '.aiovg-widget-form-categories' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-template-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-template-' + template );
		});

		// Videos Widget: Toggle fields based on the selected videos template
		$( document ).on( 'change', '.aiovg-widget-form-videos .aiovg-widget-input-template', function() {			
			var template = $( this ).val();
			
			$( this ).closest( '.aiovg-widget-form-videos' ).removeClass(function( index, classes ) {
				var matches = classes.match( /\aiovg-template-\S+/ig );
				return ( matches ) ? matches.join(' ') : '';	
			}).addClass( 'aiovg-template-' + template );
		});

		// Videos Widget: Initiate color picker
		$( '#widgets-right .widget:has(.aiovg-color-picker-field)' ).each(function() {
			aiovg_widget_color_picker( $( this ) );
		});

		$( document ).on( 'widget-added widget-updated', on_aiovg_widget_update );
			   
	});	

})( jQuery );
