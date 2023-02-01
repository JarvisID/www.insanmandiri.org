(function() {
    tinymce.create('tinymce.plugins.wpEasyGallery', {
        init : function(ed, url) {
			
			var t = this;
            
			ed.addButton('wpegselector', {
                title : 'WP Easy Gallery',
				text : 'WP Easy Gallery',
                cmd : 'wpegselector'				
            });
			
			ed.addCommand('wpegselector', function() {
                var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                    W = W - 80;
                    H = H - 84;
                    tb_show( 'Insert WP Easy Gallery shortcode', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=wp-easy-gallery-form' );
                				
            });
        }
        // ... Hidden code
    });
    // Register plugin
    tinymce.PluginManager.add( 'wpegbutton', tinymce.plugins.wpEasyGallery );
})();

jQuery(function(){
    // creates a form to be displayed everytime the button is clicked
    // you should achieve this using AJAX instead of direct html code like this
    var form = jQuery('<div id="wp-easy-gallery-form"><table id="wp-easy-gallery-table" class="form-table" style="text-align: left">\
         \
            \
        <tr>\
        <th><label class="title" for="wp-easy-gallery-select">WP Easy Gallery</label></th>\
            <td><select id="wp-easy-gallery-select">\
</select><br />\
        </td>\
        </tr>\</table>\
    <p class="submit">\
        <input type="button" id="wp-easy-gallery-insert" class="button-primary" value="Insert shortcode" name="submit" style=" margin: 10px 150px 50px; float:left;"/>\
    </p>\
    </div>');

    var table = form.find('table');
    form.appendTo('body').hide();
	
	var galleries;
	var galleryOptions;
	
	var data = {
			'action': 'wpeg_shortcode'
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			var result = JSON.parse(response);
			
			galleries = result.wpEasyGallery;
			for (var i = 0; i < galleries.length; i++) {
				galleryOptions += "<option value='"+galleries[i].id+"'>"+galleries[i].name+"</option>";
			}
			jQuery('#wp-easy-gallery-select').append(galleryOptions);
		});

    // handles the click event of the submit button
    jQuery('#wp-easy-gallery-insert').on('click', function(){
        // defines the options and their default values
        // again, this is not the most elegant way to do this
        // but well, this gets the job done nonetheless        

		var key = jQuery('#wp-easy-gallery-select option:selected').val();
        var shortcode = "[EasyGallery key='"+key+"']";         

        // inserts the shortcode into the active editor
        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

        // closes Thickhighlight
        tb_remove();
    });
});