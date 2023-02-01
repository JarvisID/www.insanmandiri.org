function wpeg_upload_image(galleryId, images) {
	var data = {
		'action': 'wpeg_add_images',						
		'galleryId' : galleryId,
		'images' : images
	};
						
	jQuery('#wp-easy-gallery-update-status').show();
					
	jQuery.post(ajaxurl, data, function(response) {	
						
	var r = JSON.parse(response);					
						
	if (r.success == "true") {
		//add image rows to UI
		if (r.newImages != null) {
			if (!jQuery('#imageResults').length)
			{
				var imgResultsTbl = jQuery("<table class='widefat post fixed wp-easy-gallery-table' id='imageResults'></table>");
				var imgResultsTblHd = jQuery("<thead><tr><th class='eg-cell-spacer-80'>Image Preview</th><th class='eg-cell-spacer-700'>Image Info</th><th></th></tr></thead>");
				var imgResultsTblBd = jQuery("<tbody></tbody>");
				
				imgResultsTbl.append(imgResultsTblHd, imgResultsTblBd).appendTo("#imageResults-wrap");
			}
			for (var i = 0; i < r.newImages.length; i++) {
				//add image row to UI
				var row = $("<tr id='image-"+r.newImages[i].id+"'></tr>");
				var col1 = $("<td><a onclick=\"var images=['"+r.newImages[i].imagePath+"']; var titles=['"+r.newImages[i].title+"']; var descriptions=['']; jQuery.prettyPhoto.open(images,titles,descriptions);\" style='cursor: pointer;'><img src='"+r.newImages[i].imagePath+"' width='75' alt='"+r.newImages[i].title+"' /></a><br />Click to preview</p></td>");
						
				var gId = "<input type='hidden' name='edit_gId[]' value='"+r.gId+"' />";
				var imageId = "<input type='hidden' name='edit_image[]' value='"+r.newImages[i].id+"' />";
				var imagePath = "<p><strong>Image Path:</strong> <input type='text' name='edit_imagePath[]' size='75' value='"+r.newImages[i].imagePath+"' /></p>";
				var imageTitle = "<p><strong>Image Title:</strong> <input type='text' name='edit_imageTitle[]' size='75' value='"+r.newImages[i].title+"' /></p>";
				var imageDescription = "<p><strong>Description:</strong> <input type='text' name='edit_imageDescription[]' size='75' value='' /></p>";
				var sortOrder = "<p><strong>Sort Order:</strong> <input type='number' name='edit_imageSort[]' size='4' value='"+r.newImages[i].sortOrder+"' /></p>";
					
				var col2 = $("<td>"+gId+imageId+imagePath+imageTitle+imageDescription+sortOrder+"</td>");
				var col3 = $("<td></td>");
				if (r.newImages[i].sortOrder == 0) {										
					row.prepend(col1,col2,col3).prependTo("#imageResults");
				} else
					row.append(col1,col2,col3).appendTo("#imageResults");
			}								
		}
	}
						
	jQuery('#wp-easy-gallery-update-status').hide();
						
	});
}

function wpeg_media_uploader(e, multiple, galleryId) {
	if ( typeof multiple == "undefined" ) {
    var multiple = false;
  }
  var custom_uploader;
  e.preventDefault();
  // If the uploader object has already been created, reopen the dialog.
  if ( custom_uploader ) {
    custom_uploader.open();
  }
  
  custom_uploader = wp.media.frames.file_frame = wp.media( {
    title: 'WP Easy Gallery',
    library: { type: 'image' },
    button: { text: 'insert' },
    multiple: multiple
  } );
  
  // When a file is selected, grab the URL and set it as the text field's value
  custom_uploader.on( 'select', function () {
    if ( multiple == false ) {
      attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
	  
	  jQuery('#upload_image').val(attachment.url);
	  jQuery('#image_title').val(attachment.title);
    }
    else {
      attachment = custom_uploader.state().get( 'selection' ).toJSON();
	  
	  var filesSelectedML = [];
    
	  for ( var image in attachment ) {
		var image_url = attachment[image].url;
		var image_title = attachment[image].title;
		filesSelectedML.push( image_url+'|'+image_title );
	  }
	  
	  wpeg_upload_image(galleryId, filesSelectedML);
    }
	
  });
  // Open the uploader dialog.
  custom_uploader.open();
}